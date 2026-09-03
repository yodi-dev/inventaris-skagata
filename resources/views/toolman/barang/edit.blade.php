@extends('layouts.admin')

@section('title', 'Edit Barang - Router Mikrotik RB951')
@section('header_title', 'Manajemen Barang Bengkel')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12" x-data="{
        tipe: 'inventaris', // 'inventaris' | 'bahan'
        bengkelCode: 'TKJ',
        kodeBarang: 'INV-TKJ-045',
        namaBarang: 'Router Mikrotik RB951',
        kategoriId: 'jaringan',
        lokasi: 'Rak Jaringan A-02 - Lemari Alat Utama',
        satuan: 'Unit',
        
        // Kuantitas Alat Inventaris
        stokBaik: 1,
        stokRusakRingan: 0,
        stokRusakBerat: 0,
        stokDipinjam: 1, // Sedang dipinjam oleh Budi Santoso
        
        // Kuantitas Bahan Habis Pakai
        stokBahan: 2.5,
        batasMinimum: 1,
        
        // Estimasi Harga & Spesifikasi
        estimasiHarga: '850000',
        spesifikasi: 'Routerboard Mikrotik RB951Ui-2HnD, 5 Port Fast Ethernet, PoE Out port 5, Wireless 2.4GHz b/g/n, RAM 128MB. Lengkap dengan adaptor 24V 0.8A bawaan.',
        
        // Image state
        hasExistingImage: true,
        imagePreview: null,
        
        // Modal state
        showDeleteModal: false,
        toastMessage: '',
        showToast: false,

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
                this.hasExistingImage = false;
            }
        },
        removeImage() {
            this.imagePreview = null;
            this.hasExistingImage = false;
            const fileInput = document.getElementById('foto_barang');
            if (fileInput) fileInput.value = '';
        },
        setTipe(val) {
            this.tipe = val;
        },
        totalStokInventaris() {
            return (parseInt(this.stokBaik) || 0) + (parseInt(this.stokRusakRingan) || 0) + (parseInt(this.stokRusakBerat) || 0);
        },
        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
        }
    }">

        <!-- Toast Notification Floating -->
        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 border border-gray-700 text-sm max-w-md"
            style="display: none;">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="toastMessage"></span>
        </div>

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <!-- Breadcrumb -->
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="/toolman/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('toolman.barang.index') }}" class="hover:text-primary-600 transition-colors">Manajemen Barang</a>
                    <span>/</span>
                    <span class="text-gray-400 font-mono">INV-TKJ-045</span>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Edit Barang</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Data Barang</h3>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        INV-TKJ-045
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                        Sedang Dipinjam (Budi S.)
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Perbarui spesifikasi, lokasi simpan fisik, atau sesuaikan status kondisi aset bengkel.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('toolman.barang.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Katalog
                </a>
            </div>
        </div>

        <!-- Callout Banner: Peringatan Barang Sedang Dipinjam (Sesuai Aturan PRD) -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-blue-900">Perhatian: Barang Sedang Aktif Dipinjam</h5>
                    <p class="text-xs text-blue-800 mt-0.5 leading-relaxed">
                        Saat ini sebanyak <strong>1 Unit</strong> sedang dipinjam oleh <span class="font-semibold">Budi Santoso (XI TKJ 1)</span> melalui Tiket <span class="font-mono font-semibold">#TKJ-2026-089</span> (Batas kembali: Hari ini, 16:00 WIB). Sesuai aturan PRD, stok tidak boleh dikurangi di bawah kuantitas yang sedang dipinjam, dan aset tidak dapat dihapus sampai transaksi selesai.
                    </p>
                </div>
            </div>
            <a href="/toolman/sirkulasi/peminjaman" class="text-xs font-semibold text-blue-700 hover:text-blue-900 underline shrink-0 whitespace-nowrap">
                Lihat Tiket &rarr;
            </a>
        </div>

        <!-- Form Wrapper -->
        <form action="{{ route('toolman.barang.index') }}" method="GET" class="space-y-6">

            <!-- SECTION 1: Klasifikasi Tipe Barang -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Klasifikasi Tipe Barang</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Pencatatan inventaris menggunakan metode quantity-based.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                        <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Tipe Terkunci pada Aset
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Option A: Alat Inventaris (Active) -->
                    <div :class="tipe === 'inventaris' ? 'border-primary-500 ring-2 ring-primary-500/20 bg-primary-50/20' : 'border-gray-200 bg-gray-50 opacity-60'"
                        class="border-2 rounded-xl p-4 transition-all flex items-start gap-4 cursor-default">
                        <div :class="tipe === 'inventaris' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'"
                            class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
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
                                Peralatan fisik yang dipinjamkan dan wajib dikembalikan pada hari yang sama. Memiliki pencatatan kondisi fisik (baik/rusak).
                            </p>
                        </div>
                    </div>

                    <!-- Option B: Bahan Habis Pakai (BHP) -->
                    <div :class="tipe === 'bahan' ? 'border-orange-500 ring-2 ring-orange-500/20 bg-orange-50/20' : 'border-gray-200 bg-gray-50 opacity-60'"
                        class="border-2 rounded-xl p-4 transition-all flex items-start gap-4 cursor-default">
                        <div :class="tipe === 'bahan' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500'"
                            class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
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
                                Material praktik yang habis dikonsumsi peminjam. Memerlukan batas batas limit stok untuk memicu pengajuan RAB otomatis.
                            </p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="tipe" :value="tipe">
            </div>

            <!-- SECTION 2: Identitas & Lokasi Penyimpanan -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <h4 class="text-base font-semibold text-gray-900">Informasi Identitas & Lokasi</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Detail identitas barang dan penempatan fisik di area bengkel.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Kode Barang (Read-only for existing assets) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="kode_barang" class="block text-sm font-medium text-gray-700">
                                Kode Barang (ID Aset)
                            </label>
                            <span class="text-[11px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Tidak Dapat Diubah
                            </span>
                        </div>
                        <div class="relative">
                            <input type="text" id="kode_barang" name="kode_barang" x-model="kodeBarang" readonly
                                class="block w-full font-mono text-sm font-bold text-gray-700 rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:ring-0 focus:border-gray-200 cursor-not-allowed">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">
                            Kode aset dibuat permanen untuk menjaga konsistensi riwayat peminjaman.
                        </p>
                    </div>

                    <!-- Nama Barang -->
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_barang" name="nama_barang" x-model="namaBarang" required
                            class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                            placeholder="Contoh: Router Mikrotik RB951">
                        <p class="text-[11px] text-gray-400 mt-1">Nama ini tampil di katalog pencarian siswa & guru.</p>
                    </div>

                    <!-- Kategori Spesifik -->
                    <div>
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Golongan / Kategori Spesifik <span class="text-red-500">*</span>
                        </label>
                        <select id="kategori_id" name="kategori_id" x-model="kategoriId" required
                            class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                            <option value="jaringan">Perangkat Jaringan Komputer</option>
                            <option value="perkakas">Perkakas Tangan & Toolset</option>
                            <option value="kabel">Kabel & Konektor</option>
                            <option value="hardware">Hardware / Komputer Rakitan</option>
                            <option value="elektronika">Komponen Elektronika & Sensor</option>
                            <option value="ukur">Instrumen / Alat Ukur</option>
                            <option value="lainnya">Lain-lain</option>
                        </select>
                    </div>

                    <!-- Bengkel Terisolasi -->
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
                        <p class="text-[11px] text-gray-400 mt-1">Data aset terisolasi pada lingkup bengkel TKJ (PRD Scope).</p>
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
                            <input type="text" id="lokasi" name="lokasi" x-model="lokasi" required
                                class="block w-full pl-9 text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                                placeholder="Contoh: Rak Jaringan A-02 - Lemari Alat Utama">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Lokasi memudahkan Toolman saat menyiapkan serah terima alat.</p>
                    </div>

                    <!-- Satuan Barang -->
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Satuan Hitung <span class="text-red-500">*</span>
                        </label>
                        <select id="satuan" name="satuan" x-model="satuan" required
                            class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                            <option value="Unit">Unit</option>
                            <option value="Pcs">Pcs / Buah</option>
                            <option value="Set">Set / Kotak Lengkap</option>
                            <option value="Pack">Pack</option>
                            <option value="Roll">Roll / Gulung</option>
                            <option value="Meter">Meter</option>
                            <option value="Box">Box / Kotak</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- SECTION 3: Kuantitas, Kondisi Fisik, & Stok Limit (Quantity-Based) -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Manajemen Kuantitas & Kondisi Fisik</h4>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Perbarui jumlah unit berdasarkan hasil pengecekan fisik di bengkel.
                        </p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200">
                        1 Unit Sedang Dipinjam
                    </span>
                </div>

                <!-- SUB-SECTION: JIKA ALAT INVENTARIS -->
                <div x-show="tipe === 'inventaris'" class="space-y-4">
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-xs text-emerald-800 leading-relaxed">
                            <strong>Aturan Kondisi Fisik:</strong> Unit dengan <span class="font-semibold text-emerald-700">"Kondisi Baik"</span> yang tidak sedang dipinjam dapat langsung dipinjam siswa/guru. Unit rusak dialokasikan ke draf pengadaan/perbaikan.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Stok Baik -->
                        <div class="p-4 bg-green-50/50 border border-green-200 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <label for="stok_baik" class="text-xs font-bold text-green-900 uppercase tracking-wider">
                                    Kondisi Baik (Total)
                                </label>
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" id="stok_baik" name="stok_baik" x-model.number="stokBaik" min="1" required
                                    class="block w-full text-center text-lg font-bold rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500">
                                <span class="text-xs font-semibold text-gray-500" x-text="satuan">Unit</span>
                            </div>
                            <p class="text-[10px] text-green-700 mt-2 font-medium">1 unit sedang aktif dipinjam.</p>
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
                            <p class="text-[10px] text-amber-700 mt-2">Dalam perawatan teknisi.</p>
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
                            <p class="text-[10px] text-red-700 mt-2">Diusulkan untuk penggantian RAB.</p>
                        </div>
                    </div>

                    <!-- Rekap Kuantitas -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-4 py-3 bg-slate-50 border border-gray-200 rounded-lg text-sm gap-2">
                        <span class="font-medium text-gray-700">Total Kuantitas Fisik Barang:</span>
                        <div class="font-bold text-gray-900 flex items-center gap-2">
                            <span class="text-lg text-primary-700" x-text="totalStokInventaris()">1</span>
                            <span x-text="satuan">Unit</span>
                            <span class="text-xs font-normal text-gray-500">(1 Unit dibawa peminjam, 0 Unit tersedia di rak)</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-SECTION: JIKA BAHAN HABIS PAKAI (BHP) -->
                <div x-show="tipe === 'bahan'" class="space-y-4" style="display: none;">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="stok_bahan" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kuantitas Sisa Stok <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="stok_bahan" name="stok_bahan" step="0.1" min="0" x-model="stokBahan"
                                    class="block w-full text-base font-bold rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                                <span class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 shrink-0"
                                    x-text="satuan">Roll</span>
                            </div>
                        </div>

                        <div>
                            <label for="batas_minimum" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Batas Minimum Stok (Low-Stock Alert) <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="batas_minimum" name="batas_minimum" step="0.1" min="0" x-model="batasMinimum"
                                    class="block w-full text-base font-bold rounded-lg border-amber-300 focus:ring-amber-500 focus:border-amber-500 shadow-sm bg-amber-50/30">
                                <span class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 shrink-0"
                                    x-text="satuan">Roll</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SECTION 4: Spesifikasi Teknis, Estimasi Harga, & Foto Barang -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <h4 class="text-base font-semibold text-gray-900">Spesifikasi Teknis & Dokumentasi</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Spesifikasi detail untuk acuan peminjam dan generator RAB pengadaan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Kolom Kiri: Spesifikasi & Harga -->
                    <div class="space-y-4">
                        <div>
                            <label for="spesifikasi" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Spesifikasi Teknis & Keterangan
                            </label>
                            <textarea id="spesifikasi" name="spesifikasi" rows="4" x-model="spesifikasi"
                                class="block w-full text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm"></textarea>
                            <p class="text-[11px] text-gray-400 mt-1">Siswa dapat membaca spesifikasi ini saat memilih barang di katalog.</p>
                        </div>

                        <div>
                            <label for="estimasi_harga" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Estimasi Harga Satuan (Acuan RAB)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-medium text-sm">
                                    Rp
                                </div>
                                <input type="number" id="estimasi_harga" name="estimasi_harga" x-model="estimasiHarga"
                                    class="block w-full pl-10 text-sm rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk mengisi estimasi biaya saat barang diajukan ke RAB pengadaan.</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Foto Barang Eksisting & Upload Baru -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Foto / Gambar Barang
                        </label>
                        
                        <div class="border-2 border-dashed border-gray-300 hover:border-primary-500 rounded-xl p-4 text-center transition-colors bg-gray-50/50 relative">
                            
                            <!-- State 1: Ada Preview Baru -->
                            <template x-if="imagePreview">
                                <div class="space-y-3">
                                    <div class="relative max-w-xs mx-auto h-40 rounded-lg overflow-hidden border border-gray-200 shadow-inner">
                                        <img :src="imagePreview" alt="Preview Foto Baru" class="w-full h-full object-contain bg-white">
                                        <button type="button" @click="removeImage()"
                                            class="absolute top-2 right-2 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-md transition-colors"
                                            title="Hapus foto baru">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-green-600 font-medium">Foto baru siap diunggah</p>
                                </div>
                            </template>

                            <!-- State 2: Foto Eksisting -->
                            <template x-if="hasExistingImage && !imagePreview">
                                <div class="space-y-3">
                                    <div class="relative max-w-xs mx-auto h-40 rounded-lg overflow-hidden border border-gray-200 bg-white flex items-center justify-center p-2">
                                        <!-- Mockup Ilustrasi Foto Router -->
                                        <div class="w-full h-full bg-slate-100 rounded-md flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-[11px] font-mono mt-1 text-gray-500">router_rb951.png</span>
                                        </div>
                                        <button type="button" @click="removeImage()"
                                            class="absolute top-2 right-2 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-md transition-colors"
                                            title="Hapus foto ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex justify-center gap-3 text-xs">
                                        <label for="foto_barang" class="text-primary-600 hover:text-primary-700 font-semibold cursor-pointer">
                                            Ganti Foto
                                            <input id="foto_barang" name="foto_barang" type="file" accept="image/*" class="sr-only"
                                                @change="handleFileChange($event)">
                                        </label>
                                    </div>
                                </div>
                            </template>

                            <!-- State 3: Kosong -->
                            <template x-if="!hasExistingImage && !imagePreview">
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
                                    <p class="text-[11px] text-gray-400 mt-1">PNG, JPG, WEBP hingga 2MB</p>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SECTION 5: Log Mutasi & Riwayat Aset (Informasi Audit) -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 shadow-sm text-xs text-gray-600 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <p class="font-semibold text-gray-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Log Mutasi & Riwayat Sirkulasi Aset
                    </p>
                    <p class="text-gray-500">Didaftarkan: <span class="font-medium text-gray-700">10 Jan 2026</span> oleh <span class="font-medium text-gray-700">Toolman TKJ</span> &bull; Terakhir Dicek: <span class="font-medium text-gray-700">28 Feb 2026</span></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-white border border-gray-300 font-medium text-gray-700">
                        12x Peminjaman Selesai
                    </span>
                </div>
            </div>

            <!-- Action Footer (Sticky Bar) -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <!-- Tombol Hapus Aset -->
                    <button type="button" @click="showDeleteModal = true"
                        class="px-4 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Barang
                    </button>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('toolman.barang.index') }}"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors text-center">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>

        <!-- ============================================================== -->
        <!-- MODAL KONFIRMASI HAPUS BARANG                                  -->
        <!-- ============================================================== -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60" @click="showDeleteModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-red-100">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Hapus Data Barang?</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Aset <strong class="text-gray-800">Router Mikrotik RB951 (INV-TKJ-045)</strong> akan dihapus permanen dari inventaris bengkel.
                                </p>
                            </div>
                        </div>

                        <!-- Warning: Sedang dipinjam -->
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800 leading-relaxed">
                            <strong>Tidak Dapat Dihapus:</strong> Barang ini sedang dalam status aktif dipinjam oleh siswa (1 Unit). Anda harus menunggu pengembalian barang dan menyelesaikan tiket terlebih dahulu sebelum dapat menghapus aset ini.
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            Tutup
                        </button>
                        <button type="button" disabled
                            class="px-5 py-2 bg-gray-300 text-gray-500 text-xs font-bold rounded-lg shadow-sm cursor-not-allowed">
                            Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

