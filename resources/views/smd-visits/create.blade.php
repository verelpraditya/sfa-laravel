<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kunjungan SMD</p>
            <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Input Aktivitas SMD</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Pilih outlet existing atau buat outlet baru, lalu isi aktivitas lapangan beserta bukti kunjungan.</p>
        </div>
    </x-slot>

    <div class="py-4 sm:py-5">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('smd-visits.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="smdVisitForm()">
                @csrf

                <section class="app-panel p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Outlet</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Pilih outlet existing atau buat baru</h3>
                        </div>
                        <button type="button" @click="creatingNewOutlet = ! creatingNewOutlet; resetSelection()" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">
                            <span x-text="creatingNewOutlet ? 'Pakai Outlet Existing' : 'Outlet Baru'"></span>
                        </button>
                    </div>

                    <div class="app-soft-panel mt-4 p-4" x-show="! creatingNewOutlet">
                        <x-input-label for="outlet-search" value="Cari outlet" />
                        <div class="mt-2 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                            </svg>
                            <input id="outlet-search" x-model="query" @input.debounce.300ms="searchOutlets" type="text" placeholder="Ketik kode/nama outlet..." class="w-full border-0 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none">
                        </div>
                        <input type="hidden" name="outlet_id" :value="selectedOutlet?.id || ''">

                        <div class="mt-3 space-y-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" x-show="loading || results.length > 0 || (query.length > 0 && !selectedOutlet)">
                            <template x-for="item in results" :key="item.id">
                                <button type="button" @click="chooseOutlet(item)" class="flex w-full items-start justify-between rounded-xl px-3 py-2 text-left text-sm text-slate-600 transition hover:bg-slate-50">
                                    <div>
                                        <p class="font-semibold text-slate-800" x-text="item.name"></p>
                                        <p class="mt-1 text-xs text-slate-500" x-text="`${item.district}, ${item.city}`"></p>
                                    </div>
                                    <span class="text-xs font-semibold text-sky-700" x-text="item.official_kode || 'Pilih'"></span>
                                </button>
                            </template>
                            <p x-show="!loading && results.length === 0" class="px-3 py-2 text-sm text-slate-400">Outlet belum ditemukan, kamu bisa ganti ke mode outlet baru.</p>
                            <p x-show="loading" class="px-3 py-2 text-sm text-slate-400">Mencari outlet...</p>
                        </div>

                        <div x-show="selectedOutlet" class="mt-4 rounded-2xl border border-sky-100 bg-white px-4 py-4 text-sm text-slate-600 shadow-[0_12px_30px_-20px_rgba(14,165,233,0.35)]">
                            <p class="font-semibold text-slate-900" x-text="selectedOutlet?.name"></p>
                            <p class="mt-1" x-text="selectedOutlet ? `${selectedOutlet.district}, ${selectedOutlet.city}` : ''"></p>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-sky-700" x-text="selectedOutlet?.official_kode || 'Belum ada official kode'"></p>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('outlet_id')" />
                    </div>

                    <div class="app-soft-panel mt-4 p-4" x-show="creatingNewOutlet">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-input-label for="new_outlet_name" value="Nama outlet baru" />
                                <x-text-input id="new_outlet_name" name="new_outlet_name" class="mt-2 block w-full" :value="old('new_outlet_name')" />
                                <x-input-error class="mt-2" :messages="$errors->get('new_outlet_name')" />
                            </div>
                            <div>
                                <x-input-label for="new_outlet_category" value="Kategori outlet" />
                                <select id="new_outlet_category" name="new_outlet_category" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                    <option value="salon">Salon</option>
                                    <option value="toko">Toko</option>
                                    <option value="barbershop">Barbershop</option>
                                    <option value="lainnya">Lainnya</option>
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
                            <div class="sm:col-span-2" x-show="newOutletType === 'pelanggan_lama'">
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

                <section class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div class="app-panel p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aktivitas SMD</p>
                        <div class="mt-4 space-y-3">
                            @foreach (['ambil_po' => 'Ambil PO', 'merapikan_display' => 'Merapikan Display', 'tukar_faktur' => 'Tukar Faktur', 'ambil_tagihan' => 'Ambil Tagihan'] as $value => $label)
                                <label class="flex items-start gap-3 rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-sky-200 hover:bg-sky-50/70">
                                    <input type="checkbox" name="activities[]" value="{{ $value }}" x-model="activities" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(collect(old('activities', []))->contains($value))>
                                    <span>
                                        <span class="block font-semibold text-slate-900">{{ $label }}</span>
                                        <span class="mt-1 block text-sm text-slate-500">Aktivitas akan ikut masuk rekap kunjungan SMD.</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('activities')" />

                        <div class="mt-6 grid gap-4">
                            <div x-show="activities.includes('ambil_po')" x-transition>
                                <x-input-label for="po_amount" value="Nominal PO" />
                                <x-text-input id="po_amount" name="po_amount" type="number" min="0" step="0.01" class="mt-2 block w-full" :value="old('po_amount')" placeholder="0" />
                                <x-input-error class="mt-2" :messages="$errors->get('po_amount')" />
                            </div>

                            <div x-show="activities.includes('ambil_tagihan')" x-transition>
                                <x-input-label for="payment_amount" value="Nominal Pembayaran" />
                                <x-text-input id="payment_amount" name="payment_amount" type="number" min="0" step="0.01" class="mt-2 block w-full" :value="old('payment_amount')" placeholder="0" />
                                <x-input-error class="mt-2" :messages="$errors->get('payment_amount')" />
                            </div>

                            <div x-show="activities.includes('merapikan_display')" x-transition>
                                <x-input-label for="display_photo" value="Foto Display" />
                                <input id="display_photo" name="display_photo" type="file" accept="image/*" @change="previewDisplayPhoto($event)" class="mt-2 block w-full rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-500">
                                <x-input-error class="mt-2" :messages="$errors->get('display_photo')" />
                                <div x-show="displayPhotoName" class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                                    File dipilih: <span class="font-semibold text-slate-900" x-text="displayPhotoName"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="app-panel p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Bukti Kunjungan</p>
                        <div class="mt-4 grid gap-4">
                            <div class="app-soft-panel p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">Lokasi Kunjungan</p>
                                        <p class="mt-1 text-sm text-slate-500">Gunakan tombol ambil lokasi agar koordinat terisi otomatis.</p>
                                    </div>
                                    <button type="button" @click="fillMockLocation" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">
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
                                <input id="visit_photo" name="visit_photo" type="file" accept="image/*" @change="previewVisitPhoto($event)" class="mt-2 block w-full rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-500">
                                <x-input-error class="mt-2" :messages="$errors->get('visit_photo')" />
                                <div x-show="visitPhotoName" class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                                    File dipilih: <span class="font-semibold text-slate-900" x-text="visitPhotoName"></span>
                                </div>
                            </div>

                            <div class="app-soft-panel p-4">
                                <x-input-label for="notes" value="Catatan" />
                                <textarea id="notes" name="notes" rows="4" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">{{ old('notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>
                        </div>
                    </div>
                </section>

                <div class="sticky bottom-4 z-20 flex justify-end">
                    <div class="w-full rounded-[1.75rem] border border-white/80 bg-white/92 p-3 shadow-[0_18px_40px_-22px_rgba(15,23,42,0.38)] backdrop-blur sm:w-auto">
                        <x-primary-button class="w-full justify-center sm:w-auto sm:min-w-[240px]">Simpan Kunjungan SMD</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function smdVisitForm() {
                return {
                    query: '',
                    results: [],
                    loading: false,
                    creatingNewOutlet: {{ old('new_outlet_name') ? 'true' : 'false' }},
                    selectedOutlet: null,
                    newOutletType: '{{ old('new_outlet_type', 'prospek') }}',
                    activities: @json(old('activities', [])),
                    latitude: '{{ old('latitude') }}',
                    longitude: '{{ old('longitude') }}',
                    visitPhotoName: '',
                    displayPhotoName: '',
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
                    previewVisitPhoto(event) {
                        this.visitPhotoName = event.target.files?.[0]?.name || '';
                    },
                    previewDisplayPhoto(event) {
                        this.displayPhotoName = event.target.files?.[0]?.name || '';
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
