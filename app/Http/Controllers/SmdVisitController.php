<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmdVisitRequest;
use App\Models\Outlet;
use App\Models\SmdVisitActivity;
use App\Models\SmdVisitDetail;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SmdVisitController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('search'));
        $activity = $request->string('activity')->toString();

        $visits = Visit::query()
            ->with(['outlet', 'smdDetail', 'smdActivities', 'branch', 'user'])
            ->where('visit_type', 'smd')
            ->when(! $user->isAdminPusat(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when($user->isSmd(), fn ($query) => $query->where('user_id', $user->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('outlet', function ($outletQuery) use ($search) {
                    $outletQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('official_kode', 'like', "%{$search}%");
                });
            })
            ->when($activity !== '', fn ($query) => $query->whereHas('smdActivities', fn ($activityQuery) => $activityQuery->where('activity_type', $activity)))
            ->latest('visited_at')
            ->paginate(10)
            ->withQueryString();

        return view('smd-visits.index', [
            'visits' => $visits,
            'filters' => [
                'search' => $search,
                'activity' => $activity,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('smd-visits.create', [
            'user' => $request->user(),
        ]);
    }

    public function store(SmdVisitRequest $request): RedirectResponse
    {
        $user = $request->user();

        $visit = DB::transaction(function () use ($request, $user) {
            $outlet = $request->existingOutlet();

            if (! $outlet) {
                $newType = $request->string('new_outlet_type')->toString();

                $outlet = Outlet::create([
                    'branch_id' => $user->branch_id,
                    'name' => $request->string('new_outlet_name')->toString(),
                    'address' => $request->string('new_outlet_address')->toString(),
                    'district' => $request->string('new_outlet_district')->toString(),
                    'city' => $request->string('new_outlet_city')->toString(),
                    'category' => $request->string('new_outlet_category')->toString(),
                    'outlet_type' => $newType,
                    'outlet_status' => 'active',
                    'official_kode' => $request->string('new_outlet_official_kode')->toString() ?: null,
                    'verification_status' => match ($newType) {
                        'pelanggan_lama' => 'verified',
                        'noo' => 'pending',
                        default => null,
                    },
                    'verified_by' => $newType === 'pelanggan_lama' ? $user->id : null,
                    'verified_at' => $newType === 'pelanggan_lama' ? now() : null,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }

            $visitPhoto = $request->file('visit_photo')->store('visits/smd', 'public');
            $displayPhoto = $request->hasFile('display_photo')
                ? $request->file('display_photo')->store('visits/smd/display', 'public')
                : null;

            $visit = Visit::create([
                'branch_id' => $user->branch_id,
                'outlet_id' => $outlet->id,
                'user_id' => $user->id,
                'visit_type' => 'smd',
                'outlet_condition' => null,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'visit_photo_path' => $visitPhoto,
                'visited_at' => now(),
                'notes' => $request->string('notes')->toString() ?: null,
            ]);

            SmdVisitDetail::create([
                'visit_id' => $visit->id,
                'po_amount' => $request->filled('po_amount') ? $request->input('po_amount') : null,
                'payment_amount' => $request->filled('payment_amount') ? $request->input('payment_amount') : null,
                'display_photo_path' => $displayPhoto,
            ]);

            foreach ($request->validated('activities') as $activity) {
                SmdVisitActivity::create([
                    'visit_id' => $visit->id,
                    'activity_type' => $activity,
                ]);
            }

            return $visit;
        });

        return redirect()->route('smd-visits.index')->with('status', 'Kunjungan SMD berhasil disimpan untuk outlet '.$visit->outlet->name.'.');
    }
}
