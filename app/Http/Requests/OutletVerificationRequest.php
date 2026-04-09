<?php

namespace App\Http\Requests;

use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OutletVerificationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'official_kode' => $this->normalizeOfficialKode($this->input('official_kode')),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->canVerifyOutlets();
    }

    public function rules(): array
    {
        /** @var Outlet $outlet */
        $outlet = $this->route('outlet');

        return [
            'official_kode' => [
                'required',
                'string',
                'max:100',
                'regex:/^\S+$/',
                Rule::unique(Outlet::class, 'official_kode')->ignore($outlet->id),
            ],
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'official_kode.required' => 'Official kode wajib diisi saat outlet akan diaktifkan.',
            'official_kode.regex' => 'Official kode tidak boleh mengandung spasi.',
            'official_kode.unique' => 'Official kode sudah dipakai outlet lain.',
            'verification_notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    public function validatedPayload(Outlet $outlet): array
    {
        return [
            'official_kode' => $this->string('official_kode')->toString(),
            'outlet_status' => 'active',
            'verified_by' => $this->user()->id,
            'verified_at' => now(),
        ];
    }

    private function normalizeOfficialKode(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtoupper(preg_replace('/\s+/', '', $value) ?? '');

        return $normalized !== '' ? $normalized : null;
    }
}
