@extends('layouts.admin')

@section('title', 'Tambah Barang Baru')
@section('header_title', 'Manajemen Barang Bengkel')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12" x-data="{
        tipe: 'inventaris', // 'inventaris' | 'bahan'
        bengkelCode: 'TKJ',
        kodeBarang: 'INV-TKJ-046',
        satuan: 'Unit',
        // Kuantitas Alat Inventaris
        stokBaik: 1,
        stokRusakRingan: 0,
        stokRusakBerat: 0,
        // Kuantitas Bahan Habis Pakai
        stokBahan: 1,
        batasMinimum: 1,
        // Estimasi Harga
        estimasiHarga: '',
        // Upload Preview
        imagePreview: null,
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
            }
        },
        removeImage() {
            this.imagePreview = null;
            document.getElementById('foto_barang').value = '';
        },
        generateKode() {
            const randomNum = Math.floor(Math.random() * 900) + 100;
            if (this.tipe === 'inventaris') {
                this.kodeBarang = 'INV-' + this.bengkelCode + '-' + randomNum;
            } else {
                this.kodeBarang = 'BHP-' + this.bengkelCode + '-' + randomNum;
            }
        },
        setTipe(val) {
            this.tipe = val;
            if (val === 'inventaris') {
                if (this.satuan === 'Roll' || this.satuan === 'Meter') {
                    this.satuan = 'Unit';
                }
                if (this.kodeBarang.startsWith('BHP-')) {
                    this.kodeBarang = this.kodeBarang.replace('BHP-', 'INV-');
                }
            } else {
                if (this.satuan === 'Unit') {
                    this.satuan = 'Roll';
                }
                if (this.kodeBarang.startsWith('INV-')) {
                    this.kodeBarang = this.kodeBarang.replace('INV-', 'BHP-');
                }
            }
        },
        totalStokInventaris() {
            return (parseInt(this.stokBaik) || 0) + (parseInt(this.stokRusakRingan) || 0) + (parseInt(this.stokRusakBerat) || 0);
        }
    }">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <!-- Breadcrumb -->
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="/toolman/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('toolman.barang.index') }}" class="hover:text-primary-600 transition-colors">Manajemen Barang</a>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Tambah Barang Baru</span>
                </nav>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Barang Baru</h3>
                <p class="text-sm text-gray-500 mt-1">Daftarkan alat inventaris atau bahan habis pakai ke inventaris bengkel jurusan.</p>
            </div>
            <div>
                <a href="{{ route('toolman.barang.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Katalog
                </a>
            </div>
        </div>

        <!-- Form Wrapper -->
        <form action="{{ route('toolman.barang.index') }}" method="GET" class="space-y-6">

            <!-- SECTION 1: Pilih Tipe Barang (Interactive Segmented Cards) -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Klasifikasi Tipe Barang</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Tentukan jenis pencatatan sesuai aturan operasional bengkel.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 border border-primary-100">
                        Quantity-Based Tracking
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Option A: Alat Inventaris -->
                    <div @click="setTipe('inventaris')"
                        :class="tipe === 'inventaris' ? 'border-primary-500 ring-2 ring-primary-500/20 bg-primary-50/20' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        class="cursor-pointer border-2 rounded-xl p-4 transition-all flex items-start gap-4">
                        <div :class="tipe === 'inventaris' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-500'"
                            class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h5 class="font-bold text-gray-900 text-sm">Alat Inventaris</h5>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                    Wajib Dikembalikan
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Peralatan fisik (router, tang crimping, tester) yang dipinjamkan dan wajib dikembalikan pada hari yang sama. Memiliki pencatatan kondisi fisik (baik/rusak).
                            </p>
                        </div>
                    </div>

                    <!-- Option B: Bahan Habis Pakai (BHP) -->
                    <div @click="setTipe('bahan')"
                        :class="tipe === 'bahan' ? 'border-orange-500 ring-2 ring-orange-500/20 bg-orange-50/20' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        class="cursor-pointer border-2 rounded-xl p-4 transition-all flex items-start gap-4">
                        <div :class="tipe === 'bahan' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-500'"
                            class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h5 class="font-bold text-gray-900 text-sm">Bahan Habis Pakai (BHP)</h5>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">
                                    Stok Berkurang Permanen
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Material praktik (kabel UTP, timah solder, konektor RJ45) yang habis terpakai. Memerlukan batas batas limit stok untuk memicu pengajuan RAB otomatis.
                            </p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="tipe" :value="tipe">
            </div>

            <!-- SECTION 2: Informasi Dasar & Lokasi Barang -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <h4 class="text-base font-semibold text-gray-900">Informasi Identitas & Lokasi</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Detail identitas barang dan penempatan fisik di area bengkel.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Kode Barang -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="kode_barang" class="block text-sm font-medium text-gray-700">
                                Kode Barang <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="generateKode()"
                                class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Acak Kode
                            </button>
                        </div>
                        <div class="relative">
                            <input type="text" id="kode_barang" name="kode_barang" x-model="kodeBarang" required
                                class="block w-full font-mono text-sm font-semibold text-gray-900 rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                placeholder="INV-TKJ-046">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">
                            Prefix otomatis: <span class="font-mono text-primary-600">INV-</span> (Alat) atau <span class="font-mono text-orange-600">BHP-</span> (Bahan).
                        </p>
                    </div>

                    <!-- Nama Barang -->
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_barang" name="nama_barang" required
                            class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                            placeholder="Contoh: Router Mikrotik RB951, Kabel LAN Cat6, Crimping Tool...">
                        <p class="text-[11px] text-gray-400 mt-1">Gunakan nama yang umum dikenal siswa dan instruktur bengkel.</p>
                    </div>

                    <!-- Kategori Spesifik -->
                    <div>
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Golongan / Kategori Spesifik <span class="text-red-500">*</span>
                        </label>
                        <select id="kategori_id" name="kategori_id" required
                            class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                            <option value="">-- Pilih Golongan --</option>
                            <option value="jaringan" selected>Perangkat Jaringan Komputer</option>
                            <option value="perkakas">Perkakas Tangan & Toolset</option>
                            <option value="kabel">Kabel & Konektor</option>
                            <option value="hardware">Hardware / Komputer Rakitan</option>
                            <option value="elektronika">Komponen Elektronika & Sensor</option>
                            <option value="ukur">Instrumen / Alat Ukur</option>
                            <option value="lainnya">Lain-lain</option>
                        </select>
                    </div>

                    <!-- Bengkel Terisolasi (PRD: wajib terikat bengkel_id) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Bengkel / Jurusan Pemilik
                        </label>
                        <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span class="font-medium text-gray-800">Teknik Komputer & Jaringan (TKJ)</span>
                            <span class="ml-auto text-[10px] bg-primary-100 text-primary-700 px-2 py-0.5 rounded font-semibold uppercase">
                                Terisolasi
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Terkunci otomatis pada hak bengkel akun Anda (PRD Bengkel Scope).</p>
                    </div>

                    <!-- Lokasi Penyimpanan Fisik -->
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Lokasi Simpan Fisik (Rak/Lemari/Laci) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="lokasi" name="lokasi" required
                                class="block w-full pl-9 text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                placeholder="Contoh: Lemari Kaca A - Rak 2, Kotak Toolset 01">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Lokasi mempermudah Toolman mengambil barang saat siswa mengambil tiket.</p>
                    </div>

                    <!-- Satuan Barang -->
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Satuan Hitung <span class="text-red-500">*</span>
                        </label>
                        <select id="satuan" name="satuan" x-model="satuan" required
                            class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                            <template x-if="tipe === 'inventaris'">
                                <optgroup label="Satuan Alat">
                                    <option value="Unit">Unit</option>
                                    <option value="Pcs">Pcs / Buah</option>
                                    <option value="Set">Set / Kotak Lengkap</option>
                                    <option value="Pack">Pack</option>
                                </optgroup>
                            </template>
                            <template x-if="tipe === 'bahan'">
                                <optgroup label="Satuan Bahan">
                                    <option value="Roll">Roll / Gulung</option>
                                    <option value="Meter">Meter</option>
                                    <option value="Pcs">Pcs / Buah</option>
                                    <option value="Box">Box / Kotak</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Batang">Batang</option>
                                    <option value="Botol">Botol / Kaleng</option>
                                </optgroup>
                            </template>
                        </select>
                    </div>

                </div>
            </div>

            <!-- SECTION 3: Kuantitas, Kondisi Fisik, & Stok Limit (Quantity-Based) -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Manajemen Kuantitas & Stok Awal</h4>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <span x-show="tipe === 'inventaris'">Catat jumlah unit berdasarkan kondisi fisik saat barang masuk ke bengkel.</span>
                            <span x-show="tipe === 'bahan'">Catat saldo awal bahan dan tentukan batas limit peringatan restock (RAB).</span>
                        </p>
                    </div>
                </div>

                <!-- SUB-SECTION: JIKA ALAT INVENTARIS -->
                <div x-show="tipe === 'inventaris'" class="space-y-4">
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-xs text-emerald-800 leading-relaxed">
                            <strong>Aturan Inventaris:</strong> Hanya barang dengan kondisi <span class="font-semibold text-emerald-700">"Kondisi Baik"</span> yang dapat dipinjam oleh siswa/guru di katalog. Unit rusak akan otomatis dialokasikan ke antrean perbaikan atau draf RAB.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Stok Baik -->
                        <div class="p-4 bg-green-50/50 border border-green-200 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <label for="stok_baik" class="text-xs font-bold text-green-900 uppercase tracking-wider">
                                    Kondisi Baik (Siap Pinjam)
                                </label>
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" id="stok_baik" name="stok_baik" x-model.number="stokBaik" min="0" required
                                    class="block w-full text-center text-lg font-bold rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500">
                                <span class="text-xs font-semibold text-gray-500" x-text="satuan">Unit</span>
                            </div>
                            <p class="text-[10px] text-green-700 mt-2">Langsung tampil di katalog peminjam.</p>
                        </div>

                        <!-- Stok Rusak Ringan -->
                        <div class="p-4 bg-amber-50/50 border border-amber-200 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <label for="stok_rusak_ringan" class="text-xs font-bold text-amber-900 uppercase tracking-wider">
                                    Rusak Ringan
                                </label>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" id="stok_rusak_ringan" name="stok_rusak_ringan" x-model.number="stokRusakRingan" min="0"
                                    class="block w-full text-center text-lg font-bold rounded-lg border-gray-300 focus:ring-amber-500 focus:border-amber-500">
                                <span class="text-xs font-semibold text-gray-500" x-text="satuan">Unit</span>
                            </div>
                            <p class="text-[10px] text-amber-700 mt-2">Masuk antrean perbaikan internal.</p>
                        </div>

                        <!-- Stok Rusak Berat -->
                        <div class="p-4 bg-red-50/50 border border-red-200 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <label for="stok_rusak_berat" class="text-xs font-bold text-red-900 uppercase tracking-wider">
                                    Rusak Berat (Afkir)
                                </label>
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" id="stok_rusak_berat" name="stok_rusak_berat" x-model.number="stokRusakBerat" min="0"
                                    class="block w-full text-center text-lg font-bold rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                                <span class="text-xs font-semibold text-gray-500" x-text="satuan">Unit</span>
                            </div>
                            <p class="text-[10px] text-red-700 mt-2">Diusulkan untuk penggantian via RAB.</p>
                        </div>
                    </div>

                    <!-- Total Rekap Kuantitas -->
                    <div class="flex justify-between items-center px-4 py-3 bg-slate-50 border border-gray-200 rounded-lg text-sm">
                        <span class="font-medium text-gray-700">Total Kuantitas Fisik Barang:</span>
                        <div class="font-bold text-gray-900 flex items-center gap-1.5">
                            <span class="text-lg text-primary-700" x-text="totalStokInventaris()">1</span>
                            <span x-text="satuan">Unit</span>
                            <span class="text-xs font-normal text-gray-500">(Quantity-Based Single Entity)</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-SECTION: JIKA BAHAN HABIS PAKAI (BHP) -->
                <div x-show="tipe === 'bahan'" class="space-y-4" style="display: none;">
                    <div class="p-4 bg-orange-50/80 border border-orange-200 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-xs text-orange-900 leading-relaxed">
                            <strong>Aturan Bahan Habis Pakai:</strong> Stok akan terpotong permanen saat tiket peminjaman disetujui. Tentukan <strong>Batas Minimum Stok</strong> agar sistem otomatis mengingatkan Anda saat stok menipis dan memasukkannya ke generator RAB pengadaan.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Stok Bahan Awal -->
                        <div>
                            <label for="stok_bahan" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kuantitas Stok Awal <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="stok_bahan" name="stok_bahan" step="0.1" min="0.1" x-model="stokBahan"
                                    class="block w-full text-base font-bold rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                    placeholder="Contoh: 5 atau 2.5">
                                <span class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 shrink-0"
                                    x-text="satuan">Roll</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Mendukung angka desimal (contoh: 2.5 Roll kabel).</p>
                        </div>

                        <!-- Batas Minimum Stok (Low Stock Alert Threshold) -->
                        <div>
                            <label for="batas_minimum" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Batas Minimum Stok (Low-Stock Alert) <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="batas_minimum" name="batas_minimum" step="0.1" min="0" x-model="batasMinimum"
                                    class="block w-full text-base font-bold rounded-lg border-amber-300 focus:ring-amber-500 focus:border-amber-500 shadow-sm bg-amber-50/30"
                                    placeholder="Contoh: 1">
                                <span class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 shrink-0"
                                    x-text="satuan">Roll</span>
                            </div>
                            <p class="text-[11px] text-amber-700 mt-1 font-medium">
                                Peringatan muncul jika stok sisa &le; batas minimum ini.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SECTION 4: Spesifikasi Teknis, Estimasi Harga, & Foto Barang -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <h4 class="text-base font-semibold text-gray-900">Spesifikasi Teknis & Dokumentasi</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Lengkapi spesifikasi untuk acuan peminjam dan referensi pengadaan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Kolom Kiri: Deskripsi & Estimasi Biaya -->
                    <div class="space-y-4">
                        <!-- Spesifikasi / Deskripsi Teknis -->
                        <div>
                            <label for="spesifikasi" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Spesifikasi Teknis & Keterangan Tambahan
                            </label>
                            <textarea id="spesifikasi" name="spesifikasi" rows="4"
                                class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                placeholder="Tuliskan merk, tipe chipset, nomor model, panjang/kapasitas, atau panduan penggunaan..."></textarea>
                            <p class="text-[11px] text-gray-400 mt-1">Deskripsi ini akan dibaca oleh siswa saat melihat katalog peminjaman.</p>
                        </div>

                        <!-- Estimasi Harga Satuan (Untuk RAB) -->
                        <div>
                            <label for="estimasi_harga" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Estimasi Harga Satuan (Opsional)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-medium text-sm">
                                    Rp
                                </div>
                                <input type="number" id="estimasi_harga" name="estimasi_harga" x-model="estimasiHarga"
                                    class="block w-full pl-10 text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                    placeholder="Contoh: 1850000">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Digunakan sebagai perkiraan otomatis saat men-generate draf RAB Pengadaan ke Waka Sarpras.</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Upload Foto Barang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Foto / Gambar Barang
                        </label>
                        
                        <!-- Upload Area & Preview -->
                        <div class="border-2 border-dashed border-gray-300 hover:border-primary-500 rounded-xl p-4 text-center transition-colors bg-gray-50/50 relative">
                            
                            <!-- State 1: Ada Preview -->
                            <template x-if="imagePreview">
                                <div class="space-y-3">
                                    <div class="relative max-w-xs mx-auto h-44 rounded-lg overflow-hidden border border-gray-200 shadow-inner">
                                        <img :src="imagePreview" alt="Preview Foto Barang" class="w-full h-full object-contain bg-white">
                                        <button type="button" @click="removeImage()"
                                            class="absolute top-2 right-2 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-md transition-colors"
                                            title="Hapus foto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-green-600 font-medium">Foto berhasil dipilih</p>
                                </div>
                            </template>

                            <!-- State 2: Belum Ada Foto -->
                            <template x-if="!imagePreview">
                                <div class="py-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center mt-2">
                                        <label for="foto_barang"
                                            class="relative cursor-pointer bg-white rounded-md font-semibold text-primary-600 hover:text-primary-500 focus-within:outline-none">
                                            <span>Unggah gambar</span>
                                            <input id="foto_barang" name="foto_barang" type="file" accept="image/*" class="sr-only"
                                                @change="handleFileChange($event)">
                                        </label>
                                        <p class="pl-1">atau tarik ke sini</p>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">PNG, JPG, WEBP hingga 2MB (Opsional)</p>
                                </div>
                            </template>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2">Foto akan tampil di katalog siswa/guru untuk mempermudah identifikasi alat.</p>
                    </div>

                </div>
            </div>

            <!-- Sticky / Floating Action Footer -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pastikan data fisik dan klasifikasi barang telah dicek langsung di bengkel.</span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('toolman.barang.index') }}"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors text-center">
                        Batal
                    </a>

                    <button type="submit" name="action" value="save_and_add"
                        class="px-4 py-2.5 bg-primary-50 border border-primary-200 hover:bg-primary-100 text-primary-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        Simpan & Tambah Lagi
                    </button>

                    <button type="submit" name="action" value="save"
                        class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Barang
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection

