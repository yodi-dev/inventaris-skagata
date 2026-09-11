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
                <h3 class="text-xl font-bold text-gray-900">Pengecekan Pengembalian</h3>
                <p class="text-sm text-gray-500 mt-1">Periksa kondisi fisik alat inventaris bengkel
                    {{ $bengkel->nama ?? '' }}.</p>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('toolman.pengembalian.index') }}" class="relative w-full sm:w-80">
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

        <!-- List of Active Borrowings -->
        <div class="space-y-6">
            @forelse ($peminjamans as $pinjam)
                @php
                    $isLate =
                        $pinjam->status === 'terlambat' ||
                        ($pinjam->batas_kembali && \Carbon\Carbon::parse($pinjam->batas_kembali)->isPast());
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
                                <h4 class="text-sm font-bold text-gray-900">{{ $pinjam->user->name ?? '-' }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $pinjam->user && $pinjam->user->isGuru() ? 'Guru' : 'Siswa - ' . ($pinjam->user->nomor_identitas ?? '-') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            @if ($pinjam->status === 'menunggu_pengecekan')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    Menunggu Cek Fisik
                                </span>
                            @elseif ($isLate)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Terlambat
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Sedang Dipinjam
                                </span>
                            @endif
                            <p class="text-xs {{ $isLate ? 'text-red-600 font-medium' : 'text-gray-500' }} mt-1">
                                Batas Kembali:
                                {{ $pinjam->batas_kembali ? \Carbon\Carbon::parse($pinjam->batas_kembali)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari ini' }}
                            </p>
                        </div>
                    </div>

                    <!-- Body Kartu: Daftar Barang Inventaris -->
                    <div class="p-5">
                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Alat
                            Inventaris:</h5>
                        <div class="border border-gray-200 rounded-lg overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Alat Inventaris</th>
                                        <th class="px-4 py-2 text-center font-medium text-gray-600 w-32">Jumlah</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Lokasi Simpan Asal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($pinjam->detailPeminjamans as $detail)
                                        @if ($detail->barang && $detail->barang->jenis_barang === 'inventaris')
                                            <tr>
                                                <td class="px-4 py-3 text-gray-900 font-medium">
                                                    {{ $detail->barang->nama }}
                                                    <div class="text-xs text-gray-500 font-mono mt-0.5">
                                                        {{ $detail->barang->kode_barang }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold text-gray-900">
                                                    {{ $detail->jumlah }} {{ $detail->barang->satuan ?? 'Unit' }}
                                                </td>
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
                    <div class="px-5 py-3 border-t border-gray-200 bg-white flex justify-end gap-3">
                        <a href="{{ route('toolman.pengembalian.check', $pinjam->id) }}"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                            Cek Fisik & Konfirmasi Kembali
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-xl border border-gray-200 text-center">
                    <p class="text-gray-500 text-sm">Tidak ada barang inventaris yang sedang dipinjam saat ini.</p>
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
