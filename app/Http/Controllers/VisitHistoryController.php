<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVisitRequest;
use App\Models\Branch;
use App\Models\Outlet;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VisitHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->toDateString();
        $type = $request->string('type')->toString();
        $search = trim((string) $request->string('search'));
        $condition = $request->string('condition')->toString();

        $visits = $this->scopedQuery($user)
            ->with(['branch', 'user', 'outlet', 'salesDetail', 'smdDetail', 'smdActivities', 'displayPhotos'])
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
            })
            ->latest('visited_at')
            ->paginate(15)
            ->withQueryString();

        return view('visit-history.index', [
            'visits' => $visits,
            'filters' => compact('from', 'to', 'type', 'search', 'condition'),
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
}
