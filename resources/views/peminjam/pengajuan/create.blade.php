@extends('layouts.peminjam')

@section('title', 'Form Pengajuan Peminjaman')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Flash Notifications -->
        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl p-4 flex items-start gap-3 shadow-xs">
                <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-bold">Terjadi Kesalahan:</h4>
                    <p class="text-rose-700 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Form Pengajuan Peminjaman</h3>
                <p class="text-sm text-gray-500 mt-1">Lengkapi detail keperluan peminjaman alat praktik atau bahan ke
                    bengkel
                    {{ $bengkel->nama ?? '' }}.</p>
            </div>
            <a href="{{ route('peminjam.katalog.index') }}"
                class="text-xs sm:text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali ke Katalog
            </a>
        </div>

        <!-- Form Container -->
        <form action="{{ route('peminjam.pengajuan.store') }}" method="POST"
            class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
            @csrf
            <input type="hidden" name="bengkel_id" value="{{ $bengkel?->id }}">

            <div class="p-6 space-y-6">

                <!-- Barang Pilihan -->
                @if ($barang)
                    <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                    <div
                        class="bg-primary-50/60 border border-primary-100 rounded-2xl p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3.5">
                            <div
                                class="h-12 w-12 rounded-xl flex items-center justify-center shrink-0 {{ $barang->jenis_barang === 'inventaris' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                @if ($barang->jenis_barang === 'inventaris')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $barang->jenis_barang === 'inventaris' ? 'text-emerald-700 bg-emerald-100' : 'text-amber-700 bg-amber-100' }}">
                                    {{ $barang->jenis_barang === 'inventaris' ? 'Alat Inventaris' : 'Bahan Habis Pakai' }}
                                </span>
                                <h4 class="text-base font-bold text-gray-900 mt-0.5">{{ $barang->nama }}</h4>
                                <p class="text-xs text-gray-500">
                                    Kode: <strong class="font-mono text-gray-700">{{ $barang->kode_barang }}</strong>
                                    &bull;
                                    Stok Tersedia: <strong class="text-emerald-700">{{ $barang->stok_tersedia }}
                                        {{ $barang->satuan }}</strong> &bull;
                                    Lokasi: {{ $barang->lokasiPenyimpanan->nama ?? 'Gudang' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div>
                        <label for="barang_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Barang
                            yang Dipinjam: <span class="text-red-500">*</span></label>
                        <select name="barang_id" id="barang_id" required
                            class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-2xs">
                            <option value="">-- Pilih Barang Bengkel --</option>
                            @foreach ($availableBarangs as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->nama }} ({{ $b->kode_barang }}) - Tersedia: {{ $b->stok_tersedia }}
                                    {{ $b->satuan }} [{{ strtoupper($b->jenis_barang) }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Input Jumlah -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="jumlah" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jumlah yang
                            Dipinjam: <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="jumlah" name="jumlah" value="1" min="1"
                                max="{{ $barang ? $barang->stok_tersedia : 100 }}" required
                                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-2xs">
                            <span
                                class="text-xs text-gray-500 font-semibold shrink-0">{{ $barang->satuan ?? 'Unit' }}</span>
                        </div>
                        @if ($barang)
                            <p class="text-[11px] text-gray-400 mt-1">Maksimal jumlah: {{ $barang->stok_tersedia }}
                                {{ $barang->satuan }}.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Bengkel Asal:</label>
                        <input type="text" readonly value="{{ $bengkel->nama ?? 'Bengkel Kejuruan' }}"
                            class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-600 text-sm shadow-2xs font-medium">
                    </div>
                </div>

                <!-- JADWAL PENGEMBALIAN (PRD 3.5: Hari yang sama untuk barang inventaris) -->
                @if (!$barang || $barang->jenis_barang === 'inventaris')
                    <div id="wrapper-kalender"
                        class="bg-amber-50/70 p-4 sm:p-5 rounded-2xl border border-amber-200 text-xs space-y-3">
                        <div class="flex items-center gap-2 text-amber-900 font-bold">
                            <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Jadwal Pengembalian Alat Inventaris</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 uppercase mb-1">Waktu
                                    Pinjam:</label>
                                <input type="text" value="Hari Ini (Saat disetujui Toolman)" readonly
                                    class="block w-full rounded-xl border-gray-300 bg-white text-gray-700 text-xs shadow-2xs font-medium py-2">
                            </div>

                            <div>
                                <label for="batas_kembali"
                                    class="block text-[11px] font-semibold text-gray-700 uppercase mb-1">Batas Pengembalian
                                    (Wajib): <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="batas_kembali" name="batas_kembali"
                                    value="{{ now()->setTime(16, 0)->format('Y-m-d\TH:i') }}" required
                                    class="block w-full rounded-xl border-amber-300 bg-white text-amber-950 text-xs font-bold shadow-2xs focus:ring-primary-500 focus:border-primary-500 py-2">
                                <p class="text-[11px] text-amber-800 font-medium mt-1">
                                    *Alat inventaris wajib dikembalikan pada hari yang sama sebelum jam bengkel tutup.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs text-emerald-900 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <strong class="font-bold block">Bahan Habis Pakai (BHP)</strong>
                            <p class="mt-0.5">Barang ini adalah bahan habis pakai dan tidak memerlukan batas waktu
                                pengembalian fisik.</p>
                        </div>
                    </div>
                @endif

                <!-- Catatan / Tujuan Penggunaan -->
                <div>
                    <label for="keperluan" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Keperluan
                        Penggunaan / Praktik: <span class="text-red-500">*</span></label>
                    <textarea id="keperluan" name="keperluan" rows="3" required
                        class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-2xs placeholder:text-gray-400"
                        placeholder="Contoh: Praktik konfigurasi jaringan dasar modul VLAN bersama Pak Yono di Lab 2..."></textarea>
                </div>

            </div>

            <!-- Footer Action -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('peminjam.katalog.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold rounded-xl shadow-2xs transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors flex items-center gap-1.5">
                    <span>Ajukan Peminjaman Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3">
                        </path>
                    </svg>
                </button>
            </div>

        </form>
    </div>
@endsection
