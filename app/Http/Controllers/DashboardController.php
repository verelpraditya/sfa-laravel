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
            $user,
            $this->outletScope($user),
            $this->visitScope($user),
            'Scope aktif sekarang',
            'Data real 7 hari terakhir di scope kamu',
        );

        $supervisorBranchData = null;
        $supervisorPersonalData = null;

        if ($user->isSupervisor()) {
            $supervisorBranchData = $this->buildDashboardData(
                $user,
                Outlet::query()->where('branch_id', $user->branch_id),
                Visit::query()->where('visits.branch_id', $user->branch_id),
                'Aktivitas tim cabang',
                'Semua kunjungan sales, SMD, dan supervisor di cabang ini',
            );

            $supervisorPersonalData = $this->buildDashboardData(
                $user,
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
        $query = Visit::query()->when(! $user->isAdminPusat(), fn ($inner) => $inner->where('visits.branch_id', $user->branch_id));

        if ($user->isSales()) {
            $query->where('visits.user_id', $user->id)->where('visits.visit_type', 'sales');
        }

        if ($user->isSmd()) {
            $query->where('visits.user_id', $user->id)->where('visits.visit_type', 'smd');
        }

        return $query;
    }

    private function buildDashboardData($user, Builder $outletQuery, Builder $visitQuery, string $chartContext, string $chartHelper): array
    {
        $today = now()->startOfDay();
        $weekDates = collect(range(6, 0))->map(fn ($daysAgo) => CarbonImmutable::today()->subDays($daysAgo));

        $totalVisitsToday = (clone $visitQuery)->where('visited_at', '>=', $today)->count();
        $pendingOutlets = (clone $outletQuery)->where('outlet_status', 'pending')->count();
        $prospects = (clone $outletQuery)->where('outlet_status', 'prospek')->count();
        $inactiveOutlets = (clone $outletQuery)->where('outlet_status', 'inactive')->count();
        $oldProspects = (clone $outletQuery)->where('outlet_status', 'prospek')->where('created_at', '<=', now()->subDays(7))->count();
        $oldPending = (clone $outletQuery)->where('outlet_status', 'pending')->where('created_at', '<=', now()->subDays(7))->count();
        $salesVisitsToday = (clone $visitQuery)->where('visit_type', 'sales')->where('visited_at', '>=', $today)->count();
        $smdVisitsToday = (clone $visitQuery)->where('visit_type', 'smd')->where('visited_at', '>=', $today)->count();
        $openVisits = (clone $visitQuery)->where('visits.outlet_condition', 'buka')->count();
        $closedVisits = (clone $visitQuery)->where('visits.outlet_condition', 'tutup')->count();

        $salesFinancialToday = (clone $visitQuery)
            ->where('visits.visit_type', 'sales')
            ->where('visits.visited_at', '>=', $today)
            ->leftJoin('sales_visit_details', 'sales_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('COALESCE(SUM(sales_visit_details.order_amount), 0) as total_order, COALESCE(SUM(sales_visit_details.receivable_amount), 0) as total_receivable')
            ->first();

        $smdFinancial = (clone $visitQuery)
            ->where('visits.visit_type', 'smd')
            ->leftJoin('smd_visit_details', 'smd_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('COALESCE(SUM(smd_visit_details.po_amount), 0) as total_po, COALESCE(SUM(smd_visit_details.payment_amount), 0) as total_payment')
            ->first();

        $smdFinancialToday = (clone $visitQuery)
            ->where('visits.visit_type', 'smd')
            ->where('visits.visited_at', '>=', $today)
            ->leftJoin('smd_visit_details', 'smd_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('COALESCE(SUM(smd_visit_details.po_amount), 0) as total_po, COALESCE(SUM(smd_visit_details.payment_amount), 0) as total_payment')
            ->first();

        $salesAmountToday = (float) ($salesFinancialToday->total_order ?? 0) + (float) ($smdFinancialToday->total_po ?? 0);
        $collectionToday = (float) ($salesFinancialToday->total_receivable ?? 0) + (float) ($smdFinancialToday->total_payment ?? 0);

        $topPerformers = (clone $visitQuery)
            ->select('users.name')
            ->join('users', 'users.id', '=', 'visits.user_id')
            ->groupBy('users.name')
            ->selectRaw('COUNT(visits.id) as total_visits')
            ->orderByDesc('total_visits')
            ->limit(5)
            ->get();

        $topCustomers = (clone $visitQuery)
            ->where('visits.visit_type', 'sales')
            ->join('sales_visit_details', 'sales_visit_details.visit_id', '=', 'visits.id')
            ->join('outlets', 'outlets.id', '=', 'visits.outlet_id')
            ->where('sales_visit_details.order_amount', '>', 0)
            ->groupBy('outlets.id', 'outlets.name')
            ->selectRaw('outlets.id, outlets.name, COUNT(visits.id) as total_orders, MAX(visits.visited_at) as last_order_at, COALESCE(SUM(sales_visit_details.order_amount), 0) as total_amount')
            ->orderByDesc('total_orders')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $staleCustomers = (clone $visitQuery)
            ->where('visits.visit_type', 'sales')
            ->join('sales_visit_details', 'sales_visit_details.visit_id', '=', 'visits.id')
            ->join('outlets', 'outlets.id', '=', 'visits.outlet_id')
            ->where('sales_visit_details.order_amount', '>', 0)
            ->groupBy('outlets.id', 'outlets.name')
            ->selectRaw('outlets.id, outlets.name, MAX(visits.visited_at) as last_order_at, COUNT(visits.id) as total_orders, COALESCE(SUM(sales_visit_details.order_amount), 0) as total_amount')
            ->havingRaw('MAX(visits.visited_at) <= ?', [now()->subDays(30)->endOfDay()])
            ->orderBy('last_order_at')
            ->limit(5)
            ->get();

        $outletComposition = [
            'prospek' => (clone $outletQuery)->where('outlet_status', 'prospek')->count(),
            'pending' => (clone $outletQuery)->where('outlet_status', 'pending')->count(),
            'active' => (clone $outletQuery)->where('outlet_status', 'active')->count(),
        ];

        $chartSource = (clone $visitQuery)
            ->selectRaw('DATE(visited_at) as visit_date, COUNT(*) as total')
            ->where('visited_at', '>=', now()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(visited_at)')
            ->pluck('total', 'visit_date');

        $salesCollectionSource = (clone $visitQuery)
            ->where('visits.visit_type', 'sales')
            ->where('visits.visited_at', '>=', now()->subDays(6)->startOfDay())
            ->leftJoin('sales_visit_details', 'sales_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('DATE(visits.visited_at) as visit_date, COALESCE(SUM(sales_visit_details.receivable_amount), 0) as total_collection')
            ->groupByRaw('DATE(visits.visited_at)')
            ->pluck('total_collection', 'visit_date');

        $smdCollectionSource = (clone $visitQuery)
            ->where('visits.visit_type', 'smd')
            ->where('visits.visited_at', '>=', now()->subDays(6)->startOfDay())
            ->leftJoin('smd_visit_details', 'smd_visit_details.visit_id', '=', 'visits.id')
            ->selectRaw('DATE(visits.visited_at) as visit_date, COALESCE(SUM(smd_visit_details.payment_amount), 0) as total_collection')
            ->groupByRaw('DATE(visits.visited_at)')
            ->pluck('total_collection', 'visit_date');

        $chartLabels = $weekDates->map(fn ($date) => $date->translatedFormat('D'))->values();
        $chartValues = $weekDates->map(fn ($date) => (int) ($chartSource[$date->toDateString()] ?? 0))->values();
        $collectionValues = $weekDates->map(function ($date) use ($salesCollectionSource, $smdCollectionSource) {
            $key = $date->toDateString();

            return (float) ($salesCollectionSource[$key] ?? 0) + (float) ($smdCollectionSource[$key] ?? 0);
        })->values();

        $recentPendingOutlets = (clone $outletQuery)
            ->with(['branch'])
            ->where('outlet_status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $recentVisits = (clone $visitQuery)
            ->with(['outlet', 'user', 'branch', 'salesDetail', 'smdDetail'])
            ->where('visited_at', '>=', $today)
            ->latest('visited_at')
            ->limit(10)
            ->get();

        $metrics = match (true) {
            $user->isSales() => [
                ['label' => 'Visit Hari Ini', 'value' => $totalVisitsToday, 'hint' => 'Kunjungan sales kamu hari ini'],
                ['label' => 'Sales Amount Hari Ini', 'value' => 'Rp '.number_format((float) ($salesFinancialToday->total_order ?? 0), 0, ',', '.'), 'hint' => 'Nominal order yang masuk hari ini'],
                ['label' => 'Collection Hari Ini', 'value' => 'Rp '.number_format((float) ($salesFinancialToday->total_receivable ?? 0), 0, ',', '.'), 'hint' => 'Collection yang tercatat dari kunjungan sales hari ini'],
                ['label' => 'Outlet Buka', 'value' => $openVisits, 'hint' => 'Outlet melayani transaksi'],
                ['label' => 'Outlet Tutup', 'value' => $closedVisits, 'hint' => 'Outlet tidak beroperasi'],
            ],
            $user->isSmd() => [
                ['label' => 'Visit Hari Ini', 'value' => $totalVisitsToday, 'hint' => 'Aktivitas SMD kamu hari ini'],
                ['label' => 'Sales Amount Hari Ini', 'value' => 'Rp '.number_format((float) ($smdFinancialToday->total_po ?? 0), 0, ',', '.'), 'hint' => 'Nominal PO yang tercatat hari ini'],
                ['label' => 'Collection Hari Ini', 'value' => 'Rp '.number_format((float) ($smdFinancialToday->total_payment ?? 0), 0, ',', '.'), 'hint' => 'Nominal pembayaran yang diambil hari ini'],
                ['label' => 'Prospek Terkait', 'value' => $prospects, 'hint' => 'Prospek yang pernah tersentuh SMD'],
                ['label' => 'Outlet Inactive', 'value' => $inactiveOutlets, 'hint' => 'Outlet yang butuh perhatian'],
            ],
            $user->isSupervisor() => [
                ['label' => 'Visit Tim Hari Ini', 'value' => $salesVisitsToday + $smdVisitsToday, 'hint' => 'Seluruh visit sales dan SMD hari ini'],
                ['label' => 'Sales Amount Hari Ini', 'value' => 'Rp '.number_format($salesAmountToday, 0, ',', '.'), 'hint' => 'Gabungan order sales dan PO SMD hari ini'],
                ['label' => 'Collection Hari Ini', 'value' => 'Rp '.number_format($collectionToday, 0, ',', '.'), 'hint' => 'Gabungan collection sales dan SMD hari ini'],
                ['label' => 'Outlet Pending', 'value' => $pendingOutlets, 'hint' => 'Masih menunggu official kode supervisor'],
            ],
            default => [
                ['label' => 'Visit Tim Hari Ini', 'value' => $salesVisitsToday + $smdVisitsToday, 'hint' => 'Total visit sales dan SMD hari ini'],
                ['label' => 'Sales Amount Hari Ini', 'value' => 'Rp '.number_format($salesAmountToday, 0, ',', '.'), 'hint' => 'Gabungan order sales dan PO SMD hari ini'],
                ['label' => 'Collection Hari Ini', 'value' => 'Rp '.number_format($collectionToday, 0, ',', '.'), 'hint' => 'Gabungan collection sales dan SMD hari ini'],
                ['label' => 'Outlet Pending', 'value' => $pendingOutlets, 'hint' => 'Masih menunggu official kode supervisor'],
            ],
        };

        $highlights = [
            ['label' => 'Outlet Pending', 'value' => $pendingOutlets, 'hint' => 'Masih menunggu official kode supervisor'],
            ['label' => 'Prospek Follow Up', 'value' => $prospects, 'hint' => 'Potensi outlet yang bisa di-follow up'],
            ['label' => 'Prospek > 7 Hari', 'value' => $oldProspects, 'hint' => 'Perlu follow up ulang'],
            ['label' => 'Pending > 7 Hari', 'value' => $oldPending, 'hint' => 'Perlu official kode segera'],
            ['label' => 'Outlet Inactive', 'value' => $inactiveOutlets, 'hint' => 'Outlet tutup atau tidak order lagi'],
            ['label' => 'Outlet Buka/Tutup', 'value' => $openVisits.' / '.$closedVisits, 'hint' => 'Kondisi operasional dari kunjungan'],
        ];

        return [
            'metrics' => $metrics,
            'highlights' => $highlights,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'collectionValues' => $collectionValues,
            'chartContext' => $chartContext,
            'chartHelper' => $chartHelper,
            'recentPendingOutlets' => $recentPendingOutlets,
            'recentVisits' => $recentVisits,
            'topPerformers' => $topPerformers,
            'topCustomers' => $topCustomers,
            'staleCustomers' => $staleCustomers,
            'outletComposition' => $outletComposition,
        ];
    }
}
