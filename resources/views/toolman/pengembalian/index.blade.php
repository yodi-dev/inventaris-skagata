@extends('layouts.admin')

@section('title', 'Pengembalian Barang')
@section('header_title', 'Sirkulasi - Cek Fisik & Pengembalian')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header & Search -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Pengecekan &amp; Pengembalian Barang</h3>
                <p class="text-sm text-gray-500 mt-1">Periksa kondisi fisik alat inventaris bengkel
                    {{ $bengkel->nama ?? '' }}.</p>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('toolman.pengembalian.index') }}" class="relative w-full sm:w-80">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari nama / NIS peminjam...">
            </form>
        </div>

        <!-- Navigation Tabs: Perlu Pengecekan vs Riwayat Pengembalian -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs Pengembalian">
                <a href="{{ route('toolman.pengembalian.index', array_merge(request()->query(), ['tab' => 'aktif'])) }}"
                    class="{{ $tab === 'aktif' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Perlu Pengecekan / Aktif
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $tab === 'aktif' ? 'bg-primary-100 text-primary-800 font-bold' : 'bg-gray-100 text-gray-600' }}">
                        {{ $aktifCount }}
                    </span>
                </a>
                <a href="{{ route('toolman.pengembalian.index', array_merge(request()->query(), ['tab' => 'riwayat'])) }}"
                    class="{{ $tab === 'riwayat' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Pengembalian (Selesai)
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $tab === 'riwayat' ? 'bg-primary-100 text-primary-800 font-bold' : 'bg-gray-100 text-gray-600' }}">
                        {{ $riwayatCount }}
                    </span>
                </a>
            </nav>
        </div>

        <!-- List of Borrowing / Return Cards -->
        <div class="space-y-6">
            @forelse ($peminjamans as $pinjam)
                @php
                    $isLate =
                        $pinjam->status === 'terlambat' ||
                        ($pinjam->status !== 'selesai' && $pinjam->batas_kembali && \Carbon\Carbon::parse($pinjam->batas_kembali)->isPast());
                @endphp
                <div
                    class="bg-white border {{ $isLate ? 'border-red-300 shadow-red-50' : 'border-gray-200' }} rounded-xl shadow-sm overflow-hidden">
                    <!-- Header Kartu -->
                    <div
                        class="px-5 py-3 border-b {{ $isLate ? 'border-red-200 bg-red-50/70' : 'border-gray-200 bg-gray-50' }} flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                        <div class="flex items-center space-x-3">
                            <div
                                class="h-10 w-10 rounded-full {{ $isLate ? 'bg-red-100 text-red-700' : ($pinjam->user && $pinjam->user->isGuru() ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }} flex items-center justify-center font-bold">
                                {{ strtoupper(substr($pinjam->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-gray-900">{{ $pinjam->user->name ?? '-' }}</h4>
                                    <span class="font-mono text-xs text-gray-500 bg-gray-200/70 px-1.5 py-0.5 rounded font-semibold">
                                        #TRX-{{ str_pad($pinjam->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $pinjam->user && $pinjam->user->isGuru() ? 'Guru' : 'Siswa - ' . ($pinjam->user->nomor_identitas ?? '-') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            @if ($pinjam->status === 'selesai')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Selesai Dikembalikan
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    Dikembalikan: {{ \Carbon\Carbon::parse($pinjam->updated_at)->translatedFormat('d M Y, H:i') }} WIB
                                </p>
                            @elseif ($pinjam->status === 'menunggu_pengecekan')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    Menunggu Cek Fisik
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    Batas: {{ $pinjam->batas_kembali ? \Carbon\Carbon::parse($pinjam->batas_kembali)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari ini' }}
                                </p>
                            @elseif ($isLate)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Terlambat
                                </span>
                                <p class="text-xs text-red-600 font-medium mt-1">
                                    Batas Kembali: {{ $pinjam->batas_kembali ? \Carbon\Carbon::parse($pinjam->batas_kembali)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari ini' }}
                                </p>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Sedang Dipinjam
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    Batas Kembali: {{ $pinjam->batas_kembali ? \Carbon\Carbon::parse($pinjam->batas_kembali)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari ini' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Body Kartu: Daftar Barang Inventaris -->
                    <div class="p-5">
                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Alat Inventaris:</h5>
                        <div class="border border-gray-200 rounded-lg overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left font-medium text-gray-600">Alat Inventaris</th>
                                        <th class="px-4 py-2.5 text-center font-medium text-gray-600 w-28">Jumlah Pinjam</th>
                                        @if ($pinjam->status === 'selesai')
                                            <th class="px-4 py-2.5 text-center font-medium text-emerald-700 bg-emerald-50/70 w-24">Kembali Baik</th>
                                            <th class="px-4 py-2.5 text-center font-medium text-orange-700 bg-orange-50/70 w-24">Rusak</th>
                                            <th class="px-4 py-2.5 text-center font-medium text-red-700 bg-red-50/70 w-24">Hilang</th>
                                        @endif
                                        <th class="px-4 py-2.5 text-left font-medium text-gray-600">Lokasi Simpan Asal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($pinjam->detailPeminjamans as $detail)
                                        @if ($detail->barang && $detail->barang->jenis_barang === 'inventaris')
                                            <tr>
                                                <td class="px-4 py-3 text-gray-900 font-medium">
                                                    {{ $detail->barang->nama }}
                                                    <div class="text-xs text-gray-500 font-mono mt-0.5">
                                                        {{ $detail->barang->kode_barang }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold text-gray-900">
                                                    {{ $detail->jumlah }} {{ $detail->barang->satuan ?? 'Unit' }}
                                                </td>
                                                @if ($pinjam->status === 'selesai')
                                                    <td class="px-4 py-3 text-center font-bold text-emerald-700 bg-emerald-50/30">
                                                        {{ $detail->jumlah_baik ?? 0 }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center font-bold text-orange-700 bg-orange-50/30">
                                                        {{ $detail->jumlah_rusak ?? 0 }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center font-bold text-red-700 bg-red-50/30">
                                                        {{ $detail->jumlah_hilang ?? 0 }}
                                                    </td>
                                                @endif
                                                <td class="px-4 py-3 text-gray-600 text-xs">
                                                    {{ $detail->barang->lokasiPenyimpanan->nama ?? 'Gudang Utama' }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer Kartu: Action -->
                    <div class="px-5 py-3 border-t border-gray-200 bg-white flex justify-end items-center gap-3">
                        @if (in_array($pinjam->status, ['active', 'terlambat', 'menunggu_pengecekan']))
                            <a href="{{ route('toolman.pengembalian.check', $pinjam->id) }}"
                                class="w-full sm:w-auto text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                Cek Fisik &amp; Konfirmasi Kembali
                            </a>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pengecekan Selesai
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-xl border border-gray-200 text-center">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mx-auto text-gray-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900">
                        {{ $tab === 'riwayat' ? 'Belum Ada Riwayat Pengembalian' : 'Tidak Ada Peminjaman Aktif' }}
                    </h4>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $tab === 'riwayat' ? 'Belum ada alat inventaris yang diselesaikan pengecekan fisiknya.' : 'Semua alat inventaris saat ini tersedia di ruang bengkel.' }}
                    </p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if ($peminjamans->hasPages())
                <div class="pt-2">
                    {{ $peminjamans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

