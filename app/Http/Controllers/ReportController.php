<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        abort_unless(in_array($type, ['sales', 'smd', 'outlets'], true), 404);

        return match ($type) {
            'smd' => $this->renderSmdReport($user, $from, $to),
            'outlets' => $this->renderOutletReport($user, $from, $to),
            default => $this->renderSalesReport($user, $from, $to),
        };
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $type = $request->string('type')->toString() ?: 'sales';
        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->toDateString();

        abort_unless(in_array($type, ['sales', 'smd', 'outlets'], true), 404);

        [$filename, $headers, $rows] = match ($type) {
            'smd' => $this->exportSmdReport($user, $from, $to),
            'outlets' => $this->exportOutletReport($user, $from, $to),
            default => $this->exportSalesReport($user, $from, $to),
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

    private function renderSalesReport($user, string $from, string $to): View
    {
        $query = $this->salesQuery($user, $from, $to);
        $visits = (clone $query)->paginate(12)->withQueryString();

        $summaryQuery = clone $query;

        return view('reports.index', [
            'activeType' => 'sales',
            'title' => 'Laporan Sales',
            'description' => 'Rekap kunjungan sales, nominal order, dan total tagihan.',
            'filters' => compact('from', 'to'),
            'summary' => [
                ['label' => 'Total Visit', 'value' => (clone $summaryQuery)->count()],
                ['label' => 'Order', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->salesDetail?->order_amount ?? 0), 0, ',', '.')],
                ['label' => 'Tagihan', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->salesDetail?->receivable_amount ?? 0), 0, ',', '.')],
            ],
            'rows' => $visits,
        ]);
    }

    private function renderSmdReport($user, string $from, string $to): View
    {
        $query = $this->smdQuery($user, $from, $to);
        $visits = (clone $query)->paginate(12)->withQueryString();
        $summaryQuery = clone $query;

        return view('reports.index', [
            'activeType' => 'smd',
            'title' => 'Laporan SMD',
            'description' => 'Rekap aktivitas SMD, nominal PO, dan nominal pembayaran.',
            'filters' => compact('from', 'to'),
            'summary' => [
                ['label' => 'Total Visit', 'value' => (clone $summaryQuery)->count()],
                ['label' => 'Nominal PO', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->smdDetail?->po_amount ?? 0), 0, ',', '.')],
                ['label' => 'Pembayaran', 'value' => 'Rp '.number_format((float) (clone $summaryQuery)->get()->sum(fn ($visit) => $visit->smdDetail?->payment_amount ?? 0), 0, ',', '.')],
            ],
            'rows' => $visits,
        ]);
    }

    private function renderOutletReport($user, string $from, string $to): View
    {
        $query = $this->outletQuery($user, $from, $to);
        $outlets = (clone $query)->paginate(12)->withQueryString();
        $summaryQuery = clone $query;

        return view('reports.index', [
            'activeType' => 'outlets',
            'title' => 'Laporan Outlet',
            'description' => 'Rekap pertumbuhan outlet, prospek, NOO, dan status aktif/inaktif.',
            'filters' => compact('from', 'to'),
            'summary' => [
                ['label' => 'Outlet Baru', 'value' => (clone $summaryQuery)->count()],
                ['label' => 'Prospek', 'value' => (clone $summaryQuery)->where('outlet_type', 'prospek')->count()],
                ['label' => 'Inactive', 'value' => (clone $summaryQuery)->where('outlet_status', 'inactive')->count()],
            ],
            'rows' => $outlets,
        ]);
    }

    private function exportSalesReport($user, string $from, string $to): array
    {
        $rows = $this->salesQuery($user, $from, $to)->get()->map(fn ($visit) => [
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

    private function exportSmdReport($user, string $from, string $to): array
    {
        $rows = $this->smdQuery($user, $from, $to)->get()->map(fn ($visit) => [
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

    private function exportOutletReport($user, string $from, string $to): array
    {
        $rows = $this->outletQuery($user, $from, $to)->get()->map(fn ($outlet) => [
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

    private function salesQuery($user, string $from, string $to): Builder
    {
        return Visit::query()
            ->with(['branch', 'user', 'outlet', 'salesDetail'])
            ->where('visit_type', 'sales')
            ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSales(), fn ($query) => $query->where('user_id', $user->id))
            ->latest('visited_at');
    }

    private function smdQuery($user, string $from, string $to): Builder
    {
        return Visit::query()
            ->with(['branch', 'user', 'outlet', 'smdDetail', 'smdActivities'])
            ->where('visit_type', 'smd')
            ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSmd(), fn ($query) => $query->where('user_id', $user->id))
            ->latest('visited_at');
    }

    private function outletQuery($user, string $from, string $to): Builder
    {
        return Outlet::query()
            ->with(['branch'])
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->latest();
    }
}
