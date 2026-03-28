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
            'outlet_type' => ['required', Rule::in(['prospek', 'noo', 'pelanggan_lama'])],
            'official_kode' => [
                Rule::requiredIf($this->input('outlet_type') === 'pelanggan_lama'),
                'nullable',
                'string',
                'max:100',
                Rule::unique(Outlet::class, 'official_kode')->ignore($outlet?->id),
            ],
            'verification_status' => ['required', Rule::in(['pending', 'verified'])],
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
            'outlet_type',
            'official_kode',
            'verification_status',
        ]);

        if ($payload['verification_status'] === 'verified') {
            $payload['verified_by'] = $this->user()->id;
            $payload['verified_at'] = now();
        } elseif ($outlet?->verification_status === 'verified' && $payload['verification_status'] === 'pending') {
            $payload['verified_by'] = null;
            $payload['verified_at'] = null;
        }

        if (($payload['outlet_type'] ?? null) !== 'pelanggan_lama') {
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
