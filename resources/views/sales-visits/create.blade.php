<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="app-chip">Kunjungan Sales</span>
                </div>
                <h2 class="mt-4 text-3xl font-semibold leading-tight text-ink-950">Input Kunjungan Baru</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sales-visits.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="salesVisitForm()" x-init="init()">
                @csrf

                <section class="app-panel app-animate-enter overflow-hidden p-4 sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Alur Input</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Pilih outlet, isi nominal, lalu kirim bukti kunjungan</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <div class="rounded-[1.3rem] border border-sky-100 bg-sky-50 px-3 py-3 text-center shadow-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-sky-700">Step 1</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Outlet</p>
                            </div>
                            <div class="rounded-[1.3rem] border border-slate-200 bg-white px-3 py-3 text-center shadow-sm">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Step 2</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Transaksi</p>
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
                                <button type="button" @click="creatingNewOutlet = ! creatingNewOutlet; resetSelection()" class="app-glass-button w-full sm:w-auto">
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
                                        <span class="app-chip">Outlet aktif</span>
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
                                    <h3 class="mt-1 text-xl font-semibold text-ink-950">Kondisi outlet & nominal</h3>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-start gap-3 rounded-[1.5rem] border px-4 py-4 transition" :class="outletCondition === 'buka' ? 'border-sky-300 bg-sky-50 shadow-[0_12px_28px_-20px_rgba(14,165,233,0.45)]' : 'border-slate-200 bg-slate-50'">
                                    <input type="radio" name="outlet_condition" value="buka" x-model="outletCondition" @change="setOutletCondition('buka')" class="mt-1 h-4 w-4 border-slate-300 text-sky-600 focus:ring-sky-500">
                                    <span>
                                        <span class="block font-semibold text-slate-900">Outlet buka</span>
                                        <span class="mt-1 block text-sm text-slate-500">Isi nominal order dan tagihan jika ada transaksi.</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-[1.5rem] border px-4 py-4 transition" :class="outletCondition === 'tutup' ? 'border-sky-300 bg-sky-50 shadow-[0_12px_28px_-20px_rgba(14,165,233,0.45)]' : 'border-slate-200 bg-slate-50'">
                                    <input type="radio" name="outlet_condition" value="tutup" x-model="outletCondition" @change="setOutletCondition('tutup')" class="mt-1 h-4 w-4 border-slate-300 text-sky-600 focus:ring-sky-500">
                                    <span>
                                        <span class="block font-semibold text-slate-900">Outlet tutup</span>
                                        <span class="mt-1 block text-sm text-slate-500">Nominal transaksi akan dikosongkan otomatis.</span>
                                    </span>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('outlet_condition')" />

                            <div class="mt-5 app-soft-panel p-4 sm:p-5" x-show="outletCondition === 'buka'" x-transition>
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Nominal transaksi</p>
                                    </div>
                                    <span class="app-chip">Format Rp</span>
                                </div>

                                <div class="mt-4 grid gap-4">
                                    <div>
                                        <x-input-label for="order_amount_display" value="Nominal order" />
                                        <input id="order_amount_display" type="text" inputmode="numeric" x-model="orderAmountDisplay" @input="setCurrency('order', $event.target.value)" placeholder="Rp 0" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                        <input type="hidden" name="order_amount" :value="orderAmountRaw">
                                        <x-input-error class="mt-2" :messages="$errors->get('order_amount')" />
                                    </div>
                                    <div>
                                        <x-input-label for="receivable_amount_display" value="Total tagihan" />
                                        <input id="receivable_amount_display" type="text" inputmode="numeric" x-model="receivableAmountDisplay" @input="setCurrency('receivable', $event.target.value)" placeholder="Rp 0" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                        <input type="hidden" name="receivable_amount" :value="receivableAmountRaw">
                                        <x-input-error class="mt-2" :messages="$errors->get('receivable_amount')" />
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
                                        <button type="button" @click="fillMockLocation" class="app-glass-button w-full sm:w-auto">Ambil Lokasi</button>
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
                                            <span class="block font-semibold text-slate-800">Pilih foto kunjungan</span>
                                            <span class="mt-1 block text-xs text-slate-500">Format JPG, PNG, atau WEBP hingga 3MB.</span>
                                        </span>
                                        <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">Upload</span>
                                    </label>
                                    <input id="visit_photo" name="visit_photo" type="file" accept="image/*" @change="previewPhoto($event)" class="sr-only">
                                    <x-input-error class="mt-2" :messages="$errors->get('visit_photo')" />

                                    <div x-show="photoName" class="mt-4 rounded-[1.4rem] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                                        File dipilih: <span class="font-semibold text-slate-900" x-text="photoName"></span>
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
                                    <span class="text-slate-500">Kondisi</span>
                                    <span class="font-semibold text-slate-900" x-text="outletCondition === 'buka' ? 'Buka' : 'Tutup'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-[1.2rem] bg-slate-50 px-4 py-3" x-show="outletCondition === 'buka'">
                                    <span class="text-slate-500">Order</span>
                                    <span class="font-semibold text-slate-900" x-text="orderAmountDisplay || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-[1.2rem] bg-slate-50 px-4 py-3" x-show="outletCondition === 'buka'">
                                    <span class="text-slate-500">Tagihan</span>
                                    <span class="font-semibold text-slate-900" x-text="receivableAmountDisplay || 'Rp 0'"></span>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>

                <div class="sticky bottom-4 z-20 flex justify-end">
                    <div class="w-full rounded-[1.75rem] border border-white/80 bg-white/92 p-3 shadow-[0_18px_40px_-22px_rgba(15,23,42,0.38)] backdrop-blur sm:w-auto">
                        <x-primary-button class="w-full justify-center sm:min-w-[260px]">Simpan Kunjungan Sales</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function salesVisitForm() {
                return {
                    query: '',
                    results: [],
                    loading: false,
                    creatingNewOutlet: {{ old('new_outlet_name') ? 'true' : 'false' }},
                    selectedOutlet: null,
                    newOutletType: '{{ old('new_outlet_type', 'prospek') }}',
                    outletCondition: '{{ old('outlet_condition', 'buka') }}',
                    latitude: '{{ old('latitude') }}',
                    longitude: '{{ old('longitude') }}',
                    photoName: '',
                    orderAmountRaw: '{{ old('order_amount') }}',
                    receivableAmountRaw: '{{ old('receivable_amount') }}',
                    orderAmountDisplay: '',
                    receivableAmountDisplay: '',
                    init() {
                        this.orderAmountDisplay = this.formatRupiah(this.orderAmountRaw);
                        this.receivableAmountDisplay = this.formatRupiah(this.receivableAmountRaw);
                    },
                    formatRupiah(value) {
                        const digits = String(value || '').replace(/\D/g, '');

                        if (! digits) {
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

                        if (field === 'order') {
                            this.orderAmountRaw = digits;
                            this.orderAmountDisplay = formatted;
                            return;
                        }

                        this.receivableAmountRaw = digits;
                        this.receivableAmountDisplay = formatted;
                    },
                    setOutletCondition(value) {
                        this.outletCondition = value;

                        if (value === 'tutup') {
                            this.orderAmountRaw = '';
                            this.receivableAmountRaw = '';
                            this.orderAmountDisplay = '';
                            this.receivableAmountDisplay = '';
                        }
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
                    previewPhoto(event) {
                        this.photoName = event.target.files?.[0]?.name || '';
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
