@extends('layouts.admin')

@section('title', 'Laporan Konsumsi Bahan (BHP)')
@section('header_title', 'Laporan Konsumsi Bahan')

@section('content')
    <div class="space-y-6">

        <!-- Breadcrumb & Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('superadmin.dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-gray-400">Laporan</span>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">Konsumsi Bahan (BHP)</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Laporan Konsumsi Bahan Habis Pakai</h1>
                <p class="text-sm text-gray-500 mt-1">Audit dan pantau distribusi serta pemakaian Bahan Habis Pakai (BHP) di seluruh bengkel praktik.</p>
            </div>

            <!-- Action Buttons (Export Excel & Export PDF) -->
            <div class="flex items-center gap-3">
                <!-- Tombol Export Excel -->
                <a href="{{ route('superadmin.laporan.konsumsi.excel', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition-all shadow-xs group">
                    <svg class="w-4 h-4 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span>Export Excel</span>
                </a>

                <!-- Tombol Export PDF -->
                <a href="{{ route('superadmin.laporan.konsumsi.pdf', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all shadow-xs hover:shadow-md group">
                    <svg class="w-4 h-4 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>

        <!-- Quick Summary Cards (Statistik Konsumsi Bahan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Catatan Log -->
            <div class="p-4 bg-white border border-gray-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Transaksi</div>
                        <div class="text-xl font-bold text-gray-900">{{ number_format($totalRecords, 0, ',', '.') }} <span class="text-xs font-normal text-gray-500">Log</span></div>
                    </div>
                </div>
            </div>

            <!-- Total Kuantitas BHP Terpakai -->
            <div class="p-4 bg-white border border-gray-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Kuantitas Terpakai</div>
                        <div class="text-xl font-bold text-amber-700">{{ number_format($totalQuantity, 0, ',', '.') }} <span class="text-xs font-normal text-gray-500">Unit</span></div>
                    </div>
                </div>
            </div>

            <!-- Variasi Jenis BHP Terpakai -->
            <div class="p-4 bg-white border border-gray-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Ragam Varian BHP</div>
                        <div class="text-xl font-bold text-gray-900">{{ number_format($totalBarangVarian, 0, ',', '.') }} <span class="text-xs font-normal text-gray-500">Item</span></div>
                    </div>
                </div>
            </div>

            <!-- Bengkel Konsumsi Tertinggi -->
            <div class="p-4 bg-white border border-gray-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Bengkel Terbanyak</div>
                        <div class="text-sm font-bold text-gray-900 truncate" title="{{ $namaBengkelTerbanyak }}">{{ $namaBengkelTerbanyak }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <form action="{{ route('superadmin.laporan.konsumsi') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <!-- Filter Tanggal Mulai -->
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Tanggal Mulai
                        </label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-xs outline-none transition-all">
                    </div>

                    <!-- Filter Tanggal Akhir -->
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Tanggal Akhir
                        </label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-xs outline-none transition-all">
                    </div>

                    <!-- Filter Bengkel / Kejuruan -->
                    <div>
                        <label for="bengkel" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Bengkel / Kejuruan
                        </label>
                        <select id="bengkel" name="bengkel"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-xs outline-none transition-all bg-white">
                            <option value="">Semua Bengkel</option>
                            @foreach ($bengkels as $bengkel)
                                <option value="{{ $bengkel->id }}" {{ request('bengkel') == $bengkel->id ? 'selected' : '' }}>
                                    {{ $bengkel->nama }} ({{ $bengkel->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Cari Kata Kunci -->
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Pencarian Kata Kunci
                        </label>
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Nama barang, kode, petugas..."
                                class="w-full pl-9 pr-3 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 border shadow-xs outline-none transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Toolbar Filter -->
                <div class="flex flex-col sm:flex-row items-center justify-between pt-2 border-t border-gray-100 gap-3">
                    <div class="text-xs text-gray-500">
                        Menampilkan data konsumsi bahan berstatus <span class="font-bold text-amber-700">bhp_keluar</span>.
                    </div>

                    <div class="flex items-center gap-2">
                        @if (request()->anyFilled(['start_date', 'end_date', 'bengkel', 'search']))
                            <a href="{{ route('superadmin.laporan.konsumsi') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 border border-transparent transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reset Filter
                            </a>
                        @endif

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="py-3.5 px-4 font-semibold w-14 text-center">No</th>
                            <th class="py-3.5 px-4 font-semibold">Waktu Pemakaian</th>
                            <th class="py-3.5 px-4 font-semibold">Nama &amp; Kode Barang</th>
                            <th class="py-3.5 px-4 font-semibold">Bengkel / Lokasi</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Jumlah Dipakai</th>
                            <th class="py-3.5 px-4 font-semibold">Keterangan / Keperluan</th>
                            <th class="py-3.5 px-4 font-semibold">Petugas / Peminjam</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse ($konsumsi as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="py-4 px-4 text-center font-medium text-gray-400">
                                    {{ ($konsumsi->currentPage() - 1) * $konsumsi->perPage() + $index + 1 }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-mono">
                                        {{ $item->created_at ? $item->created_at->format('H:i') . ' WIB' : '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-gray-900 group-hover:text-green-700 transition-colors">
                                        {{ $item->barang->nama ?? 'Barang Telah Dihapus' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 text-xs font-mono font-medium border border-gray-200">
                                            {{ $item->barang->kode_barang ?? '-' }}
                                        </span>
                                        <span class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded font-medium text-[11px]">
                                            BHP
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $item->barang->bengkel->nama ?? '-' }}
                                    </div>
                                    @if ($item->barang && $item->barang->lokasiPenyimpanan)
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $item->barang->lokasiPenyimpanan->nama_lokasi }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                                        </svg>
                                        {{ abs($item->jumlah) }} {{ $item->barang->satuan ?? 'unit' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-gray-900 text-xs leading-relaxed">
                                        {{ $item->keterangan ?: 'Pemakaian rutin bahan praktik bengkel' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if ($item->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 text-xs">{{ $item->user->name }}</div>
                                                <div class="text-[11px] text-gray-400 capitalize">{{ $item->user->role }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sistem Otomatis</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-800">Tidak ada riwayat konsumsi bahan ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-1 max-w-sm">
                                            Coba atur ulang tanggal filter atau sesuaikan kata kunci pencarian barang dan bengkel.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($konsumsi->hasPages())
                <div class="px-6 py-4 bg-white border-t border-gray-200">
                    {{ $konsumsi->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
