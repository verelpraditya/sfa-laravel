<?php

namespace App\Http\Requests;

use App\Models\Outlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        if ($user->isAdminPusat()) {
            return true;
        }

        if ($user->isSupervisor()) {
            return $this->route('visit')->branch_id === $user->branch_id;
        }

        return false;
    }

    public function rules(): array
    {
        $visit = $this->route('visit');
        $isSales = $visit->visit_type === 'sales';

        $rules = [
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
            'visited_at' => ['required', 'date', 'before_or_equal:now'],
            'outlet_condition' => ['required', Rule::in(['buka', 'tutup', 'order_by_wa'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($isSales) {
            $rules['order_amount'] = ['nullable', 'numeric', 'min:0'];
            $rules['receivable_amount'] = ['nullable', 'numeric', 'min:0'];
        } else {
            $rules['po_amount'] = ['nullable', 'numeric', 'min:0'];
            $rules['payment_amount'] = ['nullable', 'numeric', 'min:0'];
            $rules['activities'] = ['required', 'array', 'min:1'];
            $rules['activities.*'] = [Rule::in(['ambil_po', 'merapikan_display', 'tukar_faktur', 'ambil_tagihan'])];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'outlet_id.required' => 'Outlet wajib dipilih.',
            'outlet_id.exists' => 'Outlet tidak ditemukan.',
            'visited_at.required' => 'Tanggal kunjungan wajib diisi.',
            'visited_at.date' => 'Format tanggal tidak valid.',
            'visited_at.before_or_equal' => 'Tanggal kunjungan tidak boleh di masa depan.',
            'outlet_condition.required' => 'Kondisi outlet wajib dipilih.',
            'outlet_condition.in' => 'Kondisi outlet tidak valid.',
            'order_amount.numeric' => 'Nominal order harus berupa angka.',
            'order_amount.min' => 'Nominal order tidak boleh kurang dari 0.',
            'receivable_amount.numeric' => 'Total tagihan harus berupa angka.',
            'receivable_amount.min' => 'Total tagihan tidak boleh kurang dari 0.',
            'po_amount.numeric' => 'Nominal PO harus berupa angka.',
            'po_amount.min' => 'Nominal PO tidak boleh kurang dari 0.',
            'payment_amount.numeric' => 'Nominal pembayaran harus berupa angka.',
            'payment_amount.min' => 'Nominal pembayaran tidak boleh kurang dari 0.',
            'activities.required' => 'Pilih minimal satu aktivitas SMD.',
            'activities.min' => 'Pilih minimal satu aktivitas SMD.',
            'activities.*.in' => 'Ada aktivitas SMD yang tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
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
            }

            $visit = $this->route('visit');

            if ($visit->visit_type === 'sales') {
                if ($this->input('outlet_condition') === 'tutup' && ($this->filled('order_amount') || $this->filled('receivable_amount'))) {
                    $validator->errors()->add('order_amount', 'Nominal order dan tagihan hanya boleh diisi saat outlet buka atau order by WA.');
                }
            } else {
                $activities = collect($this->input('activities', []));

                if ($activities->contains('ambil_po') && ! $this->filled('po_amount')) {
                    $validator->errors()->add('po_amount', 'Nominal PO wajib diisi jika aktivitas Ambil PO dipilih.');
                }

                if ($activities->contains('ambil_tagihan') && ! $this->filled('payment_amount')) {
                    $validator->errors()->add('payment_amount', 'Nominal pembayaran wajib diisi jika aktivitas Ambil Tagihan dipilih.');
                }
            }
        });
    }
}
