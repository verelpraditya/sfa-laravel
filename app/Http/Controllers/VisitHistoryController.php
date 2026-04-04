<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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

    private function scopedQuery($user): Builder
    {
        return Visit::query()
            ->when(! $user->isAdminPusat(), fn (Builder $query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSales() || $user->isSmd(), fn (Builder $query) => $query->where('user_id', $user->id));
    }
}
