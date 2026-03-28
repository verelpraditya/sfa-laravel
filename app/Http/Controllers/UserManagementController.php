<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManagementRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdminPusat(), 403);

        $search = trim((string) $request->string('search'));
        $role = $request->string('role')->toString();
        $branchId = $request->integer('branch_id');

        $users = User::query()
            ->with('branch')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'branches' => Branch::orderBy('name')->get(),
            'filters' => [
                'search' => $search,
                'role' => $role,
                'branch_id' => $branchId,
            ],
            'roles' => User::roleLabels(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdminPusat(), 403);

        return view('users.create', [
            'managedUser' => new User(),
            'branches' => Branch::orderBy('name')->get(),
            'roles' => User::roleLabels(),
        ]);
    }

    public function store(UserManagementRequest $request): RedirectResponse
    {
        $payload = $request->safe()->except(['password_confirmation']);
        $payload['password'] = Hash::make($request->string('password')->toString());
        $payload['branch_id'] = $payload['role'] === User::ROLE_ADMIN_PUSAT ? null : $payload['branch_id'];

        $user = User::create($payload);

        return redirect()->route('users.edit', $user)->with('status', 'User berhasil dibuat.');
    }

    public function edit(Request $request, User $user): View
    {
        abort_unless($request->user()->isAdminPusat(), 403);

        return view('users.edit', [
            'managedUser' => $user,
            'branches' => Branch::orderBy('name')->get(),
            'roles' => User::roleLabels(),
        ]);
    }

    public function update(UserManagementRequest $request, User $user): RedirectResponse
    {
        $payload = $request->safe()->except(['password', 'password_confirmation']);
        $payload['branch_id'] = $payload['role'] === User::ROLE_ADMIN_PUSAT ? null : $payload['branch_id'];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->string('password')->toString());
        }

        $user->update($payload);

        return redirect()->route('users.edit', $user)->with('status', 'User berhasil diperbarui.');
    }
}
