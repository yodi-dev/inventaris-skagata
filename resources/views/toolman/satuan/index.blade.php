@extends('layouts.admin')

@section('title', 'Master Satuan Barang')
@section('header_title', 'Master Satuan Barang')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editId: '',
        editNama: '',
        editSingkatan: '',
        editDeskripsi: '',
        editActionUrl: '',
        deleteActionUrl: '',
        deleteNama: '',
        openEdit(satuan) {
            this.editId = satuan.id;
            this.editNama = satuan.nama;
            this.editSingkatan = satuan.singkatan || '';
            this.editDeskripsi = satuan.deskripsi || '';
            this.editActionUrl = '{{ url('/toolman/satuan') }}/' + satuan.id;
            this.showEditModal = true;
        },
        openDelete(satuan) {
            this.deleteNama = satuan.nama + (satuan.singkatan ? ' (' + satuan.singkatan + ')' : '');
            this.deleteActionUrl = '{{ url('/toolman/satuan') }}/' + satuan.id;
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
                    <span class="text-primary-700 font-semibold">Master Satuan Barang</span>
                </nav>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Master Satuan Barang</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    Kelola daftar satuan baku (unit, pcs, roll, set, meter, dll.) untuk inventaris alat dan bahan praktik.
                </p>
            </div>
            <div>
                <button type="button" @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-primary-500/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Satuan Baru</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div
                class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm space-y-1 shadow-xs">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            <!-- Card 1: Total Satuan -->
            <div
                class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Satuan Terdaftar</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalSatuan) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Referensi satuan untuk seluruh bengkel</p>
                </div>
                <div class="bg-primary-50 text-primary-600 p-3.5 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Card 2: Satuan Digunakan -->
            <div
                class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan Digunakan Barang</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($satuanTerpakaiCount) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Aktif terhubung pada inventaris & bahan</p>
                </div>
                <div class="bg-emerald-50 text-emerald-600 p-3.5 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('toolman.satuan.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama satuan, singkatan, atau keterangan..."
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
                        <a href="{{ route('toolman.satuan.index') }}"
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
                        <tr
                            class="bg-slate-50 text-gray-500 text-[11px] uppercase tracking-wider font-semibold border-b border-gray-200">
                            <th class="px-6 py-3.5 w-16 text-center">No</th>
                            <th class="px-6 py-3.5">Nama Satuan</th>
                            <th class="px-6 py-3.5">Singkatan / Simbol</th>
                            <th class="px-6 py-3.5">Deskripsi / Peruntukan</th>
                            <th class="px-6 py-3.5 text-center">Pemakaian Barang</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse ($satuans as $index => $satuan)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 text-center font-mono text-xs text-gray-400">
                                    {{ $satuans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    {{ $satuan->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($satuan->singkatan)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                            {{ $satuan->singkatan }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                                    {{ $satuan->deskripsi ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($satuan->terpakai_count > 0)
                                        <a href="{{ route('toolman.barang.index', ['search' => $satuan->nama]) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 transition-colors"
                                            title="Lihat barang dengan satuan ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            <span>{{ $satuan->terpakai_count }} Barang</span>
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            Belum terpakai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" @click="openEdit({{ json_encode($satuan) }})"
                                            class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit Satuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" @click="openDelete({{ json_encode($satuan) }})"
                                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus Satuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                    <p class="font-medium text-gray-500">Tidak ada data satuan yang ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Tambah Satuan Baru" untuk
                                        mendaftarkan satuan baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if ($satuans->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $satuans->links() }}
                </div>
            @endif
        </div>

        <!-- ================= MODAL TAMBAH SATUAN ================= -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs"
                    @click="showCreateModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl sm:align-middle"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Tambah Satuan Baru</h3>
                                <p class="text-xs text-gray-500">Daftarkan jenis satuan standar baru</p>
                            </div>
                        </div>
                        <button type="button" @click="showCreateModal = false"
                            class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('toolman.satuan.store') }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="create_nama"
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Nama Satuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="create_nama" required
                                placeholder="Contoh: Unit, Lembar, Botol, Roll"
                                class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>

                        <div>
                            <label for="create_singkatan"
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Singkatan / Simbol (Opsional)
                            </label>
                            <input type="text" name="singkatan" id="create_singkatan"
                                placeholder="Contoh: pcs, unit, lbr, roll, btl"
                                class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>

                        <div>
                            <label for="create_deskripsi"
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Deskripsi / Catatan Peruntukan (Opsional)
                            </label>
                            <textarea name="deskripsi" id="create_deskripsi" rows="3"
                                placeholder="Contoh: Digunakan khusus untuk bahan cair pelumas atau reagen praktik kimia"
                                class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                            <button type="button" @click="showCreateModal = false"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-primary-500/20">
                                Simpan Satuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= MODAL EDIT SATUAN ================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs"
                    @click="showEditModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl sm:align-middle">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Edit Satuan</h3>
                                <p class="text-xs text-gray-500">Perbarui data satuan master</p>
                            </div>
                        </div>
                        <button type="button" @click="showEditModal = false"
                            class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form :action="editActionUrl" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="edit_nama"
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Nama Satuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="edit_nama" x-model="editNama" required
                                class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>

                        <div>
                            <label for="edit_singkatan"
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Singkatan / Simbol
                            </label>
                            <input type="text" name="singkatan" id="edit_singkatan" x-model="editSingkatan"
                                class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>

                        <div>
                            <label for="edit_deskripsi"
                                class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Deskripsi / Catatan Peruntukan
                            </label>
                            <textarea name="deskripsi" id="edit_deskripsi" rows="3" x-model="editDeskripsi"
                                class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                            <button type="button" @click="showEditModal = false"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-amber-500/20">
                                Perbarui Satuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= MODAL DELETE SATUAN ================= -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs"
                    @click="showDeleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl sm:align-middle">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-red-100 text-red-600 rounded-full shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus Satuan</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3.5 bg-slate-50 border border-gray-200 rounded-xl text-sm text-gray-700">
                        Apakah Anda yakin ingin menghapus data satuan:
                        <div class="font-bold text-gray-900 mt-1" x-text="deleteNama"></div>
                        <p class="text-xs text-amber-700 mt-2 font-medium">
                            * Catatan: Satuan yang masih digunakan oleh barang tidak dapat dihapus.
                        </p>
                    </div>

                    <form :action="deleteActionUrl" method="POST" class="mt-5 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-red-500/20">
                            Ya, Hapus Satuan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
