<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVisitRequest;
use App\Models\Branch;
use App\Models\Outlet;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VisitHistoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->toDateString();
        $type = $request->string('type')->toString();
        $search = trim((string) $request->string('search'));
        $condition = $request->string('condition')->toString();

        $baseQuery = $this->scopedQuery($user)
            ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when($type !== '', fn (Builder $query) => $query->where('visit_type', $type))
            ->when($condition !== '', fn (Builder $query) => $query->where('outlet_condition', $condition))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->whereHas('outlet', function (Builder $outletQuery) use ($search) {
                        $outletQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('official_kode', 'like', "%{$search}%");
                    })->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            });

        // JSON response for mobile infinite scroll
        if ($request->expectsJson()) {
            $paginator = (clone $baseQuery)
                ->with(['branch', 'user', 'outlet', 'salesDetail', 'smdDetail'])
                ->latest('visited_at')
                ->paginate(15);

            $canEdit = $user->isAdminPusat() || $user->isSupervisor();

            return response()->json([
                'data' => $paginator->getCollection()->map(fn (Visit $visit) => $this->formatVisitForMobile($visit, $canEdit)),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        }

        // HTML response — full page load
        $visits = (clone $baseQuery)
            ->with(['branch', 'user', 'outlet', 'salesDetail', 'smdDetail', 'smdActivities', 'displayPhotos'])
            ->latest('visited_at')
            ->paginate(15)
            ->withQueryString();

        $kpi = $this->buildKpi($user, clone $baseQuery);

        $canEdit = $user->isAdminPusat() || $user->isSupervisor();
        $mobileInitialData = [
            'data' => $visits->getCollection()->map(fn (Visit $visit) => $this->formatVisitForMobile($visit, $canEdit)),
            'meta' => [
                'current_page' => $visits->currentPage(),
                'last_page' => $visits->lastPage(),
                'per_page' => $visits->perPage(),
                'total' => $visits->total(),
            ],
        ];

        $filters = compact('from', 'to', 'type', 'search', 'condition');

        return view('visit-history.index', [
            'visits' => $visits,
            'filters' => $filters,
            'kpi' => $kpi,
            'mobileInitialData' => $mobileInitialData,
            'canEdit' => $canEdit,
        ]);
    }

    public function show(Request $request, Visit $visit): View
    {
        abort_unless($this->scopedQuery($request->user())->whereKey($visit->id)->exists(), 404);

        return view('visit-history.show', [
            'visit' => $visit->load(['branch', 'user', 'outlet', 'salesDetail', 'smdDetail', 'smdActivities', 'displayPhotos']),
        ]);
    }

    public function edit(Request $request, Visit $visit): View
    {
        $user = $request->user();
        abort_unless($this->scopedQuery($user)->whereKey($visit->id)->exists(), 404);

        $visit->load(['branch', 'user', 'outlet', 'salesDetail', 'smdDetail', 'smdActivities', 'displayPhotos']);

        $outlets = Outlet::query()
            ->when(! $user->isAdminPusat(), fn (Builder $q) => $q->where('branch_id', $user->branch_id))
            ->orderBy('name')
            ->get(['id', 'name', 'official_kode', 'branch_id']);

        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('visit-history.edit', compact('visit', 'outlets', 'branches'));
    }

    public function update(UpdateVisitRequest $request, Visit $visit): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->scopedQuery($user)->whereKey($visit->id)->exists(), 404);

        DB::transaction(function () use ($request, $visit) {
            $outlet = Outlet::findOrFail($request->integer('outlet_id'));

            $visit->update([
                'outlet_id' => $outlet->id,
                'branch_id' => $outlet->branch_id,
                'visited_at' => $request->input('visited_at'),
                'outlet_condition' => $request->input('outlet_condition'),
                'notes' => $request->input('notes'),
            ]);

            if ($visit->visit_type === 'sales') {
                $visit->salesDetail()->updateOrCreate([], [
                    'order_amount' => $request->input('order_amount', 0) ?: 0,
                    'receivable_amount' => $request->input('receivable_amount', 0) ?: 0,
                ]);
            } else {
                $visit->smdDetail()->updateOrCreate([], [
                    'po_amount' => $request->input('po_amount', 0) ?: 0,
                    'payment_amount' => $request->input('payment_amount', 0) ?: 0,
                ]);

                $visit->smdActivities()->delete();

                foreach ($request->input('activities', []) as $activity) {
                    $visit->smdActivities()->create(['activity_type' => $activity]);
                }
            }
        });

        return redirect()->route('visit-history.show', $visit)->with('status', 'Kunjungan berhasil diperbarui.');
    }

    public function destroy(Request $request, Visit $visit): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->scopedQuery($user)->whereKey($visit->id)->exists(), 404);

        DB::transaction(function () use ($visit) {
            $photosToDelete = [];

            if ($visit->visit_photo_path) {
                $photosToDelete[] = $visit->visit_photo_path;
            }

            if ($visit->smdDetail?->display_photo_path) {
                $photosToDelete[] = $visit->smdDetail->display_photo_path;
            }

            foreach ($visit->displayPhotos as $photo) {
                $photosToDelete[] = $photo->photo_path;
            }

            $visit->displayPhotos()->delete();
            $visit->smdActivities()->delete();
            $visit->smdDetail()->delete();
            $visit->salesDetail()->delete();
            $visit->delete();

            foreach ($photosToDelete as $path) {
                Storage::disk('public')->delete($path);
            }
        });

        return redirect()->route('visit-history.index')->with('status', 'Kunjungan berhasil dihapus.');
    }

    private function scopedQuery($user): Builder
    {
        return Visit::query()
            ->when(! $user->isAdminPusat(), fn (Builder $query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSales() || $user->isSmd(), fn (Builder $query) => $query->where('user_id', $user->id));
    }

    /**
     * Build KPI metrics from a separate DB query (not from the paginator).
     * Returns role-appropriate metrics — no redundant data.
     */
    private function buildKpi($user, Builder $baseQuery): array
    {
        $totalVisits = (clone $baseQuery)->count();

        $isSales = $user->isSales();
        $isSmd = $user->isSmd();

        // Sales financials
        $salesAgg = null;
        if (! $isSmd) {
            $salesAgg = (clone $baseQuery)
                ->where('visits.visit_type', 'sales')
                ->leftJoin('sales_visit_details', 'sales_visit_details.visit_id', '=', 'visits.id')
                ->selectRaw('COUNT(visits.id) as cnt, COALESCE(SUM(sales_visit_details.order_amount), 0) as total_order, COALESCE(SUM(sales_visit_details.receivable_amount), 0) as total_receivable')
                ->first();
        }

        // SMD financials
        $smdAgg = null;
        if (! $isSales) {
            $smdAgg = (clone $baseQuery)
                ->where('visits.visit_type', 'smd')
                ->leftJoin('smd_visit_details', 'smd_visit_details.visit_id', '=', 'visits.id')
                ->selectRaw('COUNT(visits.id) as cnt, COALESCE(SUM(smd_visit_details.po_amount), 0) as total_po, COALESCE(SUM(smd_visit_details.payment_amount), 0) as total_payment')
                ->first();
        }

        $fmt = fn (float $v) => 'Rp '.number_format($v, 0, ',', '.');

        if ($isSales) {
            // Sales role: Total Visit, Sales Amount, Collection
            return [
                ['label' => 'Total Visit', 'value' => $totalVisits, 'color' => 'blue'],
                ['label' => 'Sales Amount', 'value' => $fmt((float) ($salesAgg->total_order ?? 0)), 'color' => 'sky'],
                ['label' => 'Collection', 'value' => $fmt((float) ($salesAgg->total_receivable ?? 0)), 'color' => 'emerald'],
            ];
        }

        if ($isSmd) {
            // SMD role: Total Visit, PO Amount, Collection
            return [
                ['label' => 'Total Visit', 'value' => $totalVisits, 'color' => 'blue'],
                ['label' => 'PO Amount', 'value' => $fmt((float) ($smdAgg->total_po ?? 0)), 'color' => 'violet'],
                ['label' => 'Collection', 'value' => $fmt((float) ($smdAgg->total_payment ?? 0)), 'color' => 'emerald'],
            ];
        }

        // Supervisor / Admin: Total Visit, Sales Visit, SMD Visit, Sales Amount, PO Amount, Collection
        $salesCount = (int) ($salesAgg->cnt ?? 0);
        $smdCount = (int) ($smdAgg->cnt ?? 0);
        $totalSalesAmount = (float) ($salesAgg->total_order ?? 0) + (float) ($smdAgg->total_po ?? 0);
        $totalCollection = (float) ($salesAgg->total_receivable ?? 0) + (float) ($smdAgg->total_payment ?? 0);

        return [
            ['label' => 'Total Visit', 'value' => $totalVisits, 'color' => 'blue'],
            ['label' => 'Sales Visit', 'value' => $salesCount, 'color' => 'sky'],
            ['label' => 'SMD Visit', 'value' => $smdCount, 'color' => 'violet'],
            ['label' => 'Sales Amount', 'value' => $fmt($totalSalesAmount), 'color' => 'sky'],
            ['label' => 'Collection', 'value' => $fmt($totalCollection), 'color' => 'emerald'],
        ];
    }

    /**
     * Format a single visit into a lean array for mobile card rendering.
     */
    private function formatVisitForMobile(Visit $visit, bool $canEdit): array
    {
        $formatted = $visit->visitedAtForBranch();

        return [
            'id' => $visit->id,
            'visited_at_formatted' => $formatted?->format('d M H:i') ?? '-',
            'user_name' => $visit->user?->name ?? '-',
            'outlet_name' => $visit->outlet?->name ?? '-',
            'visit_type' => $visit->visit_type,
            'outlet_condition' => $visit->outlet_condition,
            'sales_amount' => $visit->salesAmount(),
            'collection_amount' => $visit->collectionAmount(),
            'can_edit' => $canEdit,
            'url_show' => route('visit-history.show', $visit),
            'url_edit' => $canEdit ? route('visit-history.edit', $visit) : null,
            'url_destroy' => $canEdit ? route('visit-history.destroy', $visit) : null,
        ];
    }
}
