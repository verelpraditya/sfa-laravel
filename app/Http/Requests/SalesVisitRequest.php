<?php

namespace App\Http\Requests;

use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesVisitRequest extends FormRequest
{
    private ?Outlet $existingOutlet = null;

    protected function prepareForValidation(): void
    {
        $this->merge([
            'new_outlet_official_kode' => $this->normalizeOfficialKode($this->input('new_outlet_official_kode')),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->canCreateSalesVisit();
    }

    public function rules(): array
    {
        $creatingNewOutlet = ! $this->filled('outlet_id');

        return [
            'outlet_id' => ['nullable', 'integer'],
            'new_outlet_name' => [Rule::requiredIf($creatingNewOutlet), 'nullable', 'string', 'max:255'],
            'new_outlet_address' => [Rule::requiredIf($creatingNewOutlet), 'nullable', 'string'],
            'new_outlet_district' => [Rule::requiredIf($creatingNewOutlet), 'nullable', 'string', 'max:255'],
            'new_outlet_city' => [Rule::requiredIf($creatingNewOutlet), 'nullable', 'string', 'max:255'],
            'new_outlet_category' => [Rule::requiredIf($creatingNewOutlet), 'nullable', Rule::in(['salon', 'toko', 'barbershop', 'lainnya'])],
            'new_outlet_type' => [Rule::requiredIf($creatingNewOutlet), 'nullable', Rule::in(['prospek', 'noo', 'pelanggan_lama'])],
            'new_outlet_official_kode' => [
                Rule::requiredIf($creatingNewOutlet && $this->input('new_outlet_type') === 'pelanggan_lama'),
                'nullable',
                'string',
                'max:100',
                'regex:/^\S+$/',
                Rule::unique(Outlet::class, 'official_kode'),
            ],
            'outlet_condition' => ['required', Rule::in(['buka', 'tutup', 'order_by_wa'])],
            'order_amount' => ['nullable', 'numeric', 'min:0'],
            'receivable_amount' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'visit_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_outlet_name.required' => 'Nama outlet baru wajib diisi.',
            'new_outlet_address.required' => 'Alamat outlet baru wajib diisi.',
            'new_outlet_district.required' => 'Kecamatan outlet baru wajib diisi.',
            'new_outlet_city.required' => 'Kota outlet baru wajib diisi.',
            'new_outlet_category.required' => 'Kategori outlet baru wajib dipilih.',
            'new_outlet_category.in' => 'Kategori outlet baru tidak valid.',
            'new_outlet_type.required' => 'Jenis outlet baru wajib dipilih.',
            'new_outlet_type.in' => 'Jenis outlet baru tidak valid.',
            'new_outlet_official_kode.required' => 'Official kode wajib diisi untuk pelanggan lama.',
            'new_outlet_official_kode.regex' => 'Official kode tidak boleh mengandung spasi.',
            'new_outlet_official_kode.unique' => 'Official kode sudah dipakai outlet lain.',
            'outlet_condition.required' => 'Pilih kondisi outlet terlebih dahulu.',
            'outlet_condition.in' => 'Kondisi outlet tidak valid.',
            'order_amount.numeric' => 'Nominal order harus berupa angka.',
            'order_amount.min' => 'Nominal order tidak boleh kurang dari 0.',
            'receivable_amount.numeric' => 'Total tagihan harus berupa angka.',
            'receivable_amount.min' => 'Total tagihan tidak boleh kurang dari 0.',
            'latitude.required' => 'Lokasi kunjungan wajib diambil.',
            'latitude.numeric' => 'Latitude tidak valid.',
            'latitude.between' => 'Latitude tidak valid.',
            'longitude.required' => 'Lokasi kunjungan wajib diambil.',
            'longitude.numeric' => 'Longitude tidak valid.',
            'longitude.between' => 'Longitude tidak valid.',
            'visit_photo.required' => 'Foto bukti kunjungan wajib diambil.',
            'visit_photo.image' => 'File bukti kunjungan harus berupa gambar.',
            'visit_photo.mimes' => 'Foto bukti kunjungan harus berformat JPG, JPEG, PNG, atau WEBP.',
            'visit_photo.max' => 'Ukuran foto bukti kunjungan maksimal 3 MB.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'new_outlet_name' => 'nama outlet baru',
            'new_outlet_address' => 'alamat outlet baru',
            'new_outlet_district' => 'kecamatan outlet baru',
            'new_outlet_city' => 'kota outlet baru',
            'new_outlet_category' => 'kategori outlet baru',
            'new_outlet_type' => 'jenis outlet baru',
            'new_outlet_official_kode' => 'official kode',
            'outlet_condition' => 'kondisi outlet',
            'order_amount' => 'nominal order',
            'receivable_amount' => 'total tagihan',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'visit_photo' => 'foto bukti kunjungan',
            'notes' => 'catatan',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $user = $this->user();

            if ($this->filled('outlet_id')) {
                $outlet = Outlet::find($this->integer('outlet_id'));

                if (! $outlet || (! $user->isAdminPusat() && $outlet->branch_id !== $user->branch_id)) {
                    $validator->errors()->add('outlet_id', 'Outlet tidak ditemukan untuk cabang kamu.');
                }

                $this->existingOutlet = $outlet;
            }

            if ($this->input('outlet_condition') === 'tutup' && ($this->filled('order_amount') || $this->filled('receivable_amount'))) {
                $validator->errors()->add('order_amount', 'Nominal order dan tagihan hanya boleh diisi saat outlet buka atau order by WA.');
            }

            if ($this->input('outlet_condition') === 'buka' && ! $this->filled('order_amount') && ! $this->filled('receivable_amount')) {
                return;
            }
        });
    }

    public function existingOutlet(): ?Outlet
    {
        return $this->existingOutlet;
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
