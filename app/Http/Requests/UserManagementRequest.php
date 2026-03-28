<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdminPusat();
    }

    public function rules(): array
    {
        /** @var User|null $managedUser */
        $managedUser = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique(User::class, 'username')->ignore($managedUser?->id)],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($managedUser?->id)],
            'role' => ['required', Rule::in(User::roleOptions())],
            'branch_id' => [
                Rule::requiredIf($this->input('role') !== User::ROLE_ADMIN_PUSAT),
                'nullable',
                'integer',
                Rule::exists(Branch::class, 'id'),
            ],
            'is_active' => ['required', 'boolean'],
            'password' => [$managedUser ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
