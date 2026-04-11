<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutletRequest;
use App\Models\Branch;
use App\Models\Outlet;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OutletController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('search'));
        $outletStatus = $request->string('outlet_status')->toString();

        $outlets = $this->baseQuery($user)
            ->with(['branch', 'creator', 'verifier'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_kode', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when($outletStatus !== '', fn ($query) => $query->where('outlet_status', $outletStatus))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('outlets.index', [
            'outlets' => $outlets,
            'branches' => Branch::orderBy('name')->get(),
            'filters' => [
                'search' => $search,
                'outlet_status' => $outletStatus,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->canManageOutletMaster(), 403);

        return view('outlets.create', [
            'outlet' => new Outlet,
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Outlet $outlet): View|JsonResponse
    {
        $user = $request->user();

        abort_if(! $user->isAdminPusat() && $user->branch_id !== $outlet->branch_id, 404);

        $outlet->load(['branch', 'creator', 'verifier']);

        // Visit query — scoped per user for sales/SMD
        $visitQuery = Visit::where('outlet_id', $outlet->id)
            ->when(! $user->isAdminPusat(), fn ($q) => $q->where('visits.branch_id', $user->branch_id))
            ->when($user->isSales() || $user->isSmd(), fn ($q) => $q->where('visits.user_id', $user->id));

        // JSON response for mobile infinite scroll
        if ($request->expectsJson()) {
            $paginator = (clone $visitQuery)
                ->with(['user', 'salesDetail', 'smdDetail', 'branch'])
                ->latest('visited_at')
                ->paginate(10);

            return response()->json([
                'data' => $paginator->getCollection()->map(fn (Visit $visit) => $this->formatVisitForOutlet($visit)),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        }

        // HTML response
        $visits = (clone $visitQuery)
            ->with(['user', 'salesDetail', 'smdDetail', 'branch'])
            ->latest('visited_at')
            ->paginate(10);

        // Stats — Opsi B: always show full outlet stats (all users), computed via SQL aggregates
        $stats = $this->buildOutletStats($outlet);

        $mobileInitialData = [
            'data' => $visits->getCollection()->map(fn (Visit $visit) => $this->formatVisitForOutlet($visit)),
            'meta' => [
                'current_page' => $visits->currentPage(),
                'last_page' => $visits->lastPage(),
                'per_page' => $visits->perPage(),
                'total' => $visits->total(),
            ],
        ];

        return view('outlets.show', [
            'outlet' => $outlet,
            'visits' => $visits,
            'stats' => $stats,
            'mobileInitialData' => $mobileInitialData,
        ]);
    }

    public function store(OutletRequest $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->canManageOutletMaster(), 403);

        $outlet = Outlet::create([
            ...$request->validatedPayload(),
            'branch_id' => $request->resolvedBranchId(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return redirect()->route('outlets.edit', $outlet)->with('status', 'Outlet berhasil dibuat.');
    }

    public function edit(Request $request, Outlet $outlet): View
    {
        $this->ensureUserCanAccess($request->user(), $outlet);

        return view('outlets.edit', [
            'outlet' => $outlet->load(['branch', 'creator', 'verifier']),
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }

    public function update(OutletRequest $request, Outlet $outlet): RedirectResponse
    {
        $this->ensureUserCanAccess($request->user(), $outlet);

        $outlet->update([
            ...$request->validatedPayload($outlet),
            'branch_id' => $request->resolvedBranchId($outlet),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('outlets.edit', $outlet)->with('status', 'Outlet berhasil diperbarui.');
    }

    public function destroy(Request $request, Outlet $outlet): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->canDeleteOutlets(), 403);

        if ($outlet->visits()->exists()) {
            return back()->with('error', 'Outlet tidak bisa dihapus karena sudah memiliki data kunjungan. Hapus semua kunjungan terlebih dahulu atau gunakan fitur Merge.');
        }

        DB::transaction(function () use ($outlet) {
            DB::table('outlet_status_histories')->where('outlet_id', $outlet->id)->delete();
            DB::table('outlet_verification_logs')->where('outlet_id', $outlet->id)->delete();
            $outlet->delete();
        });

        return redirect()->route('outlets.index')->with('status', 'Outlet berhasil dihapus.');
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if ($query === '') {
            return response()->json(['data' => []]);
        }

        $outlets = $this->baseQuery($request->user())
            ->select(['id', 'branch_id', 'name', 'official_kode', 'district', 'city', 'category', 'outlet_status'])
            ->where(function ($inner) use ($query) {
                $inner
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('official_kode', 'like', "%{$query}%");
            })
            ->with('branch:id,name')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $outlets->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'official_kode' => $outlet->official_kode,
                'district' => $outlet->district,
                'city' => $outlet->city,
                'category' => $outlet->category,
                'outlet_status' => $outlet->outlet_status,
                'branch' => $outlet->branch?->name,
            ]),
        ]);
    }

    public function prospects(Request $request): View
    {
        abort_unless($request->user()->canViewOperationalOutletLists(), 403);

        return $this->renderOperationalList(
            $request,
            fn ($query) => $query->where('outlet_status', 'prospek'),
            'Prospek Follow Up',
            'Daftar outlet prospek yang bisa ditindaklanjuti sales dan supervisor.',
            'prospek',
        );
    }

    public function noo(Request $request): View
    {
        abort_unless($request->user()->canVerifyOutlets(), 403);

        return $this->renderOperationalList(
            $request,
            fn ($query) => $query->where('outlet_status', 'pending'),
            'Outlet Pending Official Kode',
            'Daftar outlet pending yang masih menunggu official kode dan tindak lanjut supervisor.',
            'pending',
        );
    }

    public function inactive(Request $request): View
    {
        abort_unless($request->user()->canVerifyOutlets(), 403);

        return $this->renderOperationalList(
            $request,
            fn ($query) => $query->where('outlet_status', 'inactive'),
            'Outlet Inactive',
            'Daftar outlet yang ditandai tutup atau tidak order lagi.',
            'inactive',
        );
    }

    private function baseQuery($user)
    {
        return Outlet::query()->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id));
    }

    private function renderOperationalList(Request $request, callable $scope, string $title, string $description, string $variant): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('search'));

        $outlets = $this->baseQuery($user)
            ->with(['branch', 'creator', 'verifier', 'latestVisit.user:id,name,role', 'latestVisit.branch:id,timezone'])
            ->tap($scope)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_kode', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('outlets.operational-list', [
            'outlets' => $outlets,
            'title' => $title,
            'description' => $description,
            'variant' => $variant,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    private function ensureUserCanAccess($user, Outlet $outlet): void
    {
        abort_unless($user->canManageOutletMaster(), 403);
        abort_if(! $user->isAdminPusat() && $user->branch_id !== $outlet->branch_id, 404);
    }

    /**
     * Build outlet-level stats via SQL aggregates (always all users — Opsi B).
     */
    private function buildOutletStats(Outlet $outlet): array
    {
        $outletId = $outlet->id;

        $totalVisits = Visit::where('outlet_id', $outletId)->count();

        $salesAgg = Visit::where('outlet_id', $outletId)
            ->where('visit_type', 'sales')
            ->leftJoin('sales_visit_details', 'sales_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('COALESCE(SUM(sales_visit_details.order_amount), 0) as total_order, COALESCE(SUM(sales_visit_details.receivable_amount), 0) as total_receivable')
            ->first();

        $smdAgg = Visit::where('outlet_id', $outletId)
            ->where('visit_type', 'smd')
            ->leftJoin('smd_visit_details', 'smd_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('COALESCE(SUM(smd_visit_details.po_amount), 0) as total_po, COALESCE(SUM(smd_visit_details.payment_amount), 0) as total_payment')
            ->first();

        $totalSales = (float) ($salesAgg->total_order ?? 0) + (float) ($smdAgg->total_po ?? 0);
        $totalCollection = (float) ($salesAgg->total_receivable ?? 0) + (float) ($smdAgg->total_payment ?? 0);

        $lastVisit = Visit::where('outlet_id', $outletId)->with('branch')->latest('visited_at')->first();

        return [
            'total_visits' => $totalVisits,
            'total_sales' => $totalSales,
            'total_collection' => $totalCollection,
            'last_visit' => $lastVisit,
        ];
    }

    /**
     * Format a single visit into a lean array for mobile card rendering in outlet show.
     */
    private function formatVisitForOutlet(Visit $visit): array
    {
        $formatted = $visit->visitedAtForBranch();

        return [
            'id' => $visit->id,
            'visited_at_formatted' => $formatted?->format('d M H:i') ?? '-',
            'visited_at_full' => $formatted?->format('d M Y H:i') ?? '-',
            'user_name' => $visit->user?->name ?? '-',
            'visit_type' => $visit->visit_type,
            'sales_amount' => $visit->salesAmount(),
            'collection_amount' => $visit->collectionAmount(),
            'url_show' => route('visit-history.show', $visit),
        ];
    }
}
