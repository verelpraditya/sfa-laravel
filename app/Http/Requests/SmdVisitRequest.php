<?php

namespace App\Http\Requests;

use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmdVisitRequest extends FormRequest
{
    private ?Outlet $existingOutlet = null;

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->canCreateSmdVisit();
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
                Rule::unique(Outlet::class, 'official_kode'),
            ],
            'activities' => ['required', 'array', 'min:1'],
            'activities.*' => [Rule::in(['ambil_po', 'merapikan_display', 'tukar_faktur', 'ambil_tagihan'])],
            'po_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'display_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
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

            if ($this->input('new_outlet_type') === 'pelanggan_lama' && blank($this->input('new_outlet_official_kode'))) {
                $validator->errors()->add('new_outlet_official_kode', 'Official kode wajib untuk outlet pelanggan lama.');
            }

            $activities = collect($this->input('activities', []));

            if ($activities->contains('ambil_po') && ! $this->filled('po_amount')) {
                $validator->errors()->add('po_amount', 'Nominal PO wajib diisi jika aktivitas Ambil PO dipilih.');
            }

            if ($activities->contains('ambil_tagihan') && ! $this->filled('payment_amount')) {
                $validator->errors()->add('payment_amount', 'Nominal pembayaran wajib diisi jika aktivitas Ambil Tagihan dipilih.');
            }

            if ($activities->contains('merapikan_display') && ! $this->hasFile('display_photo')) {
                $validator->errors()->add('display_photo', 'Foto display wajib diupload jika aktivitas Merapikan Display dipilih.');
            }
        });
    }

    public function existingOutlet(): ?Outlet
    {
        return $this->existingOutlet;
    }
}
