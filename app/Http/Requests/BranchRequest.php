<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdminPusat();
    }

    public function rules(): array
    {
        /** @var Branch|null $branch */
        $branch = $this->route('branch');

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique(Branch::class, 'code')->ignore($branch?->id)],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'timezone' => ['required', Rule::in(array_keys(Branch::timezoneOptions()))],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
