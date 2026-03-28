<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Visit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $dashboardData = $this->buildDashboardData(
            $this->outletScope($user),
            $this->visitScope($user),
            'Scope aktif sekarang',
            'Data real 7 hari terakhir di scope kamu',
        );

        $supervisorBranchData = null;
        $supervisorPersonalData = null;

        if ($user->isSupervisor()) {
            $supervisorBranchData = $this->buildDashboardData(
                Outlet::query()->where('branch_id', $user->branch_id),
                Visit::query()->where('branch_id', $user->branch_id),
                'Aktivitas tim cabang',
                'Semua kunjungan sales, SMD, dan supervisor di cabang ini',
            );

            $supervisorPersonalData = $this->buildDashboardData(
                Outlet::query()->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)->orWhereHas('visits', fn ($visits) => $visits->where('user_id', $user->id));
                }),
                Visit::query()->where('user_id', $user->id),
                'Kunjungan supervisor sendiri',
                'Aktivitas pribadi supervisor selama 7 hari terakhir',
            );
        }

        return view('dashboard', [
            'user' => $user,
            'dashboardData' => $dashboardData,
            'supervisorBranchData' => $supervisorBranchData,
            'supervisorPersonalData' => $supervisorPersonalData,
        ]);
    }

    private function outletScope($user): Builder
    {
        $query = Outlet::query()->when(! $user->isAdminPusat(), fn ($inner) => $inner->where('branch_id', $user->branch_id));

        if ($user->isSales()) {
            $query->where(function ($inner) use ($user) {
                $inner->where('created_by', $user->id)->orWhereHas('visits', fn ($visits) => $visits->where('user_id', $user->id));
            });
        }

        if ($user->isSmd()) {
            $query->whereHas('visits', fn ($visits) => $visits->where('user_id', $user->id)->where('visit_type', 'smd'));
        }

        return $query;
    }

    private function visitScope($user): Builder
    {
        $query = Visit::query()->when(! $user->isAdminPusat(), fn ($inner) => $inner->where('branch_id', $user->branch_id));

        if ($user->isSales()) {
            $query->where('user_id', $user->id)->where('visit_type', 'sales');
        }

        if ($user->isSmd()) {
            $query->where('user_id', $user->id)->where('visit_type', 'smd');
        }

        return $query;
    }

    private function buildDashboardData(Builder $outletQuery, Builder $visitQuery, string $chartContext, string $chartHelper): array
    {
        $today = now()->startOfDay();
        $weekDates = collect(range(6, 0))->map(fn ($daysAgo) => CarbonImmutable::today()->subDays($daysAgo));

        $totalVisitsToday = (clone $visitQuery)->where('visited_at', '>=', $today)->count();
        $pendingOutlets = (clone $outletQuery)->where('verification_status', 'pending')->where('outlet_type', '!=', 'prospek')->count();
        $prospects = (clone $outletQuery)->where('outlet_type', 'prospek')->count();
        $nooWithoutCode = (clone $outletQuery)->where('outlet_type', 'noo')->whereNull('official_kode')->count();
        $inactiveOutlets = (clone $outletQuery)->where('outlet_status', 'inactive')->count();

        $chartSource = (clone $visitQuery)
            ->selectRaw('DATE(visited_at) as visit_date, COUNT(*) as total')
            ->where('visited_at', '>=', now()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(visited_at)')
            ->pluck('total', 'visit_date');

        $chartLabels = $weekDates->map(fn ($date) => $date->translatedFormat('D'))->values();
        $chartValues = $weekDates->map(fn ($date) => (int) ($chartSource[$date->toDateString()] ?? 0))->values();

        $recentPendingOutlets = (clone $outletQuery)
            ->with(['branch'])
            ->where('verification_status', 'pending')
            ->where('outlet_type', '!=', 'prospek')
            ->latest()
            ->limit(5)
            ->get();

        $recentVisits = (clone $visitQuery)
            ->with(['outlet', 'user', 'branch'])
            ->latest('visited_at')
            ->limit(5)
            ->get();

        return [
            'metrics' => [
                ['label' => 'Visit Hari Ini', 'value' => $totalVisitsToday, 'hint' => 'Kunjungan tercatat di hari berjalan'],
                ['label' => 'Outlet Pending', 'value' => $pendingOutlets, 'hint' => 'Butuh verifikasi atau official kode'],
                ['label' => 'Prospek Follow Up', 'value' => $prospects, 'hint' => 'Outlet prospek aktif untuk tindak lanjut'],
                ['label' => 'NOO Tanpa Official Kode', 'value' => $nooWithoutCode, 'hint' => 'Perlu finalisasi official kode'],
                ['label' => 'Outlet Inactive', 'value' => $inactiveOutlets, 'hint' => 'Outlet tutup atau tidak order lagi'],
            ],
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'chartContext' => $chartContext,
            'chartHelper' => $chartHelper,
            'recentPendingOutlets' => $recentPendingOutlets,
            'recentVisits' => $recentVisits,
        ];
    }
}
