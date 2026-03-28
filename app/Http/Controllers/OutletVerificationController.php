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
        $status = $request->string('status')->toString();
        $type = $request->string('type')->toString();
        $outletStatus = $request->string('outlet_status')->toString();

        $outlets = $this->baseQuery($user)
            ->with(['branch', 'creator', 'verifier'])
            ->when($type === '', fn ($query) => $query->where('outlet_type', '!=', 'prospek'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_kode', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('verification_status', $status))
            ->when($type !== '', fn ($query) => $query->where('outlet_type', $type))
            ->when($outletStatus !== '', fn ($query) => $query->where('outlet_status', $outletStatus))
            ->orderByRaw("verification_status = 'pending' desc")
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('outlet-verifications.index', [
            'outlets' => $outlets,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'type' => $type,
                'outlet_status' => $outletStatus,
            ],
        ]);
    }

    public function edit(Request $request, Outlet $outlet): View
    {
        $this->ensureUserCanAccess($request->user(), $outlet);

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

        DB::transaction(function () use ($request, $outlet): void {
            $oldType = $outlet->outlet_type;

            $outlet->update([
                ...$request->validatedPayload($outlet),
                'updated_by' => $request->user()->id,
            ]);

            if ($oldType !== $outlet->outlet_type) {
                DB::table('outlet_status_histories')->insert([
                    'outlet_id' => $outlet->id,
                    'old_outlet_type' => $oldType,
                    'new_outlet_type' => $outlet->outlet_type,
                    'changed_by' => $request->user()->id,
                    'notes' => $request->string('verification_notes')->toString() ?: 'Status diperbarui saat verifikasi outlet.',
                    'created_at' => now(),
                ]);
            }

            DB::table('outlet_verification_logs')->insert([
                'outlet_id' => $outlet->id,
                'verification_status' => $outlet->verification_status ?: 'not_required',
                'official_kode' => $outlet->official_kode,
                'verified_by' => $request->user()->id,
                'notes' => $request->string('verification_notes')->toString() ?: null,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('outlet-verifications.edit', $outlet)->with('status', 'Verifikasi outlet berhasil diperbarui.');
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
