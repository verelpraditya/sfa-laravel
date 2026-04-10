@php
    $isSales = $visit->visit_type === 'sales';
    $activities = $visit->smdActivities->pluck('activity_type')->toArray();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">Edit Kunjungan</span>
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">{{ $visit->typeLabel() }}</span>
                    </div>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">Edit Data Kunjungan</h2>
                    <p class="mt-1 text-sm text-white/70">Foto dan GPS tidak dapat diubah — data lapangan tetap asli.</p>
                </div>
                <a href="{{ route('visit-history.show', $visit) }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    Kembali ke Detail
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Toast notification --}}
            @if (session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                     class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('visit-history.update', $visit) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Outlet Selection --}}
                <div class="app-panel app-animate-enter overflow-hidden">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <h3 class="text-sm font-bold text-slate-800">Outlet & Waktu</h3>
                        </div>
                    </div>
                    <div class="space-y-5 px-5 py-5">
                        <div>
                            <x-input-label for="outlet_id" value="Outlet" />
                            <select id="outlet_id" name="outlet_id" class="app-select mt-2 block w-full" required>
                                <option value="">Pilih outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" @selected((int) old('outlet_id', $visit->outlet_id) === $outlet->id)>
                                        {{ $outlet->name }}{{ $outlet->official_kode ? ' ('.$outlet->official_kode.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('outlet_id')" />
                        </div>

                        <div>
                            <x-input-label for="visited_at" value="Tanggal & Waktu Kunjungan" />
                            <x-text-input id="visited_at" name="visited_at" type="datetime-local" class="mt-2 block w-full"
                                :value="old('visited_at', $visit->visited_at?->format('Y-m-d\TH:i'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('visited_at')" />
                        </div>

                        <div>
                            <x-input-label for="outlet_condition" value="Kondisi Outlet" />
                            <select id="outlet_condition" name="outlet_condition" class="app-select mt-2 block w-full" required>
                                <option value="buka" @selected(old('outlet_condition', $visit->outlet_condition) === 'buka')>Buka</option>
                                <option value="tutup" @selected(old('outlet_condition', $visit->outlet_condition) === 'tutup')>Tutup</option>
                                <option value="order_by_wa" @selected(old('outlet_condition', $visit->outlet_condition) === 'order_by_wa')>Order by WA</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('outlet_condition')" />
                        </div>
                    </div>
                </div>

                {{-- Amounts --}}
                <div class="app-panel app-animate-enter overflow-hidden">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="text-sm font-bold text-slate-800">Nominal</h3>
                        </div>
                    </div>
                    <div class="space-y-5 px-5 py-5">
                        @if ($isSales)
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="order_amount" value="Order Amount (Rp)" />
                                    <x-text-input id="order_amount" name="order_amount" type="number" step="1" min="0" class="mt-2 block w-full"
                                        :value="old('order_amount', $visit->salesDetail?->order_amount ?? 0)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('order_amount')" />
                                </div>
                                <div>
                                    <x-input-label for="receivable_amount" value="Receivable / Tagihan (Rp)" />
                                    <x-text-input id="receivable_amount" name="receivable_amount" type="number" step="1" min="0" class="mt-2 block w-full"
                                        :value="old('receivable_amount', $visit->salesDetail?->receivable_amount ?? 0)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('receivable_amount')" />
                                </div>
                            </div>
                        @else
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="po_amount" value="PO Amount (Rp)" />
                                    <x-text-input id="po_amount" name="po_amount" type="number" step="1" min="0" class="mt-2 block w-full"
                                        :value="old('po_amount', $visit->smdDetail?->po_amount ?? 0)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('po_amount')" />
                                </div>
                                <div>
                                    <x-input-label for="payment_amount" value="Payment Amount (Rp)" />
                                    <x-text-input id="payment_amount" name="payment_amount" type="number" step="1" min="0" class="mt-2 block w-full"
                                        :value="old('payment_amount', $visit->smdDetail?->payment_amount ?? 0)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_amount')" />
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SMD Activities (only for SMD visits) --}}
                @unless ($isSales)
                    <div class="app-panel app-animate-enter overflow-hidden">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <h3 class="text-sm font-bold text-slate-800">Aktivitas SMD</h3>
                            </div>
                        </div>
                        <div class="px-5 py-5">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach (['ambil_po' => 'Ambil PO', 'merapikan_display' => 'Merapikan Display', 'tukar_faktur' => 'Tukar Faktur', 'ambil_tagihan' => 'Ambil Tagihan'] as $value => $label)
                                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 transition hover:border-violet-300 hover:bg-violet-50 has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                                        <input type="checkbox" name="activities[]" value="{{ $value }}"
                                            @checked(in_array($value, old('activities', $activities)))
                                            class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500/30" />
                                        <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('activities')" />
                        </div>
                    </div>
                @endunless

                {{-- Notes --}}
                <div class="app-panel app-animate-enter overflow-hidden">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <h3 class="text-sm font-bold text-slate-800">Catatan</h3>
                        </div>
                    </div>
                    <div class="px-5 py-5">
                        <textarea id="notes" name="notes" rows="4" class="app-textarea block w-full" placeholder="Catatan tambahan (opsional)">{{ old('notes', $visit->notes) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>
                </div>

                {{-- Read-only info: foto & GPS --}}
                <div class="app-panel app-animate-enter overflow-hidden opacity-75">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <h3 class="text-sm font-bold text-slate-800">Data Lapangan (Tidak Bisa Diubah)</h3>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-50">
                        <div class="flex items-center gap-4 px-5 py-3.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Koordinat GPS</p>
                                <p class="mt-0.5 font-mono text-xs font-semibold text-slate-600">{{ $visit->latitude }}, {{ $visit->longitude }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 px-5 py-3.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Foto Bukti</p>
                                <p class="mt-0.5 text-xs font-semibold text-slate-600">{{ $visit->visit_photo_path ? 'Tersimpan' : 'Tidak ada' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 px-5 py-3.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Dikunjungi oleh</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-600">{{ $visit->user?->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="app-action-primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('visit-history.show', $visit) }}" class="app-action-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
