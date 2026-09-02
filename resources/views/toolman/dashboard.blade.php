@extends('layouts.admin')

@section('title', 'Dashboard Admin Bengkel')
@section('header_title', 'Dashboard Bengkel - Teknik Komputer Jaringan')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header & Quick Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Halo, Yodi! 👋</h3>
                <p class="text-sm text-gray-500 mt-1">Berikut adalah ringkasan aktivitas bengkel TKJ hari ini.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('toolman.sirkulasi.peminjaman') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Acc Peminjaman (3 Pending)
                </a>
                <a href="{{ route('toolman.pengadaan.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    Buat RAB Pengadaan
                </a>
            </div>
        </div>

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
                    <p class="text-2xl font-bold text-gray-900">12 <span
                            class="text-sm font-normal text-gray-500">Alat</span></p>
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
                    <p class="text-2xl font-bold text-gray-900">3 <span
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
                    <p class="text-2xl font-bold text-gray-900">4 <span
                            class="text-sm font-normal text-gray-500">Inventaris</span></p>
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
                    <p class="text-2xl font-bold text-gray-900">2 <span
                            class="text-sm font-normal text-gray-500">Bahan</span></p>
                </div>
            </div>
        </div>

        <!-- Data Tables Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Table 1: Barang Kembali Hari Ini -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h4 class="font-semibold text-gray-800">Jadwal Pengembalian Hari Ini</h4>
                    <a href="{{ route('toolman.sirkulasi.pengembalian') }}"
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
                            <!-- Dummy Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">Budi Santoso</p>
                                    <p class="text-xs text-gray-500">XI TKJ 1</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-800">Crimping Tool</p>
                                    <p class="text-xs text-gray-500">INV-TKJ-001</p>
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum Kembali
                                    </span>
                                </td>
                            </tr>
                            <!-- Dummy Row 2 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">Pak Yono</p>
                                    <p class="text-xs text-gray-500">Guru Produktif</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-800">Proyektor Epson</p>
                                    <p class="text-xs text-gray-500">INV-TKJ-015</p>
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Sudah Kembali
                                    </span>
                                </td>
                            </tr>
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
                                <th class="px-5 py-3 font-medium">Nama Bahan</th>
                                <th class="px-5 py-3 font-medium">Sisa Stok</th>
                                <th class="px-5 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <!-- Dummy Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">Kabel UTP Cat6 (Belden)</p>
                                    <p class="text-xs text-gray-500">Satuan: Roll</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-bold text-red-600">0.2 Roll</p>
                                    <p class="text-xs text-gray-500">Batas min: 1 Roll</p>
                                </td>
                                <td class="px-5 py-3">
                                    <button
                                        class="text-xs font-medium text-primary-600 border border-primary-600 rounded-lg px-2 py-1 hover:bg-primary-50 transition-colors">
                                        + List RAB
                                    </button>
                                </td>
                            </tr>
                            <!-- Dummy Row 2 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">Konektor RJ-45</p>
                                    <p class="text-xs text-gray-500">Satuan: Box</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-bold text-orange-500">1 Box</p>
                                    <p class="text-xs text-gray-500">Batas min: 3 Box</p>
                                </td>
                                <td class="px-5 py-3">
                                    <button
                                        class="text-xs font-medium text-primary-600 border border-primary-600 rounded-lg px-2 py-1 hover:bg-primary-50 transition-colors">
                                        + List RAB
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
