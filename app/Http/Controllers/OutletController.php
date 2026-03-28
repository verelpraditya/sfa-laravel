<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutletRequest;
use App\Models\Branch;
use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletController extends Controller
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('outlets.index', [
            'outlets' => $outlets,
            'branches' => Branch::orderBy('name')->get(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'type' => $type,
                'outlet_status' => $outletStatus,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->canManageOutletMaster(), 403);

        return view('outlets.create', [
            'outlet' => new Outlet(),
            'branches' => Branch::orderBy('name')->get(),
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

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if ($query === '') {
            return response()->json(['data' => []]);
        }

        $outlets = $this->baseQuery($request->user())
            ->select(['id', 'branch_id', 'name', 'official_kode', 'district', 'city', 'category', 'outlet_type', 'verification_status'])
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
                'outlet_type' => $outlet->outlet_type,
                'verification_status' => $outlet->verification_status,
                'branch' => $outlet->branch?->name,
            ]),
        ]);
    }

    private function baseQuery($user)
    {
        return Outlet::query()->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id));
    }

    private function ensureUserCanAccess($user, Outlet $outlet): void
    {
        abort_unless($user->canManageOutletMaster(), 403);
        abort_if(! $user->isAdminPusat() && $user->branch_id !== $outlet->branch_id, 404);
    }
}
