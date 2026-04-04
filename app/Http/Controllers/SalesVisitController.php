<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesVisitRequest;
use App\Models\Outlet;
use App\Models\SalesVisitDetail;
use App\Models\Visit;
use App\Models\VisitSubmission;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

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
        $submissionToken = $request->string('submission_token')->toString() ?: (string) Str::uuid();

        $submissionState = $this->beginSubmission($submissionToken, $user->id, 'sales');

        if ($submissionState['status'] === 'duplicate') {
            return redirect()->route('sales-visits.index')->with('status', 'Kunjungan sales berhasil disimpan untuk outlet '.$submissionState['visit']->outlet->name.'.');
        }

        if ($submissionState['status'] === 'pending') {
            return redirect()->route('sales-visits.index')->with('status', 'Kunjungan sales sedang diproses. Silakan cek daftar kunjungan dalam beberapa saat.');
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
                        'verified_by' => $initialStatus === 'active' ? $user->id : null,
                        'verified_at' => $initialStatus === 'active' ? now() : null,
                        'created_by' => $user->id,
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

        return redirect()->route('sales-visits.index')->with('status', 'Kunjungan sales berhasil disimpan untuk outlet '.$visit->outlet->name.'.');
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
