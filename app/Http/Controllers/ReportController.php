<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Branch;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $type = $request->string('type')->toString() ?: 'sales';
        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->toDateString();
        $branchId = $request->integer('branch_id');
        $userId = $request->integer('user_id');

        abort_unless(in_array($type, ['sales', 'smd', 'outlets'], true), 404);

        return match ($type) {
            'smd' => $this->renderSmdReport($user, $from, $to, $branchId, $userId),
            'outlets' => $this->renderOutletReport($user, $from, $to, $branchId, $userId),
            default => $this->renderSalesReport($user, $from, $to, $branchId, $userId),
        };
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $type = $request->string('type')->toString() ?: 'sales';
        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->toDateString();
        $branchId = $request->integer('branch_id');
        $userId = $request->integer('user_id');

        abort_unless(in_array($type, ['sales', 'smd', 'outlets'], true), 404);

        [$filename, $headers, $rows] = match ($type) {
            'smd' => $this->exportSmdReport($user, $from, $to, $branchId, $userId),
            'outlets' => $this->exportOutletReport($user, $from, $to, $branchId, $userId),
            default => $this->exportSalesReport($user, $from, $to, $branchId, $userId),
        };

        $callback = function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }

            fclose($stream);
        };

        return Response::streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    private function renderSalesReport($user, string $from, string $to, int $branchId = 0, int $userId = 0): View
    {
        $query = $this->salesQuery($user, $from, $to, $branchId, $userId);
        $visits = (clone $query)->paginate(12)->withQueryString();

        $summaryQuery = clone $query;

        return view('reports.index', [
            'activeType' => 'sales',
            'title' => 'Laporan Sales',
            'description' => 'Rekap kunjungan sales, nominal order, dan total tagihan.',
            'filters' => compact('from', 'to', 'branchId', 'userId'),
            'summary' => [
                ['label' => 'Total Visit', 'value' => (clone $summaryQuery)->count()],
                ['label' => 'Order', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->salesDetail?->order_amount ?? 0), 0, ',', '.')],
                ['label' => 'Tagihan', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->salesDetail?->receivable_amount ?? 0), 0, ',', '.')],
                ['label' => 'Outlet Tutup', 'value' => (clone $summaryQuery)->where('outlet_condition', 'tutup')->count()],
            ],
            'rows' => $visits,
            'branches' => $this->availableBranches($user),
            'users' => $this->availableUsers($user, $branchId),
        ]);
    }

    private function renderSmdReport($user, string $from, string $to, int $branchId = 0, int $userId = 0): View
    {
        $query = $this->smdQuery($user, $from, $to, $branchId, $userId);
        $visits = (clone $query)->paginate(12)->withQueryString();
        $summaryQuery = clone $query;

        return view('reports.index', [
            'activeType' => 'smd',
            'title' => 'Laporan SMD',
            'description' => 'Rekap aktivitas SMD, nominal PO, dan nominal pembayaran.',
            'filters' => compact('from', 'to', 'branchId', 'userId'),
            'summary' => [
                ['label' => 'Total Visit', 'value' => (clone $summaryQuery)->count()],
                ['label' => 'Nominal PO', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->smdDetail?->po_amount ?? 0), 0, ',', '.')],
                ['label' => 'Pembayaran', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->smdDetail?->payment_amount ?? 0), 0, ',', '.')],
                ['label' => 'Display Tasks', 'value' => (clone $summaryQuery)->get()->filter(fn ($visit) => $visit->smdActivities->contains('activity_type', 'merapikan_display'))->count()],
            ],
            'rows' => $visits,
            'branches' => $this->availableBranches($user),
            'users' => $this->availableUsers($user, $branchId),
        ]);
    }

    private function renderOutletReport($user, string $from, string $to, int $branchId = 0, int $userId = 0): View
    {
        $query = $this->outletQuery($user, $from, $to, $branchId, $userId);
        $outlets = (clone $query)->paginate(12)->withQueryString();
        $summaryQuery = clone $query;

        return view('reports.index', [
            'activeType' => 'outlets',
            'title' => 'Laporan Outlet',
            'description' => 'Rekap pertumbuhan outlet, prospek, NOO, dan status aktif/inaktif.',
            'filters' => compact('from', 'to', 'branchId', 'userId'),
            'summary' => [
                ['label' => 'Outlet Baru', 'value' => (clone $summaryQuery)->count()],
                ['label' => 'Prospek', 'value' => (clone $summaryQuery)->where('outlet_type', 'prospek')->count()],
                ['label' => 'Inactive', 'value' => (clone $summaryQuery)->where('outlet_status', 'inactive')->count()],
                ['label' => 'Prospek > 7 Hari', 'value' => (clone $summaryQuery)->where('outlet_type', 'prospek')->where('created_at', '<=', now()->subDays(7))->count()],
                ['label' => 'NOO > 7 Hari', 'value' => (clone $summaryQuery)->where('outlet_type', 'noo')->whereNull('official_kode')->where('created_at', '<=', now()->subDays(7))->count()],
            ],
            'rows' => $outlets,
            'branches' => $this->availableBranches($user),
            'users' => $this->availableUsers($user, $branchId),
        ]);
    }

    private function exportSalesReport($user, string $from, string $to, int $branchId = 0, int $userId = 0): array
    {
        $rows = $this->salesQuery($user, $from, $to, $branchId, $userId)->get()->map(fn ($visit) => [
            $visit->visitedAtForBranch()?->format('Y-m-d H:i'),
            $visit->branch?->name,
            $visit->user?->name,
            $visit->outlet?->name,
            $visit->outlet_condition,
            (float) ($visit->salesDetail?->order_amount ?? 0),
            (float) ($visit->salesDetail?->receivable_amount ?? 0),
        ]);

        return ['report-sales.csv', ['Waktu', 'Cabang', 'Pelaksana', 'Outlet', 'Kondisi', 'Nominal Order', 'Total Tagihan'], $rows];
    }

    private function exportSmdReport($user, string $from, string $to, int $branchId = 0, int $userId = 0): array
    {
        $rows = $this->smdQuery($user, $from, $to, $branchId, $userId)->get()->map(fn ($visit) => [
            $visit->visitedAtForBranch()?->format('Y-m-d H:i'),
            $visit->branch?->name,
            $visit->user?->name,
            $visit->outlet?->name,
            $visit->smdActivities->pluck('activity_type')->implode(', '),
            (float) ($visit->smdDetail?->po_amount ?? 0),
            (float) ($visit->smdDetail?->payment_amount ?? 0),
        ]);

        return ['report-smd.csv', ['Waktu', 'Cabang', 'Pelaksana', 'Outlet', 'Aktivitas', 'Nominal PO', 'Nominal Pembayaran'], $rows];
    }

    private function exportOutletReport($user, string $from, string $to, int $branchId = 0, int $userId = 0): array
    {
        $rows = $this->outletQuery($user, $from, $to, $branchId, $userId)->get()->map(fn ($outlet) => [
            $outlet->created_at?->format('Y-m-d H:i'),
            $outlet->branch?->name,
            $outlet->name,
            $outlet->typeLabel(),
            $outlet->official_kode,
            $outlet->statusLabel(),
            $outlet->verificationLabel(),
        ]);

        return ['report-outlets.csv', ['Dibuat', 'Cabang', 'Outlet', 'Jenis', 'Official Kode', 'Status Outlet', 'Verifikasi'], $rows];
    }

    private function salesQuery($user, string $from, string $to, int $branchId = 0, int $userId = 0): Builder
    {
        return Visit::query()
            ->with(['branch', 'user', 'outlet', 'salesDetail'])
            ->where('visit_type', 'sales')
            ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSales(), fn ($query) => $query->where('user_id', $user->id))
            ->when($branchId > 0 && ! $user->isSales(), fn ($query) => $query->where('branch_id', $branchId))
            ->when($userId > 0 && ! $user->isSales(), fn ($query) => $query->where('user_id', $userId))
            ->latest('visited_at');
    }

    private function smdQuery($user, string $from, string $to, int $branchId = 0, int $userId = 0): Builder
    {
        return Visit::query()
            ->with(['branch', 'user', 'outlet', 'smdDetail', 'smdActivities'])
            ->where('visit_type', 'smd')
            ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSmd(), fn ($query) => $query->where('user_id', $user->id))
            ->when($branchId > 0 && ! $user->isSmd(), fn ($query) => $query->where('branch_id', $branchId))
            ->when($userId > 0 && ! $user->isSmd(), fn ($query) => $query->where('user_id', $userId))
            ->latest('visited_at');
    }

    private function outletQuery($user, string $from, string $to, int $branchId = 0, int $userId = 0): Builder
    {
        return Outlet::query()
            ->with(['branch', 'creator'])
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
            ->when($userId > 0, fn ($query) => $query->where('created_by', $userId))
            ->latest();
    }

    private function availableBranches($user)
    {
        return Branch::query()
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('id', $user->branch_id))
            ->orderBy('name')
            ->get();
    }

    private function availableUsers($user, int $branchId = 0)
    {
        $effectiveBranchId = $user->isAdminPusat() ? $branchId : $user->branch_id;

        return User::query()
            ->when($effectiveBranchId > 0, fn ($query) => $query->where('branch_id', $effectiveBranchId))
            ->when($user->isSales() || $user->isSmd(), fn ($query) => $query->where('id', $user->id))
            ->orderBy('name')
            ->get();
    }
}
