<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OutletMergeController extends Controller
{
    /**
     * Display groups of suspected duplicate outlets.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user->canMergeOutlets(), 403);

        $branchScope = ! $user->isAdminPusat() ? $user->branch_id : null;

        // ── Detect duplicates by normalised name within the same branch ──
        $nameGroups = DB::table('outlets')
            ->select('branch_id', DB::raw('LOWER(TRIM(name)) as norm_name'), DB::raw('COUNT(*) as cnt'))
            ->when($branchScope, fn ($q) => $q->where('branch_id', $branchScope))
            ->groupBy('branch_id', DB::raw('LOWER(TRIM(name))'))
            ->having('cnt', '>', 1)
            ->get();

        // ── Detect duplicates by official_kode within the same branch ──
        $kodeGroups = DB::table('outlets')
            ->select('branch_id', 'official_kode', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('official_kode')
            ->where('official_kode', '!=', '')
            ->when($branchScope, fn ($q) => $q->where('branch_id', $branchScope))
            ->groupBy('branch_id', 'official_kode')
            ->having('cnt', '>', 1)
            ->get();

        // ── Build unified list of duplicate groups ──
        $groups = collect();

        foreach ($nameGroups as $row) {
            $outlets = Outlet::where('branch_id', $row->branch_id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [$row->norm_name])
                ->with(['branch', 'visits'])
                ->get();

            $groups->push([
                'type' => 'name',
                'key' => $row->norm_name,
                'branch' => $outlets->first()?->branch?->name ?? '-',
                'outlets' => $outlets,
            ]);
        }

        foreach ($kodeGroups as $row) {
            // Avoid duplicating groups already caught by name
            $outlets = Outlet::where('branch_id', $row->branch_id)
                ->where('official_kode', $row->official_kode)
                ->with(['branch', 'visits'])
                ->get();

            $alreadyCaptured = $groups->contains(function ($g) use ($outlets) {
                $existingIds = $g['outlets']->pluck('id')->sort()->values()->toArray();
                $newIds = $outlets->pluck('id')->sort()->values()->toArray();

                return $existingIds === $newIds;
            });

            if (! $alreadyCaptured) {
                $groups->push([
                    'type' => 'kode',
                    'key' => $row->official_kode,
                    'branch' => $outlets->first()?->branch?->name ?? '-',
                    'outlets' => $outlets,
                ]);
            }
        }

        return view('outlets.duplicates', [
            'groups' => $groups,
        ]);
    }

    /**
     * Show side-by-side comparison of a duplicate group for merging.
     * The {outlet} parameter is the "anchor" outlet — we find its duplicates.
     */
    public function show(Request $request, Outlet $outlet): View
    {
        $user = $request->user();

        abort_unless($user->canMergeOutlets(), 403);
        abort_if(! $user->isAdminPusat() && $user->branch_id !== $outlet->branch_id, 404);

        // Find outlets with same normalised name OR same official_kode in same branch
        $duplicates = Outlet::where('branch_id', $outlet->branch_id)
            ->where('id', '!=', $outlet->id)
            ->where(function ($q) use ($outlet) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($outlet->name))]);
                if ($outlet->official_kode) {
                    $q->orWhere('official_kode', $outlet->official_kode);
                }
            })
            ->with(['branch', 'creator', 'verifier'])
            ->withCount('visits')
            ->get();

        $outlet->loadCount('visits');
        $outlet->load(['branch', 'creator', 'verifier']);

        $allOutlets = collect([$outlet])->merge($duplicates);

        return view('outlets.merge', [
            'anchor' => $outlet,
            'allOutlets' => $allOutlets,
        ]);
    }

    /**
     * Execute merge: move all related data from duplicates to master, then delete duplicates.
     */
    public function merge(Request $request, Outlet $outlet): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->canMergeOutlets(), 403);
        abort_if(! $user->isAdminPusat() && $user->branch_id !== $outlet->branch_id, 404);

        $request->validate([
            'master_id' => 'required|exists:outlets,id',
            'duplicate_ids' => 'required|array|min:1',
            'duplicate_ids.*' => 'exists:outlets,id',
        ]);

        $masterId = (int) $request->input('master_id');
        $duplicateIds = collect($request->input('duplicate_ids'))->map(fn ($id) => (int) $id)->toArray();

        // Master must not be in duplicates
        if (in_array($masterId, $duplicateIds, true)) {
            return back()->with('error', 'Outlet master tidak boleh termasuk dalam daftar duplikat.');
        }

        // Ensure all involved outlets belong to same branch
        $allIds = array_merge([$masterId], $duplicateIds);
        $outlets = Outlet::whereIn('id', $allIds)->get();

        if ($outlets->count() !== count($allIds)) {
            return back()->with('error', 'Beberapa outlet tidak ditemukan.');
        }

        $branchIds = $outlets->pluck('branch_id')->unique();
        if ($branchIds->count() > 1) {
            return back()->with('error', 'Semua outlet yang akan digabung harus berada di cabang yang sama.');
        }

        // Branch access check for supervisor
        if (! $user->isAdminPusat() && $branchIds->first() !== $user->branch_id) {
            abort(403);
        }

        DB::transaction(function () use ($masterId, $duplicateIds) {
            foreach ($duplicateIds as $dupId) {
                // Move visits
                DB::table('visits')->where('outlet_id', $dupId)->update(['outlet_id' => $masterId]);

                // Move audit logs
                DB::table('outlet_status_histories')->where('outlet_id', $dupId)->update(['outlet_id' => $masterId]);
                DB::table('outlet_verification_logs')->where('outlet_id', $dupId)->update(['outlet_id' => $masterId]);

                // Delete the duplicate outlet
                Outlet::where('id', $dupId)->delete();
            }
        });

        $duplicateCount = count($duplicateIds);

        return redirect()->route('outlets.duplicates')->with('status', "{$duplicateCount} outlet duplikat berhasil digabung.");
    }
}
