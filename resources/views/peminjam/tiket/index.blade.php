@extends('layouts.peminjam')

@section('title', 'Tiket Peminjaman Saya')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header & Status Filter -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Tiket Peminjaman Saya 🎟️</h3>
                <p class="text-sm text-gray-500 mt-1">Pantau status persetujuan, barang aktif yang sedang dibawa, dan riwayat
                    peminjaman.</p>
            </div>

            <!-- Filter Status -->
            <div class="w-full sm:w-auto">
                <select
                    class="block w-full text-xs border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 py-2">
                    <option value="semua">Semua Status Tiket</option>
                    <option value="pending">Menunggu Acc (Pending)</option>
                    <option value="active">Sedang Dipinjam (Active)</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
        </div>

        <!-- List of Tickets -->
        <div class="space-y-5">

            <!-- TIKET 1: STATUS ACTIVE (Sedang Dipinjam - Ada Tombol Ajukan Pengembalian) -->
            <div class="bg-white border border-blue-200 rounded-xl shadow-sm overflow-hidden">
                <!-- Header Tiket -->
                <div
                    class="px-5 py-3 border-b border-blue-100 bg-blue-50/50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono font-bold text-blue-700">#TRX-2026-089</span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-xs text-gray-500">Dipinjam hari ini, 08:30 WIB</span>
                    </div>
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                            Aktif (Sedang Dipinjam)
                        </span>
                    </div>
                </div>

                <!-- Body Tiket -->
                <div class="p-5">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <!-- Rincian Barang -->
                        <div class="space-y-2 flex-1">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider text-gray-500">Daftar Alat
                                Dibawa:</h4>
                            <div class="bg-slate-50 border border-gray-200 rounded-lg p-3 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-medium text-gray-900">Crimping Tool RJ45 <span
                                            class="text-xs text-gray-500">(1 Unit)</span></span>
                                    <span class="text-xs font-mono text-gray-500">INV-TKJ-012</span>
                                </div>
                                <div class="flex justify-between items-center text-sm border-t border-gray-200 pt-2">
                                    <span class="font-medium text-gray-900">LAN Tester <span
                                            class="text-xs text-gray-500">(1 Unit)</span></span>
                                    <span class="text-xs font-mono text-gray-500">INV-TKJ-008</span>
                                </div>
                            </div>
                        </div>

                        <!-- Batas Waktu & Keperluan -->
                        <div
                            class="w-full md:w-72 bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col justify-between space-y-2">
                            <div>
                                <p class="text-xs text-gray-500">Batas Pengembalian</p>
                                <p class="text-sm font-bold text-red-600">Besok, 14:00 WIB</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tujuan Praktik</p>
                                <p class="text-xs font-medium text-gray-800 line-clamp-2">Praktik Jaringan Dasar Pak Yono
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tiket: Tombol Aksi Ajukan Pengembalian -->
                <div
                    class="px-5 py-3 border-t border-gray-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500">
                        💡 Silakan bawa kembali alat ke bengkel untuk divalidasi oleh Toolman.
                    </p>
                    <button
                        onclick="alert('Permohonan pengembalian diajukan! Silakan temui Toolman di bengkel untuk pengecekan fisik.')"
                        class="w-full sm:w-auto px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        Ajukan Pengembalian Alat
                    </button>
                </div>
            </div>

            <!-- TIKET 2: STATUS PENDING (Menunggu Persetujuan Toolman) -->
            <div class="bg-white border border-yellow-200 rounded-xl shadow-sm overflow-hidden">
                <div
                    class="px-5 py-3 border-b border-yellow-100 bg-yellow-50/50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono font-bold text-yellow-800">#TRX-2026-092</span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-xs text-gray-500">Diajukan 15 menit yang lalu</span>
                    </div>
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            Menunggu Persetujuan Toolman
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Kabel UTP Cat6 (Belden) <span
                                    class="text-xs font-normal text-orange-600">(10 Meter - Bahan Habis Pakai)</span></h4>
                            <p class="text-xs text-gray-500 mt-1">Keperluan: Merakit kabel patch cord untuk lab komputer.
                            </p>
                        </div>
                        <button
                            class="text-xs text-red-600 hover:text-red-700 font-medium border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                            Batalkan Request
                        </button>
                    </div>
                </div>
            </div>

            <!-- TIKET 3: STATUS SELESAI (Riwayat) -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden opacity-90">
                <div
                    class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono font-bold text-gray-600">#TRX-2026-070</span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-xs text-gray-500">Selesai pada 28 Februari 2026</span>
                    </div>
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Selesai & Dikembalikan
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <p class="font-bold text-gray-900">Router Mikrotik RB951 <span
                                    class="text-xs font-normal text-gray-500">(1 Unit)</span></p>
                            <p class="text-xs text-green-600 mt-0.5">✅ Kondisi fisik saat dikembalikan: Baik tanpa cacat</p>
                        </div>
                        <span class="text-xs text-gray-400 font-medium">Durasi: 1 Hari</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
