<x-app-layout>
    @php($submissionToken = old('submission_token', (string) \Illuminate\Support\Str::uuid()))
    <x-slot name="header">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-chip">Kunjungan SMD</span>
            </div>
            <h2 class="mt-4 text-3xl font-semibold leading-tight text-ink-950">Input Aktivitas SMD</h2>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-[1.6rem] border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700 shadow-sm">
                    <p class="font-semibold">Form belum bisa disimpan. Periksa field yang masih bermasalah.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('smd-visits.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="smdVisitForm()" x-init="init()" @submit="handleSubmit($event)">
                @csrf
                <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

                <section class="app-panel app-animate-enter overflow-hidden p-4 sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Alur Input</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Pilih outlet, tandai aktivitas, lalu kirim bukti kunjungan</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <div class="rounded-[1.3rem] border border-sky-100 bg-sky-50 px-3 py-3 text-center shadow-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-sky-700">Step 1</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Outlet</p>
                            </div>
                            <div class="rounded-[1.3rem] border border-slate-200 bg-white px-3 py-3 text-center shadow-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Step 2</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Aktivitas</p>
                            </div>
                            <div class="rounded-[1.3rem] border border-slate-200 bg-white px-3 py-3 text-center shadow-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Step 3</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Bukti</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid items-start gap-6 xl:grid-cols-[1.08fr_0.92fr]">
                    <div class="space-y-6">
                        <section class="app-panel app-animate-enter p-4 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Step 1</p>
                                    <h3 class="mt-2 text-xl font-semibold text-ink-950">Outlet yang dikunjungi</h3>
                                </div>
                                <button type="button" @click="creatingNewOutlet = ! creatingNewOutlet; resetSelection()" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900 shadow-[0_14px_30px_-20px_rgba(14,165,233,0.45)] transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100 sm:w-auto">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-sm shadow-sky-100/80">
                                        <svg x-show="!creatingNewOutlet" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 5v14M5 12h14" /></svg>
                                        <svg x-show="creatingNewOutlet" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 7h10v10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m9 15 6-6" /></svg>
                                    </span>
                                    <span x-text="creatingNewOutlet ? 'Pakai Outlet Existing' : 'Buat Outlet Baru'"></span>
                                </button>
                            </div>

                            <div class="mt-5 app-soft-panel p-4 sm:p-5" x-show="! creatingNewOutlet">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-sky-700 shadow-sm">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                            <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                            <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <x-input-label for="outlet-search" value="Cari outlet existing" />
                                        <p class="mt-1 text-xs text-slate-500">Cari berdasarkan nama outlet atau official kode.</p>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-[1.4rem] border border-slate-200/90 bg-white px-4 py-3.5 shadow-[0_12px_32px_-18px_rgba(15,23,42,0.28)]">
                                    <input id="outlet-search" x-model="query" @input.debounce.300ms="searchOutlets" type="text" placeholder="Ketik nama outlet / kode..." class="w-full border-0 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none">
                                </div>
                                <input type="hidden" name="outlet_id" :value="selectedOutlet?.id || ''">
                                <input type="hidden" name="selected_outlet_name" :value="selectedOutlet?.name || ''">
                                <input type="hidden" name="selected_outlet_district" :value="selectedOutlet?.district || ''">
                                <input type="hidden" name="selected_outlet_city" :value="selectedOutlet?.city || ''">

                                <div class="mt-3 space-y-2 rounded-[1.4rem] border border-slate-200 bg-white p-2 shadow-sm" x-show="loading || results.length > 0 || (query.length > 0 && !selectedOutlet)">
                                    <template x-for="item in results" :key="item.id">
                                        <button type="button" @click="chooseOutlet(item)" class="flex w-full items-start justify-between rounded-[1rem] px-3 py-3 text-left text-sm text-slate-600 transition hover:bg-slate-50">
                                            <div>
                                                <p class="font-semibold text-slate-800" x-text="item.name"></p>
                                                <p class="mt-1 text-xs text-slate-500" x-text="`${item.district}, ${item.city}`"></p>
                                            </div>
                                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700" x-text="item.official_kode || 'Pilih'"></span>
                                        </button>
                                    </template>
                                    <p x-show="!loading && results.length === 0" class="px-3 py-2 text-sm text-slate-400">Outlet belum ditemukan, pindah ke mode outlet baru jika perlu.</p>
                                    <p x-show="loading" class="px-3 py-2 text-sm text-slate-400">Mencari outlet...</p>
                                </div>

                                <div x-show="selectedOutlet" class="mt-4 rounded-[1.5rem] border border-sky-100 bg-white px-4 py-4 shadow-[0_16px_34px_-24px_rgba(14,165,233,0.35)]">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900" x-text="selectedOutlet?.name"></p>
                                            <p class="mt-1 text-xs text-slate-500" x-text="selectedOutlet ? `${selectedOutlet.district}, ${selectedOutlet.city}` : ''"></p>
                                        </div>
                                        <span class="app-chip" x-text="selectedOutlet?.official_kode || 'Belum ada kode'"></span>
                                    </div>
                                </div>

                                <x-input-error class="mt-2" :messages="$errors->get('outlet_id')" />
                            </div>

                            <div class="mt-5 app-soft-panel p-4 sm:p-5" x-show="creatingNewOutlet" x-cloak>
                                <div class="mb-4 flex items-start gap-3">
                                    <div class="mt-1 flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-sky-700 shadow-sm">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Outlet baru</p>
                                        <p class="mt-1 text-xs text-slate-500">Lengkapi data dasar outlet baru yang kamu kunjungi.</p>
                                    </div>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <x-input-label for="new_outlet_name" value="Nama outlet baru" />
                                        <x-text-input id="new_outlet_name" name="new_outlet_name" class="mt-2 block w-full" :value="old('new_outlet_name')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_name')" />
                                    </div>
                                    <div>
                                        <x-input-label for="new_outlet_category" value="Kategori outlet" />
                                        <select id="new_outlet_category" name="new_outlet_category" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                            <option value="salon" @selected(old('new_outlet_category', 'salon') === 'salon')>Salon</option>
                                            <option value="toko" @selected(old('new_outlet_category') === 'toko')>Toko</option>
                                            <option value="barbershop" @selected(old('new_outlet_category') === 'barbershop')>Barbershop</option>
                                            <option value="lainnya" @selected(old('new_outlet_category') === 'lainnya')>Lainnya</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_category')" />
                                    </div>
                                    <div>
                                        <x-input-label for="new_outlet_type" value="Jenis outlet" />
                                        <select id="new_outlet_type" name="new_outlet_type" x-model="newOutletType" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                            <option value="prospek">Prospek</option>
                                            <option value="noo">NOO</option>
                                            <option value="pelanggan_lama">Pelanggan Lama</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_type')" />
                                    </div>
                                    <div class="sm:col-span-2" x-show="newOutletType === 'pelanggan_lama'" x-cloak>
                                        <x-input-label for="new_outlet_official_kode" value="Official kode" />
                                        <x-text-input id="new_outlet_official_kode" name="new_outlet_official_kode" class="mt-2 block w-full" :value="old('new_outlet_official_kode')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_official_kode')" />
                                    </div>
                                    <div>
                                        <x-input-label for="new_outlet_district" value="Kecamatan" />
                                        <x-text-input id="new_outlet_district" name="new_outlet_district" class="mt-2 block w-full" :value="old('new_outlet_district')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_district')" />
                                    </div>
                                    <div>
                                        <x-input-label for="new_outlet_city" value="Kota" />
                                        <x-text-input id="new_outlet_city" name="new_outlet_city" class="mt-2 block w-full" :value="old('new_outlet_city')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_city')" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <x-input-label for="new_outlet_address" value="Alamat outlet" />
                                        <textarea id="new_outlet_address" name="new_outlet_address" rows="4" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">{{ old('new_outlet_address') }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('new_outlet_address')" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="app-panel app-animate-enter p-4 sm:p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_16px_34px_-20px_rgba(29,78,216,0.65)]">2</div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Step 2</p>
                                    <h3 class="mt-1 text-xl font-semibold text-ink-950">Aktivitas & nominal</h3>
                                </div>
                            </div>

                            <div class="mt-5 space-y-3">
                                @foreach (['ambil_po' => 'Ambil PO', 'merapikan_display' => 'Merapikan Display', 'tukar_faktur' => 'Tukar Faktur', 'ambil_tagihan' => 'Ambil Tagihan'] as $value => $label)
                                    <label class="flex items-start gap-3 rounded-[1.5rem] border px-4 py-4 transition" :class="activities.includes('{{ $value }}') ? 'border-sky-300 bg-sky-50 shadow-[0_12px_28px_-20px_rgba(14,165,233,0.45)]' : 'border-slate-200 bg-slate-50 hover:border-sky-200 hover:bg-sky-50/70'">
                                        <input type="checkbox" name="activities[]" value="{{ $value }}" x-model="activities" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(collect(old('activities', []))->contains($value))>
                                        <span>
                                            <span class="block font-semibold text-slate-900">{{ $label }}</span>
                                            <span class="mt-1 block text-sm text-slate-500">Aktivitas akan ikut masuk rekap kunjungan SMD.</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('activities')" />

                            <div class="mt-5 space-y-4">
                                <div class="app-soft-panel p-4 sm:p-5" x-show="activities.includes('ambil_po')" x-transition x-cloak>
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Nominal PO</p>
                                        </div>
                                        <span class="app-chip">Format Rp</span>
                                    </div>
                                    <input id="po_amount_display" type="text" inputmode="numeric" x-model="poAmountDisplay" @input="setCurrency('po', $event.target.value)" placeholder="Rp 0" class="mt-3 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                    <input type="hidden" name="po_amount" :value="poAmountRaw">
                                    <x-input-error class="mt-2" :messages="$errors->get('po_amount')" />
                                </div>

                                <div class="app-soft-panel p-4 sm:p-5" x-show="activities.includes('ambil_tagihan')" x-transition x-cloak>
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Nominal pembayaran</p>
                                        </div>
                                        <span class="app-chip">Format Rp</span>
                                    </div>
                                    <input id="payment_amount_display" type="text" inputmode="numeric" x-model="paymentAmountDisplay" @input="setCurrency('payment', $event.target.value)" placeholder="Rp 0" class="mt-3 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                    <input type="hidden" name="payment_amount" :value="paymentAmountRaw">
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_amount')" />
                                </div>

                                <div class="app-soft-panel p-4 sm:p-5" x-show="activities.includes('merapikan_display')" x-transition x-cloak>
                                    <x-input-label value="Foto display" />
                                    <x-input-error class="mt-2" :messages="$errors->get('display_photos')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('display_photos.*')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('display_photo')" />
                                    <p class="mt-2 text-xs text-slate-500">Ambil foto display satu per satu dari kamera. Maksimal 10 foto.</p>
                                    <div class="mt-3 space-y-3">
                                        <template x-for="(slot, index) in displayPhotoSlots" :key="slot.id">
                                            <div class="rounded-[1.4rem] border border-slate-200 bg-white px-4 py-4 shadow-sm">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900" x-text="slot.ready ? 'Foto ' + (index + 1) + ' siap' : 'Foto ' + (index + 1)"></p>
                                                        <p class="mt-1 text-xs text-slate-500" x-text="slot.ready ? (slot.sizeLabel || 'Siap diupload') : 'Ambil foto dari kamera' "></p>
                                                        <p x-show="slot.status" x-cloak class="mt-2 text-xs font-medium text-sky-700" x-text="slot.status"></p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <label :for="'display_photo_' + slot.id" class="inline-flex cursor-pointer items-center rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700" x-text="slot.ready ? 'Ganti' : 'Kamera'"></label>
                                                        <button type="button" @click="removeDisplayPhotoSlot(slot.id)" x-show="displayPhotoSlots.length > 1 || slot.ready" class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Hapus</button>
                                                    </div>
                                                </div>
                                                <input :id="'display_photo_' + slot.id" name="display_photos[]" type="file" accept="image/*" capture="environment" @change="handleDisplayPhotoSlot($event, slot.id)" class="sr-only">
                                            </div>
                                        </template>

                                        <button type="button" @click="addDisplayPhotoSlot()" x-show="displayPhotoSlots.length < 10" class="inline-flex items-center rounded-2xl border border-dashed border-sky-300 bg-sky-50/70 px-4 py-3 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                                            + Tambah Foto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="space-y-6">
                        <section class="app-panel app-animate-enter p-4 sm:p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_16px_34px_-20px_rgba(29,78,216,0.65)]">3</div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Step 3</p>
                                    <h3 class="mt-1 text-xl font-semibold text-ink-950">Lokasi, foto, dan catatan</h3>
                                </div>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div class="app-soft-panel p-4">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Lokasi kunjungan</p>
                                            <p class="mt-1 text-xs text-slate-500">Tap sekali untuk isi koordinat otomatis.</p>
                                        </div>
                                        <button type="button" @click="fillMockLocation" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900 shadow-[0_14px_30px_-20px_rgba(16,185,129,0.4)] transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-100 sm:w-auto">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-sm shadow-emerald-100/80">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 21c4-4.35 6-7.39 6-10a6 6 0 1 0-12 0c0 2.61 2 5.65 6 10Z" /><circle cx="12" cy="11" r="2.5" stroke-width="1.9" /></svg>
                                            </span>
                                            Ambil Lokasi
                                        </button>
                                    </div>
                                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <x-input-label for="latitude" value="Latitude" />
                                            <x-text-input id="latitude" name="latitude" class="mt-2 block w-full" x-model="latitude" :value="old('latitude')" required />
                                            <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                                        </div>
                                        <div>
                                            <x-input-label for="longitude" value="Longitude" />
                                            <x-text-input id="longitude" name="longitude" class="mt-2 block w-full" x-model="longitude" :value="old('longitude')" required />
                                            <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                                        </div>
                                    </div>
                                </div>

                                <div class="app-soft-panel p-4">
                                    <x-input-label for="visit_photo" value="Foto bukti kunjungan" />
                                    <label for="visit_photo" class="mt-2 flex cursor-pointer items-center justify-between gap-4 rounded-[1.4rem] border border-dashed border-slate-300 bg-white px-4 py-4 text-sm text-slate-500 transition hover:border-sky-300 hover:bg-sky-50/50">
                                        <span>
                                            <span class="block font-semibold text-slate-800">Ambil foto kunjungan</span>
                                            <span class="mt-1 block text-xs text-slate-500">Gunakan foto bukti kunjungan yang jelas.</span>
                                        </span>
                                        <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">Kamera</span>
                                    </label>
                                    <input id="visit_photo" name="visit_photo" type="file" accept="image/*" capture="environment" @change="handleVisitPhoto($event)" class="sr-only">
                                    <x-input-error class="mt-2" :messages="$errors->get('visit_photo')" />
                                    <div x-show="visitPhotoName" class="mt-3 rounded-[1.4rem] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                                        File dipilih: <span class="font-semibold text-slate-900" x-text="visitPhotoName"></span>
                                    </div>
                                    <div x-show="visitPhotoStatus" x-cloak class="mt-3 rounded-[1.2rem] border border-sky-100 bg-sky-50 px-4 py-3 text-xs font-medium text-sky-700" x-text="visitPhotoStatus"></div>
                                    <div x-show="visitPhotoPreviewUrl" x-cloak class="mt-4 overflow-hidden rounded-[1.4rem] border border-slate-200 bg-white">
                                        <img :src="visitPhotoPreviewUrl" alt="Preview foto kunjungan" class="h-56 w-full object-cover">
                                    </div>
                                </div>

                                <div class="app-soft-panel p-4">
                                    <x-input-label for="notes" value="Catatan" />
                                    <textarea id="notes" name="notes" rows="4" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-4 focus:ring-brand-100" placeholder="Tambahkan catatan singkat jika perlu">{{ old('notes') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                                </div>
                            </div>
                        </section>

                        <section class="app-panel app-animate-enter overflow-hidden p-4 sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ringkasan Cepat</p>
                            <div class="mt-4 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-3 rounded-[1.2rem] bg-slate-50 px-4 py-3">
                                    <span class="text-slate-500">Mode outlet</span>
                                    <span class="font-semibold text-slate-900" x-text="creatingNewOutlet ? 'Outlet baru' : 'Outlet existing'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-[1.2rem] bg-slate-50 px-4 py-3">
                                    <span class="text-slate-500">Aktivitas dipilih</span>
                                    <span class="font-semibold text-slate-900" x-text="activities.length"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-[1.2rem] bg-slate-50 px-4 py-3" x-show="activities.includes('ambil_po')" x-cloak>
                                    <span class="text-slate-500">Nominal PO</span>
                                    <span class="font-semibold text-slate-900" x-text="poAmountDisplay || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-[1.2rem] bg-slate-50 px-4 py-3" x-show="activities.includes('ambil_tagihan')" x-cloak>
                                    <span class="text-slate-500">Pembayaran</span>
                                    <span class="font-semibold text-slate-900" x-text="paymentAmountDisplay || 'Rp 0'"></span>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>

                <div class="sticky bottom-4 z-20 flex justify-end">
                    <div class="w-full rounded-[1.75rem] border border-white/80 bg-white/92 p-3 shadow-[0_18px_40px_-22px_rgba(15,23,42,0.38)] backdrop-blur sm:w-auto">
                        <x-primary-button class="w-full justify-center sm:min-w-[260px]" x-bind:disabled="submitting">
                            <span x-show="!submitting">Simpan Kunjungan SMD</span>
                            <span x-show="submitting" x-cloak>Menyimpan...</span>
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function smdVisitForm() {
                return {
                    query: @js(old('selected_outlet_name', '')),
                    results: [],
                    loading: false,
                    creatingNewOutlet: {{ old('new_outlet_name') ? 'true' : 'false' }},
                    selectedOutlet: @js(old('outlet_id') ? ['id' => (int) old('outlet_id'), 'name' => old('selected_outlet_name'), 'district' => old('selected_outlet_district'), 'city' => old('selected_outlet_city')] : null),
                    newOutletType: '{{ old('new_outlet_type', 'prospek') }}',
                    activities: @json(old('activities', [])),
                    latitude: '{{ old('latitude') }}',
                    longitude: '{{ old('longitude') }}',
                    visitPhotoName: '',
                    visitPhotoStatus: '',
                    visitPhotoPreviewUrl: null,
                    displayPhotoSlots: [],
                    nextDisplayPhotoSlotId: 1,
                    submitting: false,
                    poAmountRaw: '{{ old('po_amount') }}',
                    paymentAmountRaw: '{{ old('payment_amount') }}',
                    poAmountDisplay: '',
                    paymentAmountDisplay: '',
                    init() {
                        this.poAmountDisplay = this.formatRupiah(this.poAmountRaw);
                        this.paymentAmountDisplay = this.formatRupiah(this.paymentAmountRaw);

                        if (this.selectedOutlet && ! this.query) {
                            this.query = this.selectedOutlet.name || '';
                        }

                        this.addDisplayPhotoSlot();
                    },
                    handleSubmit(event) {
                        if (this.submitting) {
                            event.preventDefault();
                            return;
                        }

                        this.submitting = true;
                    },
                    formatRupiah(value) {
                        const digits = String(value || '').replace(/\D/g, '');

                        if (!digits) {
                            return '';
                        }

                        return `Rp ${new Intl.NumberFormat('id-ID').format(Number(digits))}`;
                    },
                    normalizeDigits(value) {
                        return String(value || '').replace(/\D/g, '');
                    },
                    setCurrency(field, value) {
                        const digits = this.normalizeDigits(value);
                        const formatted = this.formatRupiah(digits);

                        if (field === 'po') {
                            this.poAmountRaw = digits;
                            this.poAmountDisplay = formatted;
                            return;
                        }

                        this.paymentAmountRaw = digits;
                        this.paymentAmountDisplay = formatted;
                    },
                    async searchOutlets() {
                        if (this.query.trim().length === 0) {
                            this.results = [];
                            return;
                        }

                        this.loading = true;

                        try {
                            const response = await fetch(`{{ route('ajax.outlets.search') }}?q=${encodeURIComponent(this.query)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            });

                            const payload = await response.json();
                            this.results = payload.data || [];
                        } catch (error) {
                            this.results = [];
                        } finally {
                            this.loading = false;
                        }
                    },
                    chooseOutlet(item) {
                        this.selectedOutlet = item;
                        this.query = item.name;
                        this.results = [];
                        this.creatingNewOutlet = false;
                    },
                    resetSelection() {
                        this.selectedOutlet = null;
                        this.query = '';
                        this.results = [];
                    },
                    fillMockLocation() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.latitude = position.coords.latitude.toFixed(7);
                                this.longitude = position.coords.longitude.toFixed(7);
                            }, () => {
                                this.latitude = '-6.9175000';
                                this.longitude = '107.6191000';
                            });

                            return;
                        }

                        this.latitude = '-6.9175000';
                        this.longitude = '107.6191000';
                    },
                    async handleVisitPhoto(event) {
                        await this.handleCompressedUpload(event, 'visit');
                    },
                    addDisplayPhotoSlot() {
                        if (this.displayPhotoSlots.length >= 10) {
                            return;
                        }

                        this.displayPhotoSlots.push({
                            id: this.nextDisplayPhotoSlotId++,
                            ready: false,
                            sizeLabel: '',
                            status: '',
                        });
                    },
                    removeDisplayPhotoSlot(slotId) {
                        this.displayPhotoSlots = this.displayPhotoSlots.filter((slot) => slot.id !== slotId);

                        if (this.displayPhotoSlots.length === 0) {
                            this.addDisplayPhotoSlot();
                        }
                    },
                    async handleDisplayPhotoSlot(event, slotId) {
                        const input = event.target;
                        const file = input.files?.[0];
                        const slot = this.displayPhotoSlots.find((item) => item.id === slotId);

                        if (! slot) {
                            return;
                        }

                        if (! file) {
                            slot.ready = false;
                            slot.sizeLabel = '';
                            slot.status = '';
                            return;
                        }

                        slot.status = 'Memproses foto...';

                        try {
                            const compressed = await this.compressImage(file, {
                                maxWidth: 1600,
                                maxHeight: 1600,
                                quality: 0.82,
                            });

                            const transfer = new DataTransfer();
                            transfer.items.add(compressed);
                            input.files = transfer.files;

                            slot.ready = true;
                            slot.sizeLabel = this.formatFileSize(compressed.size);
                            slot.status = '';
                        } catch (error) {
                            slot.ready = true;
                            slot.sizeLabel = this.formatFileSize(file.size);
                            slot.status = '';
                        }
                    },
                    async handleCompressedUpload(event, kind) {
                        const input = event.target;
                        const file = input.files?.[0];

                        if (! file) {
                            if (kind === 'visit') {
                                this.visitPhotoName = '';
                                this.visitPhotoStatus = '';
                                this.visitPhotoPreviewUrl = null;
                            }
                            return;
                        }

                        if (kind === 'visit') {
                            this.visitPhotoStatus = 'Memproses foto...';
                        }

                        try {
                            const compressed = await this.compressImage(file, {
                                maxWidth: 1600,
                                maxHeight: 1600,
                                quality: 0.82,
                            });

                            const transfer = new DataTransfer();
                            transfer.items.add(compressed);
                            input.files = transfer.files;

                            if (kind === 'visit') {
                                this.visitPhotoName = compressed.name;
                                this.visitPhotoStatus = `Foto siap diupload (${this.formatFileSize(compressed.size)})`;
                                this.setPreviewUrl(compressed, 'visit');
                            }
                        } catch (error) {
                            if (kind === 'visit') {
                                this.visitPhotoName = file.name;
                                this.visitPhotoStatus = 'Foto asli akan diupload.';
                                this.setPreviewUrl(file, 'visit');
                            }
                        }
                    },
                    compressImage(file, options = {}) {
                        const maxWidth = options.maxWidth || 1600;
                        const maxHeight = options.maxHeight || 1600;
                        const quality = options.quality || 0.82;

                        return new Promise((resolve, reject) => {
                            if (! file.type.startsWith('image/')) {
                                reject(new Error('Unsupported file type.'));
                                return;
                            }

                            const reader = new FileReader();

                            reader.onerror = () => reject(new Error('Failed to read file.'));
                            reader.onload = () => {
                                const image = new Image();

                                image.onerror = () => reject(new Error('Failed to load image.'));
                                image.onload = () => {
                                    let { width, height } = image;
                                    const ratio = Math.min(maxWidth / width, maxHeight / height, 1);

                                    width = Math.round(width * ratio);
                                    height = Math.round(height * ratio);

                                    const canvas = document.createElement('canvas');
                                    canvas.width = width;
                                    canvas.height = height;

                                    const context = canvas.getContext('2d');

                                    if (! context) {
                                        reject(new Error('Canvas unavailable.'));
                                        return;
                                    }

                                    context.drawImage(image, 0, 0, width, height);
                                    canvas.toBlob((blob) => {
                                        if (! blob) {
                                            reject(new Error('Compression failed.'));
                                            return;
                                        }

                                        const originalName = file.name.replace(/\.[^.]+$/, '');
                                        const compressedFile = new File([blob], `${originalName}.jpg`, {
                                            type: 'image/jpeg',
                                            lastModified: Date.now(),
                                        });

                                        resolve(compressedFile.size < file.size ? compressedFile : file);
                                    }, 'image/jpeg', quality);
                                };

                                image.src = reader.result;
                            };

                            reader.readAsDataURL(file);
                        });
                    },
                    formatFileSize(size) {
                        if (size < 1024) {
                            return `${size} B`;
                        }

                        if (size < 1048576) {
                            return `${(size / 1024).toFixed(0)} KB`;
                        }

                        return `${(size / 1048576).toFixed(2)} MB`;
                    },
                    setPreviewUrl(file, kind) {
                        const currentUrl = kind === 'visit' ? this.visitPhotoPreviewUrl : null;

                        if (currentUrl) {
                            URL.revokeObjectURL(currentUrl);
                        }

                        const nextUrl = URL.createObjectURL(file);

                        if (kind === 'visit') {
                            this.visitPhotoPreviewUrl = nextUrl;
                            return;
                        }
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
