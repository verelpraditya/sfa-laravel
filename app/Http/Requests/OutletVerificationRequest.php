<?php

namespace App\Http\Requests;

use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OutletVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->canVerifyOutlets();
    }

    public function rules(): array
    {
        /** @var Outlet $outlet */
        $outlet = $this->route('outlet');

        return [
            'category' => ['required', Rule::in(['salon', 'toko', 'barbershop', 'lainnya'])],
            'outlet_type' => ['required', Rule::in(['prospek', 'noo', 'pelanggan_lama'])],
            'outlet_status' => ['required', Rule::in(['active', 'inactive'])],
            'official_kode' => [
                Rule::requiredIf($this->input('outlet_type') === 'pelanggan_lama'),
                'nullable',
                'string',
                'max:100',
                Rule::unique(Outlet::class, 'official_kode')->ignore($outlet->id),
            ],
            'verification_status' => ['nullable', Rule::in(['pending', 'verified'])],
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function validatedPayload(Outlet $outlet): array
    {
        $payload = $this->safe()->only([
            'category',
            'outlet_type',
            'outlet_status',
            'official_kode',
            'verification_status',
        ]);

        $payload['verification_status'] = $payload['verification_status'] ?: null;

        $payload['official_kode'] = $payload['official_kode'] ?? null;

        if ($payload['outlet_type'] === 'prospek') {
            $payload['verification_status'] = null;
            $payload['official_kode'] = null;
        }

        if ($payload['outlet_type'] === 'noo' && $payload['verification_status'] === null) {
            $payload['verification_status'] = 'pending';
        }

        if ($payload['outlet_type'] === 'pelanggan_lama' && ! blank($payload['official_kode'])) {
            $payload['verification_status'] = 'verified';
        }

        if (($payload['verification_status'] ?? null) === 'verified') {
            $payload['verified_by'] = $this->user()->id;
            $payload['verified_at'] = now();
        } elseif ($outlet->verification_status === 'verified' && ($payload['verification_status'] ?? null) !== 'verified') {
            $payload['verified_by'] = null;
            $payload['verified_at'] = null;
        }

        if ($payload['outlet_type'] !== 'pelanggan_lama' && blank($payload['official_kode'])) {
            $payload['official_kode'] = null;
        }

        return $payload;
    }
}
