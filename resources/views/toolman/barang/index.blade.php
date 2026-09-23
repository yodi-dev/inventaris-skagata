@extends('layouts.admin')

@section('title', 'Katalog Barang')
@section('header_title', 'Manajemen Barang Bengkel')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6" x-data="{ showImportModal: false, fileName: '', isDragging: false, isSubmitting: false }">

        <!-- Top Bar: Title & Primary Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Katalog Alat & Bahan</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data inventaris dan bahan habis pakai di bengkelmu.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="showImportModal = true"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </button>
                <a href="{{ route('toolman.barang.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                        </path>
                    </svg>
                    Tambah Barang Baru
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter & Search Section -->
        <form method="GET" action="{{ route('toolman.barang.index') }}"
            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row gap-4 items-center justify-between">
            <!-- Search -->
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari kode atau nama barang...">
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select name="jenis" onchange="this.form.submit()"
                    class="block w-full sm:w-auto pl-3 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                    <option value="">Semua Tipe</option>
                    <option value="inventaris" {{ request('jenis') === 'inventaris' ? 'selected' : '' }}>Alat Inventaris
                    </option>
                    <option value="bhp" {{ request('jenis') === 'bhp' ? 'selected' : '' }}>Bahan Habis Pakai</option>
                </select>
                <select name="status" onchange="this.form.submit()"
                    class="block w-full sm:w-auto pl-3 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam
                    </option>
                    <option value="rusak" {{ request('status') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="limit" {{ request('status') === 'limit' ? 'selected' : '' }}>Stok Menipis</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Cari
                </button>
                @if (request()->anyFilled(['search', 'jenis', 'status']))
                    <a href="{{ route('toolman.barang.index') }}"
                        class="px-3 py-2 text-xs font-medium text-red-600 hover:text-red-800 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Data Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-6 py-4 font-semibold">Kode & Nama Barang</th>
                            <th class="px-6 py-4 font-semibold">Tipe</th>
                            <th class="px-6 py-4 font-semibold">Lokasi</th>
                            <th class="px-6 py-4 font-semibold">Stok & Kondisi</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($barangs as $barang)
                            <tr
                                class="hover:bg-gray-50 transition-colors {{ $barang->stok_dipinjam > 0 ? 'bg-blue-50/20' : '' }}">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $barang->nama }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-gray-500 font-mono">{{ $barang->kode_barang }}</span>
                                        @if ($barang->sumberDana)
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200"
                                                title="Sumber Dana: {{ $barang->sumberDana->nama }}">
                                                {{ $barang->sumberDana->nama }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $barang->jenis_barang === 'bhp' ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $barang->jenis_barang === 'bhp' ? 'Bahan Habis Pakai' : 'Inventaris' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-800 text-xs font-medium">
                                        {{ $barang->lokasiPenyimpanan->nama ?? '-' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $barang->lokasiPenyimpanan->kode ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900 font-semibold">Tersedia: {{ $barang->stok_tersedia }}
                                        {{ $barang->satuan }}</p>
                                    @if ($barang->stok_dipinjam > 0)
                                        <p class="text-xs text-blue-600 mt-0.5 font-medium">Dipinjam:
                                            {{ $barang->stok_dipinjam }} {{ $barang->satuan }}</p>
                                    @endif
                                    @if ($barang->stok_rusak > 0)
                                        <p class="text-xs text-red-600 mt-0.5 font-medium">Rusak:
                                            {{ $barang->stok_rusak }}
                                            {{ $barang->satuan }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($barang->stok_rusak > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Perlu Perbaikan
                                        </span>
                                    @elseif ($barang->stok_tersedia <= $barang->minimum_stok)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Stok Menipis
                                        </span>
                                    @elseif ($barang->stok_dipinjam > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5"></span>
                                            Dipinjam
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('toolman.barang.print-kartu', $barang->id) }}" target="_blank"
                                        class="inline-flex text-gray-400 hover:text-emerald-600 transition-colors p-1.5 rounded-md hover:bg-emerald-50"
                                        title="Cetak Kartu Barang Resmi">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('toolman.barang.edit', $barang->id) }}"
                                        class="inline-flex text-gray-400 hover:text-primary-600 transition-colors p-1.5 rounded-md hover:bg-gray-100"
                                        title="Edit Barang">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    @if ($barang->stok_dipinjam == 0)
                                        <form action="{{ route('toolman.barang.destroy', $barang->id) }}" method="POST"
                                            class="inline" data-confirm="true" data-title="Hapus Barang Master"
                                            data-message="Apakah Anda yakin ingin menghapus barang <b>{{ addslashes($barang->nama) }}</b> ({{ $barang->kode_barang }})?"
                                            data-submessage="Tindakan ini permanen dan tidak dapat dibatalkan."
                                            data-type="danger" data-confirm-text="Ya, Hapus Barang">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex text-gray-400 hover:text-red-600 transition-colors p-1.5 rounded-md hover:bg-red-50"
                                                title="Hapus Barang">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    Tidak ada data barang yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($barangs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $barangs->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Import Excel -->
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                @click="if (!isSubmitting) showImportModal = false">
            </div>

            <!-- Modal Panel -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showImportModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100">

                    <form action="{{ route('toolman.barang.import') }}" method="POST" enctype="multipart/form-data"
                        @submit="isSubmitting = true">
                        @csrf

                        <!-- Header -->
                        <div class="p-6 pb-4 border-b border-gray-100 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                        Import Data Barang dari Excel
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Tambahkan banyak barang sekaligus ke katalog bengkel secara cepat.
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="showImportModal = false" :disabled="isSubmitting"
                                class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-5 text-sm">
                            <!-- Template Download Banner -->
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-emerald-900 text-sm flex items-center gap-1.5">
                                        <span>Template Excel Resmi</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-200/70 text-emerald-800">
                                            Multi-Sheet .XLSX
                                        </span>
                                    </h4>
                                    <p class="text-xs text-emerald-700 mt-1">
                                        Dilengkapi kolom terpisah dan lembar referensi lokasi, satuan, & sumber dana bengkel
                                        ini.
                                    </p>
                                </div>
                                <a href="{{ route('toolman.barang.template-excel') }}"
                                    class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors shrink-0">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh Template (.xlsx)
                                </a>
                            </div>

                            <!-- File Upload Area -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Pilih File Excel / Spreadsheet <span class="text-red-500">*</span>
                                </label>
                                <div class="relative border-2 border-dashed rounded-xl p-6 text-center transition-colors duration-150 cursor-pointer"
                                    :class="isDragging ? 'border-emerald-500 bg-emerald-50/50' : (fileName ?
                                        'border-emerald-300 bg-emerald-50/20' :
                                        'border-gray-300 hover:border-gray-400 bg-gray-50/50')"
                                    @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                    @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0].name; }"
                                    @click="$refs.fileInput.click()">

                                    <input type="file" name="file" x-ref="fileInput" required
                                        accept=".xlsx,.xls,.csv" class="hidden"
                                        @change="fileName = $refs.fileInput.files[0]?.name || ''">

                                    <template x-if="!fileName">
                                        <div class="space-y-2">
                                            <div
                                                class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700">
                                                Klik untuk memilih file atau seret file ke sini
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Format didukung: <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b> (Maks. 10MB)
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="fileName">
                                        <div class="flex items-center justify-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-sm font-bold text-gray-900" x-text="fileName"></p>
                                                <p class="text-xs text-emerald-600 font-medium">Siap untuk diimpor</p>
                                            </div>
                                            <button type="button" @click.stop="$refs.fileInput.value = ''; fileName = ''"
                                                class="ml-2 p-1 text-gray-400 hover:text-red-500 rounded hover:bg-gray-100"
                                                title="Hapus file">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Options Checkboxes -->
                            <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3.5 space-y-2.5">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="auto_create_lokasi" value="1" checked
                                        class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs text-gray-700 leading-relaxed">
                                        <b>Otomatis daftarkan Lokasi Penyimpanan baru</b> jika nama lokasi belum ada di
                                        bengkel ini.
                                    </span>
                                </label>
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="auto_create_sumber_dana" value="1" checked
                                        class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs text-gray-700 leading-relaxed">
                                        <b>Otomatis daftarkan Sumber Dana baru</b> jika belum tercatat di sistem inventaris.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="showImportModal = false" :disabled="isSubmitting"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="!fileName || isSubmitting"
                                class="inline-flex items-center px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg shadow-sm transition-colors">
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSubmitting ? 'Memproses Import...' : 'Mulai Import Data'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
