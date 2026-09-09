@extends('layouts.admin')

@section('title', 'Detail Tiket Peminjaman #' . $peminjaman->id)
@section('header_title', 'Sirkulasi - Detail Tiket Peminjaman')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="{{ route('toolman.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('toolman.peminjaman.index') }}"
                        class="hover:text-primary-600 transition-colors">Persetujuan Peminjaman</a>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Tiket #{{ $peminjaman->id }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Tiket Peminjaman
                        #{{ $peminjaman->id }}</h3>
                    @if ($peminjaman->status === 'menunggu_acc')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            Menunggu Persetujuan
                        </span>
                    @elseif ($peminjaman->status === 'aktif')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Sedang Dipinjam
                        </span>
                    @elseif ($peminjaman->status === 'selesai')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Selesai
                        </span>
                    @elseif ($peminjaman->status === 'ditolak')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                            Ditolak
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 capitalize">
                            {{ $peminjaman->status }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('toolman.peminjaman.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Antrean
                </a>
            </div>
        </div>

        <!-- Grid: Info Peminjam & Jadwal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card 1: Data Peminjam -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
                <h4
                    class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Peminjam
                </h4>
                <div class="flex items-center space-x-4">
                    <div
                        class="h-12 w-12 rounded-full {{ $peminjaman->user && $peminjaman->user->isGuru() ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-gray-900">{{ $peminjaman->user->name ?? '-' }}</h5>
                        <p class="text-xs text-gray-500">
                            {{ $peminjaman->user && $peminjaman->user->isGuru() ? 'Guru / Instruktur' : 'Siswa Bengkel' }}
                        </p>
                    </div>
                </div>
                <div class="space-y-2 pt-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Nomor Identitas (NIS/NIP):</span>
                        <span
                            class="font-mono font-medium text-gray-900">{{ $peminjaman->user->nomor_identitas ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Email:</span>
                        <span class="text-gray-900">{{ $peminjaman->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">WhatsApp:</span>
                        <span class="text-gray-900">{{ $peminjaman->user->nomor_wa ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Jadwal & Keperluan -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
                <h4
                    class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Waktu & Keperluan
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Bengkel:</span>
                        <span class="font-medium text-gray-900">{{ $peminjaman->bengkel->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Tanggal Pengajuan:</span>
                        <span
                            class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y, H:i') }}
                            WIB</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Batas Pengembalian:</span>
                        <span
                            class="font-medium text-gray-900">{{ $peminjaman->batas_kembali ? \Carbon\Carbon::parse($peminjaman->batas_kembali)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari yang sama' }}</span>
                    </div>
                    <div class="py-1">
                        <span class="text-gray-500 block mb-1">Keperluan / Keterangan:</span>
                        <p class="text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100 font-medium">
                            {{ $peminjaman->keperluan ?? 'Tidak ada catatan keperluan.' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Table: Rincian Barang Diminta -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h4 class="font-bold text-gray-900 text-sm">Daftar Barang Diminta</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Kode & Nama Barang</th>
                            <th class="px-6 py-3.5 font-semibold">Tipe</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Jumlah Diminta</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Stok Tersedia</th>
                            <th class="px-6 py-3.5 font-semibold">Kondisi Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($peminjaman->detailPeminjamans as $detail)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $detail->barang->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">
                                        {{ $detail->barang->kode_barang ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $detail->barang && $detail->barang->jenis_barang === 'bhp' ? 'bg-orange-50 text-orange-700' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $detail->barang && $detail->barang->jenis_barang === 'bhp' ? 'Bahan Habis Pakai' : 'Inventaris' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900">
                                    {{ $detail->jumlah }} {{ $detail->barang->satuan ?? 'Unit' }}
                                </td>
                                <td
                                    class="px-6 py-4 text-center font-medium {{ ($detail->barang->stok_tersedia ?? 0) < $detail->jumlah ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                    {{ $detail->barang->stok_tersedia ?? 0 }} {{ $detail->barang->satuan ?? 'Unit' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    @if ($peminjaman->status === 'selesai')
                                        <span class="text-green-600 font-medium">Kembali Baik:
                                            {{ $detail->jumlah_baik ?? $detail->jumlah }}</span>
                                        @if ($detail->jumlah_rusak > 0)
                                            <br><span class="text-red-600">Rusak: {{ $detail->jumlah_rusak }}</span>
                                        @endif
                                        @if ($detail->jumlah_hilang > 0)
                                            <br><span class="text-red-600">Hilang: {{ $detail->jumlah_hilang }}</span>
                                        @endif
                                    @else
                                        <span>Layak Dipinjam</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada detail barang pada
                                    tiket ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
