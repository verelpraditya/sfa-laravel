<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmdVisitRequest;
use App\Models\Outlet;
use App\Models\SmdVisitActivity;
use App\Models\SmdVisitDetail;
use App\Models\SmdVisitDisplayPhoto;
use App\Models\Visit;
use App\Models\VisitSubmission;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

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
        $submissionToken = $request->string('submission_token')->toString() ?: (string) Str::uuid();

        $submissionState = $this->beginSubmission($submissionToken, $user->id, 'smd');

        if ($submissionState['status'] === 'duplicate') {
            return redirect()->route('smd-visits.index')->with('status', 'Kunjungan SMD berhasil disimpan untuk outlet '.$submissionState['visit']->outlet->name.'.');
        }

        if ($submissionState['status'] === 'pending') {
            return redirect()->route('smd-visits.index')->with('status', 'Kunjungan SMD sedang diproses. Silakan cek daftar kunjungan dalam beberapa saat.');
        }

        try {
            $visit = DB::transaction(function () use ($request, $user, $submissionToken) {
                $outlet = $request->existingOutlet();

                if (! $outlet) {
                    $initialStatus = $this->mapInitialOutletStatus($request->string('new_outlet_type')->toString());

                    $outlet = Outlet::create([
                        'branch_id' => $user->branch_id,
                        'name' => $request->string('new_outlet_name')->toString(),
                        'address' => $request->string('new_outlet_address')->toString(),
                        'district' => $request->string('new_outlet_district')->toString(),
                        'city' => $request->string('new_outlet_city')->toString(),
                        'category' => $request->string('new_outlet_category')->toString(),
                        'outlet_status' => $initialStatus,
                        'official_kode' => $request->string('new_outlet_official_kode')->toString() ?: null,
                        'pic_name' => $request->string('new_outlet_pic_name')->toString() ?: null,
                        'pic_phone' => $request->string('new_outlet_pic_phone')->toString() ?: null,
                        'verified_by' => $initialStatus === 'active' ? $user->id : null,
                        'verified_at' => $initialStatus === 'active' ? now() : null,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                }

                $visitPhoto = $request->file('visit_photo')->storeAs(
                    'visits/smd',
                    $this->buildPhotoFilename($user->username, $outlet->name, $request->file('visit_photo')->extension()),
                    'public',
                );
                $displayPhotos = $this->storeDisplayPhotos($request, $user->username, $outlet->name);

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
                    'display_photo_path' => $displayPhotos[0] ?? null,
                ]);

                foreach ($displayPhotos as $index => $photoPath) {
                    SmdVisitDisplayPhoto::create([
                        'visit_id' => $visit->id,
                        'photo_path' => $photoPath,
                        'sort_order' => $index + 1,
                    ]);
                }

                foreach ($request->validated('activities') as $activity) {
                    SmdVisitActivity::create([
                        'visit_id' => $visit->id,
                        'activity_type' => $activity,
                    ]);
                }

                VisitSubmission::where('token', $submissionToken)->update([
                    'visit_id' => $visit->id,
                    'completed_at' => now(),
                ]);

                return $visit;
            });
        } catch (Throwable $exception) {
            VisitSubmission::where('token', $submissionToken)->delete();

            throw $exception;
        }

        return redirect()->route('smd-visits.index')->with('status', 'Kunjungan SMD berhasil disimpan untuk outlet '.$visit->outlet->name.'.');
    }

    private function buildPhotoFilename(string $username, string $outletName, string $extension): string
    {
        $timestamp = now()->format('Ymd_His');
        $safeOutletName = Str::slug($outletName, '-');
        $safeUsername = Str::slug($username, '-');

        return sprintf('%s_%s_%s.%s', $safeUsername, $safeOutletName, $timestamp, strtolower($extension));
    }

    private function mapInitialOutletStatus(string $selection): string
    {
        return match ($selection) {
            'noo' => 'pending',
            'pelanggan_lama' => 'active',
            default => 'prospek',
        };
    }

    private function storeDisplayPhotos(SmdVisitRequest $request, string $username, string $outletName): array
    {
        if (! collect($request->validated('activities'))->contains('merapikan_display')) {
            return [];
        }

        $files = [];

        if ($request->hasFile('display_photos')) {
            $files = array_merge($files, $request->file('display_photos'));
        }

        if ($request->hasFile('display_photo')) {
            $files[] = $request->file('display_photo');
        }

        $paths = [];

        foreach ($files as $index => $file) {
            $paths[] = $file->storeAs(
                'visits/smd/display',
                $this->buildPhotoFilename($username, $outletName.'-display-'.($index + 1), $file->extension()),
                'public',
            );
        }

        return array_slice($paths, 0, 10);
    }

    private function beginSubmission(string $token, int $userId, string $visitType): array
    {
        try {
            VisitSubmission::create([
                'token' => $token,
                'user_id' => $userId,
                'visit_type' => $visitType,
            ]);

            return ['status' => 'new', 'visit' => null];
        } catch (QueryException $exception) {
            if (! $this->isDuplicateSubmissionException($exception)) {
                throw $exception;
            }

            for ($attempt = 0; $attempt < 40; $attempt++) {
                $existingVisitId = VisitSubmission::where('token', $token)->value('visit_id');

                if ($existingVisitId) {
                    return ['status' => 'duplicate', 'visit' => Visit::with('outlet')->find($existingVisitId)];
                }

                usleep(250000);
            }

            return ['status' => 'pending', 'visit' => null];
        }
    }

    private function isDuplicateSubmissionException(QueryException $exception): bool
    {
        return in_array($exception->getCode(), ['23000', '23505'], true);
    }
}
