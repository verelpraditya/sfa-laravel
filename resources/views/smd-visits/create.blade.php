<x-app-layout>
    @php($submissionToken = old('submission_token', (string) \Illuminate\Support\Str::uuid()))
    <x-slot name="header">
        <div>
            <span class="app-badge app-badge-sky">Kunjungan SMD</span>
            <h2 class="app-page-title mt-2">Input Aktivitas SMD</h2>
        </div>
    </x-slot>

    <div class="app-page-shell">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
             x-data="smdVisitForm()"
             x-init="init()">

            {{-- Step progress bar --}}
            <div class="sticky top-[60px] z-30 -mx-4 mb-6 border-b border-slate-200 bg-white px-4 py-3 sm:static sm:mx-0 sm:rounded-xl sm:border sm:shadow-sm xl:hidden">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-900" x-text="'Step ' + currentStep + ' / 3'"></span>
                    <span class="text-slate-500" x-text="stepLabel()"></span>
                </div>
                <div class="app-step-bar mt-2">
                    <div class="app-step-segment" :class="currentStep >= 1 ? (currentStep > 1 ? 'app-step-segment-done' : 'app-step-segment-active') : ''"></div>
                    <div class="app-step-segment" :class="currentStep >= 2 ? (currentStep > 2 ? 'app-step-segment-done' : 'app-step-segment-active') : ''"></div>
                    <div class="app-step-segment" :class="currentStep >= 3 ? 'app-step-segment-active' : ''"></div>
                </div>
            </div>

            @if ($errors->any())
                <div class="app-error-summary mb-6">
                    <p class="font-semibold">Form belum bisa disimpan. Periksa field yang masih bermasalah.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('smd-visits.store') }}" enctype="multipart/form-data" @submit="handleSubmit($event)">
                @csrf
                <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

                <div class="grid items-start gap-6 xl:grid-cols-[1.08fr_0.92fr]">
                    <div class="space-y-6">

                        {{-- ====== STEP 1: Outlet ====== --}}
                        <section class="app-panel p-5 sm:p-6" @click="currentStep = 1">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-sm font-bold text-white">1</div>
                                    <div>
                                        <h3 class="app-section-title">Outlet yang dikunjungi</h3>
                                        <p class="mt-1 text-sm text-slate-500">Cari outlet atau buat baru.</p>
                                    </div>
                                </div>
                                <button type="button" @click.stop="creatingNewOutlet = !creatingNewOutlet; resetSelection(); currentStep = 1"
                                    class="app-action-primary w-full sm:w-auto"
                                    :class="creatingNewOutlet ? 'bg-slate-600 hover:bg-slate-700' : ''">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="!creatingNewOutlet" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />
                                        <path x-show="creatingNewOutlet" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 15 6-6M7 7h10v10" />
                                    </svg>
                                    <span x-text="creatingNewOutlet ? 'Cari Outlet Existing' : 'Buat Outlet Baru'"></span>
                                </button>
                            </div>

                            {{-- Search existing outlet --}}
                            <div class="mt-5" x-show="!creatingNewOutlet">
                                <div class="app-soft-panel p-4">
                                    <x-input-label for="outlet-search" value="Cari outlet existing" />
                                    <p class="app-helper mt-1">Ketik nama outlet atau official kode.</p>

                                    <div class="mt-3 relative">
                                        <span class="app-input-icon">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none"><path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" /></svg>
                                        </span>
                                        <input id="outlet-search" x-model="query" @input.debounce.300ms="searchOutlets" type="text" placeholder="Ketik nama outlet / kode..." class="app-field app-field-with-icon">
                                    </div>
                                    <input type="hidden" name="outlet_id" :value="selectedOutlet?.id || ''">
                                    <input type="hidden" name="selected_outlet_name" :value="selectedOutlet?.name || ''">
                                    <input type="hidden" name="selected_outlet_district" :value="selectedOutlet?.district || ''">
                                    <input type="hidden" name="selected_outlet_city" :value="selectedOutlet?.city || ''">

                                    {{-- Search results --}}
                                    <div class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white shadow-sm" x-show="loading || results.length > 0 || (query.length > 0 && !selectedOutlet)">
                                        <template x-for="item in results" :key="item.id">
                                            <button type="button" @click="chooseOutlet(item)" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-sky-50 active:bg-sky-100">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                                                    <p class="mt-0.5 text-xs text-slate-500" x-text="`${item.district}, ${item.city}`"></p>
                                                </div>
                                                <span class="app-btn-sm-primary shrink-0" x-text="item.official_kode || 'Pilih'"></span>
                                            </button>
                                        </template>
                                        <p x-show="!loading && results.length === 0" class="px-4 py-3 text-sm text-slate-500">Outlet belum ditemukan. Klik "Buat Outlet Baru" jika perlu.</p>
                                        <p x-show="loading" class="px-4 py-3 text-sm text-slate-500">Mencari outlet...</p>
                                    </div>

                                    {{-- Selected outlet card --}}
                                    <div x-show="selectedOutlet" class="mt-4 flex items-center justify-between gap-3 rounded-xl border-2 border-emerald-300 bg-emerald-50 px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" /></svg>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900" x-text="selectedOutlet?.name"></p>
                                                <p class="text-xs text-slate-500" x-text="selectedOutlet ? `${selectedOutlet.district}, ${selectedOutlet.city}` : ''"></p>
                                            </div>
                                        </div>
                                        <button type="button" @click="resetSelection(); query = ''" class="app-btn-sm text-xs">Ganti</button>
                                    </div>

                                    <x-input-error class="mt-2" :messages="$errors->get('outlet_id')" />
                                </div>
                            </div>

                            {{-- New outlet form --}}
                            <div class="mt-5" x-show="creatingNewOutlet" x-cloak>
                                <div class="app-soft-panel p-4">
                                    <div class="mb-4 flex items-start gap-3">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" /></svg>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Outlet baru</p>
                                            <p class="text-xs text-slate-500">Lengkapi data dasar outlet baru.</p>
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <x-input-label for="new_outlet_name" value="Nama outlet baru" />
                                            <x-text-input id="new_outlet_name" name="new_outlet_name" class="mt-1.5 block w-full" :value="old('new_outlet_name')" />
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_name')" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_outlet_pic_name" value="Nama PIC (opsional)" />
                                            <x-text-input id="new_outlet_pic_name" name="new_outlet_pic_name" class="mt-1.5 block w-full" :value="old('new_outlet_pic_name')" placeholder="Pemilik / penanggung jawab" />
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_pic_name')" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_outlet_pic_phone" value="No. Telp PIC (opsional)" />
                                            <x-text-input id="new_outlet_pic_phone" name="new_outlet_pic_phone" class="mt-1.5 block w-full" :value="old('new_outlet_pic_phone')" placeholder="08xxxxxxxxxx" inputmode="numeric" />
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_pic_phone')" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_outlet_category" value="Kategori outlet" />
                                            <select id="new_outlet_category" name="new_outlet_category" class="app-select mt-1.5 block w-full">
                                                <option value="salon" @selected(old('new_outlet_category', 'salon') === 'salon')>Salon</option>
                                                <option value="toko" @selected(old('new_outlet_category') === 'toko')>Toko</option>
                                                <option value="barbershop" @selected(old('new_outlet_category') === 'barbershop')>Barbershop</option>
                                                <option value="lainnya" @selected(old('new_outlet_category') === 'lainnya')>Lainnya</option>
                                            </select>
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_category')" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_outlet_type" value="Jenis outlet" />
                                            <select id="new_outlet_type" name="new_outlet_type" x-model="newOutletType" class="app-select mt-1.5 block w-full">
                                                <option value="prospek">Prospek</option>
                                                <option value="noo">NOO</option>
                                                <option value="pelanggan_lama">Pelanggan Lama</option>
                                            </select>
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_type')" />
                                        </div>
                                        <div class="sm:col-span-2" x-show="newOutletType === 'pelanggan_lama'" x-cloak>
                                            <x-input-label for="new_outlet_official_kode" value="Official kode" />
                                            <x-text-input id="new_outlet_official_kode" name="new_outlet_official_kode" class="mt-1.5 block w-full" :value="old('new_outlet_official_kode')" oninput="this.value = this.value.replaceAll(' ', '').toUpperCase()" autocomplete="off" spellcheck="false" autocapitalize="characters" />
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_official_kode')" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_outlet_district" value="Kecamatan" />
                                            <x-text-input id="new_outlet_district" name="new_outlet_district" class="mt-1.5 block w-full" :value="old('new_outlet_district')" />
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_district')" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_outlet_city" value="Kota" />
                                            <x-text-input id="new_outlet_city" name="new_outlet_city" class="mt-1.5 block w-full" :value="old('new_outlet_city')" />
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_city')" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <x-input-label for="new_outlet_address" value="Alamat outlet" />
                                            <textarea id="new_outlet_address" name="new_outlet_address" rows="3" class="app-textarea mt-1.5 block w-full">{{ old('new_outlet_address') }}</textarea>
                                            <x-input-error class="mt-1.5" :messages="$errors->get('new_outlet_address')" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- ====== STEP 2: Aktivitas & Nominal ====== --}}
                        <section class="app-panel p-5 sm:p-6" @click="currentStep = 2">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-sm font-bold text-white">2</div>
                                <div>
                                    <h3 class="app-section-title">Aktivitas & nominal</h3>
                                    <p class="mt-1 text-sm text-slate-500">Pilih aktivitas SMD yang dilakukan.</p>
                                </div>
                            </div>

                            {{-- Activity cards with icons --}}
                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                {{-- Ambil PO --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="activities.includes('ambil_po') ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="checkbox" name="activities[]" value="ambil_po" x-model="activities" class="sr-only" @checked(collect(old('activities', []))->contains('ambil_po'))>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="activities.includes('ambil_po') ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="activities.includes('ambil_po') ? 'text-sky-900' : 'text-slate-700'">Ambil PO</span>
                                        <span class="block text-xs text-slate-500">Purchase order dari outlet</span>
                                    </div>
                                </label>

                                {{-- Merapikan Display --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="activities.includes('merapikan_display') ? 'border-violet-400 bg-violet-50 ring-1 ring-violet-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="checkbox" name="activities[]" value="merapikan_display" x-model="activities" class="sr-only" @checked(collect(old('activities', []))->contains('merapikan_display'))>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="activities.includes('merapikan_display') ? 'bg-violet-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6Zm5 2v8m6-8v8" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="activities.includes('merapikan_display') ? 'text-violet-900' : 'text-slate-700'">Merapikan Display</span>
                                        <span class="block text-xs text-slate-500">Rapikan produk di outlet</span>
                                    </div>
                                </label>

                                {{-- Tukar Faktur --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="activities.includes('tukar_faktur') ? 'border-amber-400 bg-amber-50 ring-1 ring-amber-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="checkbox" name="activities[]" value="tukar_faktur" x-model="activities" class="sr-only" @checked(collect(old('activities', []))->contains('tukar_faktur'))>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="activities.includes('tukar_faktur') ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4 4 4m6 0v12m0 0 4-4m-4 4-4-4" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="activities.includes('tukar_faktur') ? 'text-amber-900' : 'text-slate-700'">Tukar Faktur</span>
                                        <span class="block text-xs text-slate-500">Tukar faktur dengan outlet</span>
                                    </div>
                                </label>

                                {{-- Ambil Tagihan --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="activities.includes('ambil_tagihan') ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="checkbox" name="activities[]" value="ambil_tagihan" x-model="activities" class="sr-only" @checked(collect(old('activities', []))->contains('ambil_tagihan'))>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="activities.includes('ambil_tagihan') ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="activities.includes('ambil_tagihan') ? 'text-emerald-900' : 'text-slate-700'">Ambil Tagihan</span>
                                        <span class="block text-xs text-slate-500">Ambil pembayaran tagihan</span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('activities')" />

                            {{-- Nominal PO --}}
                            <div class="mt-5" x-show="activities.includes('ambil_po')" x-transition x-cloak>
                                <div class="app-soft-panel p-4">
                                    <p class="text-sm font-semibold text-slate-900">Nominal PO</p>
                                    <div class="mt-3">
                                        <x-input-label for="po_amount_display" value="Jumlah PO" />
                                        <input id="po_amount_display" type="text" inputmode="numeric" x-model="poAmountDisplay" @input="setCurrency('po', $event.target.value)" placeholder="Rp 0" class="app-field mt-1.5 font-semibold">
                                        <input type="hidden" name="po_amount" :value="poAmountRaw">
                                        <x-input-error class="mt-1.5" :messages="$errors->get('po_amount')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Nominal Pembayaran --}}
                            <div class="mt-5" x-show="activities.includes('ambil_tagihan')" x-transition x-cloak>
                                <div class="app-soft-panel p-4">
                                    <p class="text-sm font-semibold text-slate-900">Nominal pembayaran</p>
                                    <div class="mt-3">
                                        <x-input-label for="payment_amount_display" value="Jumlah pembayaran" />
                                        <input id="payment_amount_display" type="text" inputmode="numeric" x-model="paymentAmountDisplay" @input="setCurrency('payment', $event.target.value)" placeholder="Rp 0" class="app-field mt-1.5 font-semibold">
                                        <input type="hidden" name="payment_amount" :value="paymentAmountRaw">
                                        <x-input-error class="mt-1.5" :messages="$errors->get('payment_amount')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Display Photos --}}
                            <div class="mt-5" x-show="activities.includes('merapikan_display')" x-transition x-cloak>
                                <div class="app-soft-panel p-4">
                                    <x-input-label value="Foto display" />
                                    <p class="mt-1 text-xs text-slate-500">Ambil foto display satu per satu dari kamera. Maksimal 10 foto.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('display_photos')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('display_photos.*')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('display_photo')" />

                                    <div class="mt-3 space-y-3">
                                        <template x-for="(slot, index) in displayPhotoSlots" :key="slot.id">
                                            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900" x-text="slot.ready ? 'Foto ' + (index + 1) + ' siap' : 'Foto ' + (index + 1)"></p>
                                                        <p class="mt-0.5 text-xs text-slate-500" x-text="slot.ready ? (slot.sizeLabel || 'Siap diupload') : 'Ambil foto dari kamera'"></p>
                                                        <p x-show="slot.status" x-cloak class="mt-1 text-xs font-medium text-sky-700" x-text="slot.status"></p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <label :for="'display_photo_' + slot.id" class="app-btn-sm-primary cursor-pointer text-xs" x-text="slot.ready ? 'Ganti' : 'Kamera'"></label>
                                                        <button type="button" @click="removeDisplayPhotoSlot(slot.id)" x-show="displayPhotoSlots.length > 1 || slot.ready" class="app-btn-sm text-xs text-rose-700 bg-rose-50 border-rose-200 hover:bg-rose-100">Hapus</button>
                                                    </div>
                                                </div>
                                                <input :id="'display_photo_' + slot.id" name="display_photos[]" type="file" accept="image/*" capture="environment" @change="handleDisplayPhotoSlot($event, slot.id)" class="sr-only">
                                            </div>
                                        </template>

                                        <button type="button" @click="addDisplayPhotoSlot()" x-show="displayPhotoSlots.length < 10" class="inline-flex items-center gap-2 rounded-xl border border-dashed border-sky-300 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" /></svg>
                                            Tambah Foto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="space-y-6">

                        {{-- ====== STEP 3: Lokasi, Foto, Catatan ====== --}}
                        <section class="app-panel p-5 sm:p-6" @click="currentStep = 3">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-sm font-bold text-white">3</div>
                                <div>
                                    <h3 class="app-section-title">Lokasi, foto, dan catatan</h3>
                                    <p class="mt-1 text-sm text-slate-500">GPS dan foto bukti kunjungan.</p>
                                </div>
                            </div>

                            <div class="mt-5 space-y-5">
                                {{-- GPS Location --}}
                                <div class="app-soft-panel p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Lokasi kunjungan</p>
                                            <p class="mt-0.5 text-xs text-slate-500" x-text="gpsStatusText"></p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span x-show="latitude && longitude" x-cloak class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7" /></svg>
                                            </span>
                                            <button type="button" @click="getLocation" class="app-btn-sm-primary flex items-center gap-1.5 px-4 py-2">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c4-4.35 6-7.39 6-10a6 6 0 1 0-12 0c0 2.61 2 5.65 6 10Z" /><circle cx="12" cy="11" r="2.5" stroke-width="2" /></svg>
                                                <span x-text="latitude ? 'Refresh' : 'Ambil Lokasi'"></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                        <div>
                                            <x-input-label for="latitude" value="Latitude" />
                                            <x-text-input id="latitude" name="latitude" class="mt-1 block w-full text-xs" x-model="latitude" :value="old('latitude')" required readonly />
                                            <x-input-error class="mt-1" :messages="$errors->get('latitude')" />
                                        </div>
                                        <div>
                                            <x-input-label for="longitude" value="Longitude" />
                                            <x-text-input id="longitude" name="longitude" class="mt-1 block w-full text-xs" x-model="longitude" :value="old('longitude')" required readonly />
                                            <x-input-error class="mt-1" :messages="$errors->get('longitude')" />
                                        </div>
                                    </div>
                                </div>

                                {{-- Photo Upload - prominent area --}}
                                <div class="app-soft-panel p-4">
                                    <x-input-label value="Foto bukti kunjungan" />
                                    <label for="visit_photo" class="mt-2 flex cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed px-6 py-8 text-center transition"
                                        :class="visitPhotoPreviewUrl ? 'border-emerald-300 bg-emerald-50' : 'border-slate-300 bg-white hover:border-sky-400 hover:bg-sky-50 active:bg-sky-100'">
                                        <template x-if="!visitPhotoPreviewUrl">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
                                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" /></svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-700">Tap untuk ambil foto</p>
                                                <p class="text-xs text-slate-500">Foto akan otomatis dikompresi</p>
                                            </div>
                                        </template>
                                        <template x-if="visitPhotoPreviewUrl">
                                            <div class="flex flex-col items-center gap-2">
                                                <img :src="visitPhotoPreviewUrl" alt="Preview" class="h-40 w-full rounded-lg object-cover">
                                                <p class="text-xs font-semibold text-emerald-700" x-text="visitPhotoStatus"></p>
                                                <p class="text-xs text-slate-500">Tap untuk ganti foto</p>
                                            </div>
                                        </template>
                                    </label>
                                    <input id="visit_photo" name="visit_photo" type="file" accept="image/*" capture="environment" @change="handleVisitPhoto($event)" class="sr-only">
                                    <x-input-error class="mt-2" :messages="$errors->get('visit_photo')" />
                                </div>

                                {{-- Notes --}}
                                <div class="app-soft-panel p-4">
                                    <x-input-label for="notes" value="Catatan (opsional)" />
                                    <textarea id="notes" name="notes" rows="3" class="app-textarea mt-1.5 block w-full" placeholder="Tambahkan catatan jika perlu">{{ old('notes') }}</textarea>
                                    <x-input-error class="mt-1.5" :messages="$errors->get('notes')" />
                                </div>
                            </div>
                        </section>

                        {{-- Quick Summary --}}
                        <section class="app-panel p-5 sm:p-6">
                            <h4 class="text-sm font-bold text-slate-900">Ringkasan</h4>
                            <div class="mt-3 space-y-2 text-sm">
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                                    <span class="text-slate-500">Outlet</span>
                                    <span class="font-semibold text-slate-900" x-text="creatingNewOutlet ? 'Outlet baru' : (selectedOutlet?.name || 'Belum dipilih')"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                                    <span class="text-slate-500">Aktivitas dipilih</span>
                                    <span class="font-semibold" :class="activities.length > 0 ? 'text-sky-700' : 'text-slate-900'" x-text="activities.length > 0 ? activities.length + ' aktivitas' : 'Belum ada'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5" x-show="activities.includes('ambil_po')" x-cloak>
                                    <span class="text-slate-500">Nominal PO</span>
                                    <span class="font-semibold text-slate-900" x-text="poAmountDisplay || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5" x-show="activities.includes('ambil_tagihan')" x-cloak>
                                    <span class="text-slate-500">Pembayaran</span>
                                    <span class="font-semibold text-slate-900" x-text="paymentAmountDisplay || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                                    <span class="text-slate-500">Lokasi</span>
                                    <span class="font-semibold" :class="latitude ? 'text-emerald-700' : 'text-amber-600'" x-text="latitude ? 'Sudah diisi' : 'Belum diisi'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                                    <span class="text-slate-500">Foto</span>
                                    <span class="font-semibold" :class="visitPhotoPreviewUrl ? 'text-emerald-700' : 'text-amber-600'" x-text="visitPhotoPreviewUrl ? 'Sudah diambil' : 'Belum diambil'"></span>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- Sticky submit button --}}
                <div class="sticky bottom-0 z-20 -mx-4 mt-6 border-t border-slate-200 bg-white px-4 py-4 sm:static sm:mx-0 sm:mt-6 sm:rounded-xl sm:border sm:shadow-sm">
                    <x-primary-button class="w-full justify-center text-base sm:w-auto sm:min-w-[260px] sm:text-sm" x-bind:disabled="submitting">
                        <svg x-show="!submitting" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" /></svg>
                        <span x-show="!submitting">Simpan Kunjungan SMD</span>
                        <span x-show="submitting" x-cloak>Menyimpan...</span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function smdVisitForm() {
                return {
                    currentStep: 1,
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
                    gpsStatusText: 'Mengambil lokasi otomatis...',
                    init() {
                        this.poAmountDisplay = this.formatRupiah(this.poAmountRaw);
                        this.paymentAmountDisplay = this.formatRupiah(this.paymentAmountRaw);

                        if (this.selectedOutlet && !this.query) {
                            this.query = this.selectedOutlet.name || '';
                        }

                        this.addDisplayPhotoSlot();

                        // Auto-fetch GPS on form load
                        if (!this.latitude) {
                            this.getLocation();
                        } else {
                            this.gpsStatusText = 'Lokasi sudah tersedia.';
                        }
                    },
                    stepLabel() {
                        return { 1: 'Pilih Outlet', 2: 'Aktivitas & Nominal', 3: 'Lokasi & Foto' }[this.currentStep] || '';
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
                        if (!digits) return '';
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
                        if (this.query.trim().length === 0) { this.results = []; return; }
                        this.loading = true;
                        try {
                            const response = await fetch(`{{ route('ajax.outlets.search') }}?q=${encodeURIComponent(this.query)}`, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            });
                            const payload = await response.json();
                            this.results = payload.data || [];
                        } catch (error) { this.results = []; }
                        finally { this.loading = false; }
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
                    getLocation() {
                        this.gpsStatusText = 'Mengambil lokasi...';
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    this.latitude = position.coords.latitude.toFixed(7);
                                    this.longitude = position.coords.longitude.toFixed(7);
                                    this.gpsStatusText = 'Lokasi berhasil didapat.';
                                },
                                () => {
                                    this.latitude = '-6.9175000';
                                    this.longitude = '107.6191000';
                                    this.gpsStatusText = 'Gagal. Menggunakan lokasi default.';
                                }
                            );
                        } else {
                            this.latitude = '-6.9175000';
                            this.longitude = '107.6191000';
                            this.gpsStatusText = 'GPS tidak tersedia. Menggunakan lokasi default.';
                        }
                    },
                    async handleVisitPhoto(event) {
                        await this.handleCompressedUpload(event, 'visit');
                    },
                    addDisplayPhotoSlot() {
                        if (this.displayPhotoSlots.length >= 10) return;
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
                        if (!slot) return;
                        if (!file) {
                            slot.ready = false;
                            slot.sizeLabel = '';
                            slot.status = '';
                            return;
                        }
                        slot.status = 'Memproses foto...';
                        try {
                            const compressed = await this.compressImage(file, { maxWidth: 1600, maxHeight: 1600, quality: 0.82 });
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
                        if (!file) {
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
                            const compressed = await this.compressImage(file, { maxWidth: 1600, maxHeight: 1600, quality: 0.82 });
                            const transfer = new DataTransfer();
                            transfer.items.add(compressed);
                            input.files = transfer.files;
                            if (kind === 'visit') {
                                this.visitPhotoName = compressed.name;
                                this.visitPhotoStatus = `Foto siap (${this.formatFileSize(compressed.size)})`;
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
                            if (!file.type.startsWith('image/')) { reject(new Error('Unsupported file type.')); return; }
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
                                    if (!context) { reject(new Error('Canvas unavailable.')); return; }
                                    context.drawImage(image, 0, 0, width, height);
                                    canvas.toBlob((blob) => {
                                        if (!blob) { reject(new Error('Compression failed.')); return; }
                                        const originalName = file.name.replace(/\.[^.]+$/, '');
                                        const compressedFile = new File([blob], `${originalName}.jpg`, { type: 'image/jpeg', lastModified: Date.now() });
                                        resolve(compressedFile.size < file.size ? compressedFile : file);
                                    }, 'image/jpeg', quality);
                                };
                                image.src = reader.result;
                            };
                            reader.readAsDataURL(file);
                        });
                    },
                    formatFileSize(size) {
                        if (size < 1024) return `${size} B`;
                        if (size < 1048576) return `${(size / 1024).toFixed(0)} KB`;
                        return `${(size / 1048576).toFixed(2)} MB`;
                    },
                    setPreviewUrl(file, kind) {
                        const currentUrl = kind === 'visit' ? this.visitPhotoPreviewUrl : null;
                        if (currentUrl) URL.revokeObjectURL(currentUrl);
                        const nextUrl = URL.createObjectURL(file);
                        if (kind === 'visit') {
                            this.visitPhotoPreviewUrl = nextUrl;
                        }
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
