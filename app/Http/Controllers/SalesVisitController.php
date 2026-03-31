<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesVisitRequest;
use App\Models\Outlet;
use App\Models\SalesVisitDetail;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SalesVisitController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('search'));
        $condition = $request->string('condition')->toString();

        $visits = Visit::query()
            ->with(['outlet', 'salesDetail', 'branch', 'user'])
            ->where('visit_type', 'sales')
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSales(), fn ($query) => $query->where('user_id', $user->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('outlet', function ($outletQuery) use ($search) {
                    $outletQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_kode', 'like', "%{$search}%");
                });
            })
            ->when($condition !== '', fn ($query) => $query->where('outlet_condition', $condition))
            ->latest('visited_at')
            ->paginate(10)
            ->withQueryString();

        return view('sales-visits.index', [
            'visits' => $visits,
            'filters' => [
                'search' => $search,
                'condition' => $condition,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('sales-visits.create', [
            'user' => $request->user(),
        ]);
    }

    public function store(SalesVisitRequest $request): RedirectResponse
    {
        $user = $request->user();

        $visit = DB::transaction(function () use ($request, $user) {
            $outlet = $request->existingOutlet();

            if (! $outlet) {
                $outlet = Outlet::create([
                    'branch_id' => $user->branch_id,
                    'name' => $request->string('new_outlet_name')->toString(),
                    'address' => $request->string('new_outlet_address')->toString(),
                    'district' => $request->string('new_outlet_district')->toString(),
                    'city' => $request->string('new_outlet_city')->toString(),
                    'category' => $request->string('new_outlet_category')->toString(),
                    'outlet_type' => $request->string('new_outlet_type')->toString(),
                    'outlet_status' => 'active',
                    'official_kode' => $request->string('new_outlet_official_kode')->toString() ?: null,
                    'verification_status' => match ($request->string('new_outlet_type')->toString()) {
                        'pelanggan_lama' => 'verified',
                        'noo' => 'pending',
                        default => null,
                    },
                    'verified_by' => $request->string('new_outlet_type')->toString() === 'pelanggan_lama' ? $user->id : null,
                    'verified_at' => $request->string('new_outlet_type')->toString() === 'pelanggan_lama' ? now() : null,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            } elseif ($request->filled('existing_outlet_type') && $request->string('existing_outlet_type')->toString() !== $outlet->outlet_type) {
                $outlet->update([
                    'outlet_type' => $request->string('existing_outlet_type')->toString(),
                    'updated_by' => $user->id,
                ]);
            }

            $storedPhoto = $request->file('visit_photo')->storeAs(
                'visits/sales',
                $this->buildPhotoFilename($user->username, $outlet->name, $request->file('visit_photo')->extension()),
                'public',
            );

            $visit = Visit::create([
                'branch_id' => $user->branch_id,
                'outlet_id' => $outlet->id,
                'user_id' => $user->id,
                'visit_type' => 'sales',
                'outlet_condition' => $request->string('outlet_condition')->toString(),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'visit_photo_path' => $storedPhoto,
                'visited_at' => now(),
                'notes' => $request->string('notes')->toString() ?: null,
            ]);

            SalesVisitDetail::create([
                'visit_id' => $visit->id,
                'order_amount' => $request->filled('order_amount') ? $request->input('order_amount') : null,
                'receivable_amount' => $request->filled('receivable_amount') ? $request->input('receivable_amount') : null,
            ]);

            return $visit;
        });

        return redirect()->route('sales-visits.index')->with('status', 'Kunjungan sales berhasil disimpan untuk outlet '.$visit->outlet->name.'.');
    }

    private function buildPhotoFilename(string $username, string $outletName, string $extension): string
    {
        $timestamp = now()->format('Ymd_His');
        $safeOutletName = Str::slug($outletName, '-');
        $safeUsername = Str::slug($username, '-');

        return sprintf('%s_%s_%s.%s', $safeUsername, $safeOutletName, $timestamp, strtolower($extension));
    }
}
