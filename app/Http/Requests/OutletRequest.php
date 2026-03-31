<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OutletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var Outlet|null $outlet */
        $outlet = $this->route('outlet');

        return [
            'branch_id' => [
                Rule::requiredIf($this->user()->isAdminPusat()),
                'nullable',
                'integer',
                Rule::exists(Branch::class, 'id'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'district' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['salon', 'toko', 'barbershop', 'lainnya'])],
            'outlet_status' => ['required', Rule::in(['prospek', 'pending', 'active', 'inactive'])],
            'official_kode' => [
                Rule::requiredIf($this->input('outlet_status') === 'active'),
                'nullable',
                'string',
                'max:100',
                Rule::unique(Outlet::class, 'official_kode')->ignore($outlet?->id),
            ],
        ];
    }

    public function validatedPayload(?Outlet $outlet = null): array
    {
        $payload = $this->safe()->only([
            'name',
            'address',
            'district',
            'city',
            'category',
            'outlet_status',
            'official_kode',
        ]);

        $payload['official_kode'] = $payload['official_kode'] ?? null;

        if ($payload['outlet_status'] === 'active' && ! blank($payload['official_kode'])) {
            $payload['verified_by'] = $this->user()->id;
            $payload['verified_at'] = now();
        } elseif ($outlet?->verified_by && $payload['outlet_status'] !== 'active') {
            $payload['verified_by'] = null;
            $payload['verified_at'] = null;
        }

        if ($payload['outlet_status'] !== 'active') {
            $payload['official_kode'] = $payload['official_kode'] ?: null;
        }

        return $payload;
    }

    public function resolvedBranchId(?Outlet $outlet = null): int
    {
        if (! $this->user()->isAdminPusat()) {
            return (int) $this->user()->branch_id;
        }

        return (int) ($this->integer('branch_id') ?: $outlet?->branch_id);
    }
}
