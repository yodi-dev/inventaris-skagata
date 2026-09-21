@extends('layouts.admin')

@section('title', 'Manajemen Lokasi Penyimpanan')
@section('header_title', 'Manajemen Lokasi - ' . ($bengkel->nama ?? 'Bengkel'))

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editId: '',
        editKode: '',
        editNama: '',
        editDeskripsi: '',
        editActionUrl: '',
        deleteActionUrl: '',
        deleteNama: '',
        openEdit(lokasi) {
            this.editId = lokasi.id;
            this.editKode = lokasi.kode;
            this.editNama = lokasi.nama;
            this.editDeskripsi = lokasi.deskripsi || '';
            this.editActionUrl = '{{ url('/toolman/lokasi') }}/' + lokasi.id;
            this.showEditModal = true;
        },
        openDelete(lokasi) {
            this.deleteNama = lokasi.nama + ' (' + lokasi.kode + ')';
            this.deleteActionUrl = '{{ url('/toolman/lokasi') }}/' + lokasi.id;
            this.showDeleteModal = true;
        }
    }">

        <!-- Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <!-- Breadcrumb -->
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="/toolman/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Manajemen Lokasi</span>
                </nav>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Master Lokasi Penyimpanan</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    Kelola lemari, rak, gudang, dan titik penempatan barang pada bengkel
                    <span class="font-semibold text-gray-700">{{ $bengkel->nama ?? 'Bengkel' }}</span>.
                </p>
            </div>
            <div>
                <button type="button" @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-primary-500/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Lokasi Baru</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm space-y-1 shadow-xs">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Terjadi kesalahan validasi input:</span>
                </div>
                <ul class="list-disc list-inside text-xs text-red-700 pl-7 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            <!-- Card 1: Total Lokasi -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Titik Lokasi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalLokasi) }}</p>
                </div>
                <div class="bg-primary-50 text-primary-600 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 2: Lokasi Terisi -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Terisi Barang</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totalTerisi) }}</p>
                </div>
                <div class="bg-blue-50 text-blue-600 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 3: Lokasi Kosong -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Kosong</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($totalKosong) }}</p>
                </div>
                <div class="bg-amber-50 text-amber-600 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('toolman.lokasi.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari berdasarkan nama lokasi, kode, atau keterangan..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Cari
                    </button>
                    @if (request('search'))
                        <a href="{{ route('toolman.lokasi.index') }}"
                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px]">
                    <thead>
                        <tr class="bg-slate-50 text-gray-500 text-[11px] uppercase tracking-wider font-semibold border-b border-gray-200">
                            <th class="px-6 py-3.5 w-16 text-center">No</th>
                            <th class="px-6 py-3.5">Kode Lokasi</th>
                            <th class="px-6 py-3.5">Nama Lokasi</th>
                            <th class="px-6 py-3.5">Deskripsi / Keterangan</th>
                            <th class="px-6 py-3.5 text-center">Jumlah Barang</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse ($lokasis as $index => $lokasi)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 text-center font-mono text-xs text-gray-400">
                                    {{ $lokasis->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                        {{ $lokasi->kode }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ $lokasi->nama }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                                    {{ $lokasi->deskripsi ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($lokasi->barangs_count > 0)
                                        <a href="{{ route('toolman.barang.index', ['search' => $lokasi->kode]) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 transition-colors"
                                            title="Lihat barang di lokasi ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            <span>{{ $lokasi->barangs_count }} Barang</span>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            0 Barang (Kosong)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" @click="openEdit({{ json_encode($lokasi) }})"
                                            class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit Lokasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" @click="openDelete({{ json_encode($lokasi) }})"
                                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus Lokasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="font-medium text-gray-600">Belum ada lokasi penyimpanan terdaftar.</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Silakan klik "Tambah Lokasi Baru" untuk mendaftarkan lemari atau rak penyimpanan bengkel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($lokasis->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $lokasis->links() }}
                </div>
            @endif
        </div>

        <!-- ================================================================= -->
        <!-- MODAL: TAMBAH LOKASI BARU                                         -->
        <!-- ================================================================= -->
        <div x-show="showCreateModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div x-show="showCreateModal"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showCreateModal = false"
                class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-5">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Tambah Lokasi Penyimpanan</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Daftarkan titik simpan baru di {{ $bengkel->nama ?? 'Bengkel' }}</p>
                    </div>
                    <button type="button" @click="showCreateModal = false"
                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('toolman.lokasi.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="create_kode" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Kode Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="create_kode" name="kode" required maxlength="50"
                            placeholder="Contoh: LOK-LA, LOK-GU, RAK-01"
                            class="w-full px-3.5 py-2 font-mono text-sm uppercase rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <p class="text-[11px] text-gray-400 mt-1">Kode bersifat unik di dalam bengkel ini (PRD 3.3).</p>
                    </div>

                    <div>
                        <label for="create_nama" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="create_nama" name="nama" required maxlength="255"
                            placeholder="Contoh: Lemari Alat A, Rak Bahan BHP, Gudang Barat"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label for="create_deskripsi" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Deskripsi / Catatan Fisik (Opsional)
                        </label>
                        <textarea id="create_deskripsi" name="deskripsi" rows="3" maxlength="1000"
                            placeholder="Penjelasan posisi rak atau jenis barang yang disimpan..."
                            class="w-full px-3.5 py-2 text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                            Simpan Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- MODAL: EDIT LOKASI                                                -->
        <!-- ================================================================= -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div x-show="showEditModal"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showEditModal = false"
                class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-5">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Edit Lokasi Penyimpanan</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi titik penempatan barang</p>
                    </div>
                    <button type="button" @click="showEditModal = false"
                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="editActionUrl" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_kode" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Kode Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_kode" name="kode" x-model="editKode" required maxlength="50"
                            class="w-full px-3.5 py-2 font-mono text-sm uppercase rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label for="edit_nama" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_nama" name="nama" x-model="editNama" required maxlength="255"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label for="edit_deskripsi" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Deskripsi / Catatan Fisik
                        </label>
                        <textarea id="edit_deskripsi" name="deskripsi" x-model="editDeskripsi" rows="3" maxlength="1000"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- MODAL: HAPUS LOKASI                                               -->
        <!-- ================================================================= -->
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div x-show="showDeleteModal"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showDeleteModal = false"
                class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4">
                
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <div class="text-center">
                    <h4 class="text-lg font-bold text-gray-900">Hapus Lokasi Penyimpanan?</h4>
                    <p class="text-xs text-gray-500 mt-1.5">
                        Apakah Anda yakin ingin menghapus lokasi <span class="font-bold text-gray-800" x-text="deleteNama"></span>?
                    </p>
                    <p class="text-[11px] text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-2.5 mt-3 text-left">
                        <strong>Perhatian:</strong> Lokasi yang masih menyimpan barang inventaris atau BHP tidak dapat dihapus demi integritas data inventaris bengkel.
                    </p>
                </div>

                <form method="POST" :action="deleteActionUrl" class="flex items-center justify-center gap-3 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors w-full">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors w-full">
                        Hapus Lokasi
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection

