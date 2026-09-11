@extends('layouts.admin')

@section('title', 'Katalog Barang')
@section('header_title', 'Manajemen Barang Bengkel')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Bar: Title & Primary Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Katalog Alat & Bahan</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data inventaris dan bahan habis pakai di bengkelmu.</p>
            </div>
            <div>
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
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $barang->kode_barang }}</p>
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
                                        <p class="text-xs text-red-600 mt-0.5 font-medium">Rusak: {{ $barang->stok_rusak }}
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
                                        <form action="{{ route('toolman.barang.destroy', $barang->id) }}" method="POST" class="inline"
                                            data-confirm="true"
                                            data-title="Hapus Barang Master"
                                            data-message="Apakah Anda yakin ingin menghapus barang <b>{{ addslashes($barang->nama) }}</b> ({{ $barang->kode_barang }})?"
                                            data-submessage="Tindakan ini permanen dan tidak dapat dibatalkan."
                                            data-type="danger"
                                            data-confirm-text="Ya, Hapus Barang">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex text-gray-400 hover:text-red-600 transition-colors p-1.5 rounded-md hover:bg-red-50"
                                                title="Hapus Barang">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    </div>
@endsection
