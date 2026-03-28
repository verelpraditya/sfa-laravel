<?php

namespace App\Http\Requests;

use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesVisitRequest extends FormRequest
{
    private ?Outlet $existingOutlet = null;

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->canCreateSalesVisit();
    }

    public function rules(): array
    {
        $creatingNewOutlet = ! $this->filled('outlet_id');

        return [
            'outlet_id' => ['nullable', 'integer'],
            'existing_outlet_type' => ['nullable', Rule::in(['prospek', 'noo', 'pelanggan_lama'])],
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
                Rule::unique(Outlet::class, 'official_kode'),
            ],
            'outlet_condition' => ['required', Rule::in(['buka', 'tutup'])],
            'order_amount' => ['nullable', 'numeric', 'min:0'],
            'receivable_amount' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'visit_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:1000'],
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

            if ($this->input('existing_outlet_type') === 'pelanggan_lama' && blank($this->existingOutlet?->official_kode)) {
                $validator->errors()->add('existing_outlet_type', 'Perubahan ke Pelanggan Lama harus dilakukan supervisor saat official kode sudah tersedia.');
            }

            if ($this->input('outlet_condition') === 'tutup' && ($this->filled('order_amount') || $this->filled('receivable_amount'))) {
                $validator->errors()->add('order_amount', 'Nominal order dan tagihan hanya boleh diisi saat outlet buka.');
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
}
