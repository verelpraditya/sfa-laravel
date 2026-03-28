<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdminPusat(), 403);

        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();

        $branches = Branch::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('timezone', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('is_active', $status === 'active'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('branches.index', [
            'branches' => $branches,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'timezones' => Branch::timezoneOptions(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdminPusat(), 403);

        return view('branches.create', [
            'branch' => new Branch(),
            'timezones' => Branch::timezoneOptions(),
        ]);
    }

    public function store(BranchRequest $request): RedirectResponse
    {
        $branch = Branch::create($request->validated());

        return redirect()->route('branches.edit', $branch)->with('status', 'Cabang berhasil dibuat.');
    }

    public function edit(Request $request, Branch $branch): View
    {
        abort_unless($request->user()->isAdminPusat(), 403);

        return view('branches.edit', [
            'branch' => $branch,
            'timezones' => Branch::timezoneOptions(),
        ]);
    }

    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->validated());

        return redirect()->route('branches.edit', $branch)->with('status', 'Cabang berhasil diperbarui.');
    }
}
