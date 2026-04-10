<x-app-layout>
    @php($submissionToken = old('submission_token', (string) \Illuminate\Support\Str::uuid()))
    <x-slot name="header">
        <div>
            <span class="app-badge app-badge-sky">Kunjungan Sales</span>
            <h2 class="app-page-title mt-2">Input Kunjungan Baru</h2>
        </div>
    </x-slot>

    <div class="app-page-shell">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
             x-data="salesVisitForm()"
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

            <form method="POST" action="{{ route('sales-visits.store') }}" enctype="multipart/form-data" @submit="handleSubmit($event)">
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

                        {{-- ====== STEP 2: Kondisi & Nominal ====== --}}
                        <section class="app-panel p-5 sm:p-6" @click="currentStep = 2">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-sm font-bold text-white">2</div>
                                <div>
                                    <h3 class="app-section-title">Kondisi outlet & nominal</h3>
                                    <p class="mt-1 text-sm text-slate-500">Pilih kondisi outlet saat dikunjungi.</p>
                                </div>
                            </div>

                            {{-- Condition cards with icons --}}
                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                {{-- Buka --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="outletCondition === 'buka' ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="radio" name="outlet_condition" value="buka" x-model="outletCondition" @change="setOutletCondition('buka')" class="sr-only">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="outletCondition === 'buka' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="outletCondition === 'buka' ? 'text-emerald-900' : 'text-slate-700'">Buka</span>
                                        <span class="block text-xs text-slate-500">Outlet buka normal</span>
                                    </div>
                                </label>
                                {{-- Tutup --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="outletCondition === 'tutup' ? 'border-rose-400 bg-rose-50 ring-1 ring-rose-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="radio" name="outlet_condition" value="tutup" x-model="outletCondition" @change="setOutletCondition('tutup')" class="sr-only">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="outletCondition === 'tutup' ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="outletCondition === 'tutup' ? 'text-rose-900' : 'text-slate-700'">Tutup</span>
                                        <span class="block text-xs text-slate-500">Outlet tidak buka</span>
                                    </div>
                                </label>
                                {{-- Order by WA --}}
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border-2 px-4 py-4 transition"
                                    :class="outletCondition === 'order_by_wa' ? 'border-violet-400 bg-violet-50 ring-1 ring-violet-200' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'">
                                    <input type="radio" name="outlet_condition" value="order_by_wa" x-model="outletCondition" @change="setOutletCondition('order_by_wa')" class="sr-only">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition"
                                        :class="outletCondition === 'order_by_wa' ? 'bg-violet-500 text-white' : 'bg-slate-100 text-slate-500'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z" /></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold" :class="outletCondition === 'order_by_wa' ? 'text-violet-900' : 'text-slate-700'">Order by WA</span>
                                        <span class="block text-xs text-slate-500">Order lewat WhatsApp</span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('outlet_condition')" />

                            {{-- Nominal fields --}}
                            <div class="mt-5" x-show="['buka', 'order_by_wa'].includes(outletCondition)" x-transition>
                                <div class="app-soft-panel p-4">
                                    <p class="text-sm font-semibold text-slate-900">Nominal transaksi</p>
                                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <x-input-label for="order_amount_display" value="Nominal order" />
                                            <input id="order_amount_display" type="text" inputmode="numeric" x-model="orderAmountDisplay" @input="setCurrency('order', $event.target.value)" placeholder="Rp 0" class="app-field mt-1.5 font-semibold">
                                            <input type="hidden" name="order_amount" :value="orderAmountRaw">
                                            <x-input-error class="mt-1.5" :messages="$errors->get('order_amount')" />
                                        </div>
                                        <div>
                                            <x-input-label for="receivable_amount_display" value="Total tagihan" />
                                            <input id="receivable_amount_display" type="text" inputmode="numeric" x-model="receivableAmountDisplay" @input="setCurrency('receivable', $event.target.value)" placeholder="Rp 0" class="app-field mt-1.5 font-semibold">
                                            <input type="hidden" name="receivable_amount" :value="receivableAmountRaw">
                                            <x-input-error class="mt-1.5" :messages="$errors->get('receivable_amount')" />
                                        </div>
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
                                        :class="photoPreviewUrl ? 'border-emerald-300 bg-emerald-50' : 'border-slate-300 bg-white hover:border-sky-400 hover:bg-sky-50 active:bg-sky-100'">
                                        <template x-if="!photoPreviewUrl">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
                                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" /></svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-700">Tap untuk ambil foto</p>
                                                <p class="text-xs text-slate-500">Foto akan otomatis dikompresi</p>
                                            </div>
                                        </template>
                                        <template x-if="photoPreviewUrl">
                                            <div class="flex flex-col items-center gap-2">
                                                <img :src="photoPreviewUrl" alt="Preview" class="h-40 w-full rounded-lg object-cover">
                                                <p class="text-xs font-semibold text-emerald-700" x-text="photoStatus"></p>
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
                                    <span class="text-slate-500">Kondisi</span>
                                    <span class="font-semibold" :class="{
                                        'text-emerald-700': outletCondition === 'buka',
                                        'text-rose-700': outletCondition === 'tutup',
                                        'text-violet-700': outletCondition === 'order_by_wa',
                                        'text-slate-900': !outletCondition
                                    }" x-text="outletConditionLabel()"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5" x-show="['buka', 'order_by_wa'].includes(outletCondition)">
                                    <span class="text-slate-500">Order</span>
                                    <span class="font-semibold text-slate-900" x-text="orderAmountDisplay || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5" x-show="['buka', 'order_by_wa'].includes(outletCondition)">
                                    <span class="text-slate-500">Tagihan</span>
                                    <span class="font-semibold text-slate-900" x-text="receivableAmountDisplay || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                                    <span class="text-slate-500">Lokasi</span>
                                    <span class="font-semibold" :class="latitude ? 'text-emerald-700' : 'text-amber-600'" x-text="latitude ? 'Sudah diisi' : 'Belum diisi'"></span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                                    <span class="text-slate-500">Foto</span>
                                    <span class="font-semibold" :class="photoPreviewUrl ? 'text-emerald-700' : 'text-amber-600'" x-text="photoPreviewUrl ? 'Sudah diambil' : 'Belum diambil'"></span>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- Sticky submit button --}}
                <div class="sticky bottom-0 z-20 -mx-4 mt-6 border-t border-slate-200 bg-white px-4 py-4 sm:static sm:mx-0 sm:mt-6 sm:rounded-xl sm:border sm:shadow-sm">
                    <x-primary-button class="w-full justify-center text-base sm:w-auto sm:min-w-[260px] sm:text-sm" x-bind:disabled="submitting">
                        <svg x-show="!submitting" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" /></svg>
                        <span x-show="!submitting">Simpan Kunjungan Sales</span>
                        <span x-show="submitting" x-cloak>Menyimpan...</span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function salesVisitForm() {
                return {
                    currentStep: 1,
                    query: @js(old('selected_outlet_name', '')),
                    results: [],
                    loading: false,
                    creatingNewOutlet: {{ old('new_outlet_name') ? 'true' : 'false' }},
                    selectedOutlet: @js(old('outlet_id') ? ['id' => (int) old('outlet_id'), 'name' => old('selected_outlet_name'), 'district' => old('selected_outlet_district'), 'city' => old('selected_outlet_city')] : null),
                    newOutletType: '{{ old('new_outlet_type', 'prospek') }}',
                    outletCondition: '{{ old('outlet_condition', 'buka') }}',
                    latitude: '{{ old('latitude') }}',
                    longitude: '{{ old('longitude') }}',
                    photoName: '',
                    photoStatus: '',
                    photoPreviewUrl: null,
                    submitting: false,
                    orderAmountRaw: '{{ old('order_amount') }}',
                    receivableAmountRaw: '{{ old('receivable_amount') }}',
                    orderAmountDisplay: '',
                    receivableAmountDisplay: '',
                    gpsStatusText: 'Mengambil lokasi otomatis...',
                    init() {
                        this.orderAmountDisplay = this.formatRupiah(this.orderAmountRaw);
                        this.receivableAmountDisplay = this.formatRupiah(this.receivableAmountRaw);

                        if (this.selectedOutlet && !this.query) {
                            this.query = this.selectedOutlet.name || '';
                        }

                        // Auto-fetch GPS on form load
                        if (!this.latitude) {
                            this.getLocation();
                        } else {
                            this.gpsStatusText = 'Lokasi sudah tersedia.';
                        }
                    },
                    stepLabel() {
                        return { 1: 'Pilih Outlet', 2: 'Kondisi & Nominal', 3: 'Lokasi & Foto' }[this.currentStep] || '';
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
                    outletConditionLabel() {
                        return { buka: 'Buka', tutup: 'Tutup', order_by_wa: 'Order by WA' }[this.outletCondition] || '-';
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
                        const input = event.target;
                        const file = input.files?.[0];
                        if (!file) { this.photoName = ''; this.photoStatus = ''; this.photoPreviewUrl = null; return; }
                        this.photoStatus = 'Memproses foto...';
                        try {
                            const compressed = await this.compressImage(file, { maxWidth: 1600, maxHeight: 1600, quality: 0.82 });
                            const transfer = new DataTransfer();
                            transfer.items.add(compressed);
                            input.files = transfer.files;
                            this.photoName = compressed.name;
                            this.photoStatus = `Foto siap (${this.formatFileSize(compressed.size)})`;
                            this.setPreviewUrl(compressed);
                        } catch (error) {
                            this.photoName = file.name;
                            this.photoStatus = 'Foto asli akan diupload.';
                            this.setPreviewUrl(file);
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
                    setPreviewUrl(file) {
                        if (this.photoPreviewUrl) URL.revokeObjectURL(this.photoPreviewUrl);
                        this.photoPreviewUrl = URL.createObjectURL(file);
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
