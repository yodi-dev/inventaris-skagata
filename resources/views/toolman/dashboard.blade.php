@extends('layouts.admin')

@section('title', 'Dashboard Admin Bengkel')
@section('header_title', 'Dashboard Bengkel - ' . ($bengkel->nama ?? 'Teknik Komputer Jaringan'))

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header & Quick Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }}! 👋</h3>
                <p class="text-sm text-gray-500 mt-1">Berikut adalah ringkasan aktivitas bengkel
                    {{ $bengkel->nama ?? 'bengkel' }} hari ini.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (($menungguPengecekan ?? 0) > 0)
                    <a href="{{ route('toolman.pengembalian.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Cek Fisik ({{ $menungguPengecekan }})
                    </a>
                @endif
                <a href="{{ route('toolman.peminjaman.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Acc Peminjaman ({{ $requestBaru }} Pending)
                </a>
                <a href="{{ route('toolman.pengadaan.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    Buat RAB Pengadaan
                </a>
            </div>
        </div>

        @if (($pendingUserCount ?? 0) > 0)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-sm text-amber-800">
                        Terdapat <span class="font-bold">{{ $pendingUserCount }} akun peminjam baru</span> yang sedang menunggu persetujuan aktivasi akun Anda.
                    </p>
                </div>
                <a href="{{ route('toolman.peminjam.index', ['tab' => 'pending']) }}"
                    class="text-xs font-semibold text-amber-900 hover:text-amber-700 underline shrink-0 ml-4">
                    Verifikasi Peminjam &rarr;
                </a>
            </div>
        @endif

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Barang Dipinjam -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sedangDipinjam }} <span
                            class="text-sm font-normal text-gray-500">Item</span></p>
                </div>
            </div>

            <!-- Card 2: Pengajuan Baru -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-yellow-50 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Request Baru</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $requestBaru }} <span
                            class="text-sm font-normal text-gray-500">Antrean</span></p>
                </div>
            </div>

            <!-- Card 3: Barang Rusak -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-red-50 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Barang Rusak</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $barangRusak }} <span
                            class="text-sm font-normal text-gray-500">Item</span></p>
                </div>
            </div>

            <!-- Card 4: Low Stock -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-orange-50 text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stokMenipis }} <span
                            class="text-sm font-normal text-gray-500">Barang</span></p>
                </div>
            </div>
        </div>

        <!-- Data Tables Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Table 1: Barang Kembali Hari Ini / Sedang Dipinjam -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h4 class="font-semibold text-gray-800">Jadwal Pengembalian Hari Ini</h4>
                    <a href="{{ route('toolman.pengembalian.index') }}"
                        class="text-sm font-medium text-primary-600 hover:text-primary-700">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-white text-xs uppercase text-gray-500">
                                <th class="px-5 py-3 font-medium">Peminjam</th>
                                <th class="px-5 py-3 font-medium">Barang</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($jadwalPengembalian as $pinjam)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-gray-900">{{ $pinjam->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $pinjam->user->isGuru() ? 'Guru' : $pinjam->user->nomor_identitas ?? 'Siswa' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3">
                                        @foreach ($pinjam->detailPeminjamans as $detail)
                                            <p class="font-medium text-gray-800">{{ $detail->barang->nama ?? '-' }}
                                                ({{ $detail->jumlah }} {{ $detail->barang->satuan ?? 'unit' }})</p>
                                            <p class="text-xs text-gray-500 font-mono">
                                                {{ $detail->barang->kode_barang ?? '' }}</p>
                                        @endforeach
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($pinjam->status === 'menunggu_pengecekan')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Menunggu Cek
                                            </span>
                                        @elseif (
                                            $pinjam->status === 'terlambat' ||
                                                ($pinjam->batas_kembali && \Carbon\Carbon::parse($pinjam->batas_kembali)->isPast()))
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Terlambat
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Sedang Dipinjam
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-6 text-center text-gray-500 text-sm">
                                        Tidak ada peminjaman aktif yang menunggu pengembalian saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: Low Stock Alert -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h4 class="font-semibold text-gray-800">Peringatan Stok Habis Pakai</h4>
                    <a href="{{ route('toolman.barang.index') }}"
                        class="text-sm font-medium text-primary-600 hover:text-primary-700">Manajemen Stok &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-white text-xs uppercase text-gray-500">
                                <th class="px-5 py-3 font-medium">Nama Bahan / Alat</th>
                                <th class="px-5 py-3 font-medium">Sisa Stok</th>
                                <th class="px-5 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($peringatanStok as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-gray-900">{{ $item->nama }}</p>
                                        <p class="text-xs text-gray-500">Satuan: {{ $item->satuan }} &bull;
                                            {{ $item->jenis_barang === 'bhp' ? 'Bahan Habis Pakai' : 'Inventaris' }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <p
                                            class="font-bold {{ $item->stok_tersedia <= 0 ? 'text-red-600' : 'text-orange-500' }}">
                                            {{ $item->stok_tersedia }} {{ $item->satuan }}
                                        </p>
                                        <p class="text-xs text-gray-500">Batas min: {{ $item->minimum_stok }}
                                            {{ $item->satuan }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('toolman.pengadaan.create') }}"
                                            class="inline-block text-xs font-medium text-primary-600 border border-primary-600 rounded-lg px-2 py-1 hover:bg-primary-50 transition-colors">
                                            + List RAB
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-6 text-center text-gray-500 text-sm">
                                        Semua stok barang dan bahan saat ini dalam kondisi aman.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
