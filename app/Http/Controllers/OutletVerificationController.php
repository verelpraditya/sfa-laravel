<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutletVerificationRequest;
use App\Models\Branch;
use App\Models\Outlet;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OutletVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('search'));

        $outlets = $this->baseQuery($user)
            ->with(['branch', 'creator', 'verifier'])
            ->where('outlet_status', 'pending')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_kode', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('outlet-verifications.index', [
            'outlets' => $outlets,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function edit(Request $request, Outlet $outlet): View
    {
        $this->ensureUserCanAccess($request->user(), $outlet);
        abort_unless($outlet->outlet_status === 'pending', 404);

        $recentVisits = Visit::query()
            ->with('user:id,name,role')
            ->where('outlet_id', $outlet->id)
            ->latest('visited_at')
            ->limit(8)
            ->get();

        return view('outlet-verifications.edit', [
            'outlet' => $outlet->load(['branch', 'creator', 'verifier']),
            'branches' => Branch::orderBy('name')->get(),
            'recentVisits' => $recentVisits,
        ]);
    }

    public function update(OutletVerificationRequest $request, Outlet $outlet): RedirectResponse
    {
        $this->ensureUserCanAccess($request->user(), $outlet);
        abort_unless($outlet->outlet_status === 'pending', 404);

        DB::transaction(function () use ($request, $outlet): void {
            $oldStatus = $outlet->outlet_status;

            $outlet->update([
                ...$request->validatedPayload($outlet),
                'updated_by' => $request->user()->id,
            ]);

            if ($oldStatus !== $outlet->outlet_status) {
                DB::table('outlet_status_histories')->insert([
                    'outlet_id' => $outlet->id,
                    'old_outlet_type' => $oldStatus,
                    'new_outlet_type' => $outlet->outlet_status,
                    'changed_by' => $request->user()->id,
                    'notes' => $request->string('verification_notes')->toString() ?: 'Status outlet diperbarui saat review outlet.',
                    'created_at' => now(),
                ]);
            }

            DB::table('outlet_verification_logs')->insert([
                'outlet_id' => $outlet->id,
                'verification_status' => $outlet->outlet_status,
                'official_kode' => $outlet->official_kode,
                'verified_by' => $request->user()->id,
                'notes' => $request->string('verification_notes')->toString() ?: null,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('outlet-verifications.index')->with('status', 'Official kode berhasil disimpan dan outlet sudah aktif.');
    }

    private function baseQuery($user)
    {
        return Outlet::query()->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id));
    }

    private function ensureUserCanAccess($user, Outlet $outlet): void
    {
        abort_if(! $user->isAdminPusat() && $user->branch_id !== $outlet->branch_id, 404);
    }
}
