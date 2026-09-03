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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                        </path>
                    </svg>
                    Tambah Barang Baru
                </a>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div
            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row gap-4 items-center justify-between">
            <!-- Search -->
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari kode atau nama barang...">
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select
                    class="block w-full sm:w-auto pl-3 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Tipe</option>
                    <option value="inventaris">Alat Inventaris</option>
                    <option value="bahan">Bahan Habis Pakai</option>
                </select>
                <select
                    class="block w-full sm:w-auto pl-3 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="dipinjam">Sedang Dipinjam</option>
                    <option value="rusak">Rusak</option>
                </select>
                <button
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-6 py-4 font-semibold">Kode & Nama Barang</th>
                            <th class="px-6 py-4 font-semibold">Tipe</th>
                            <th class="px-6 py-4 font-semibold">Stok/Kondisi</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">

                        <!-- Dummy Row 1 (Sedang Dipinjam - Disorot/Ditaruh Atas) -->
                        <tr class="bg-blue-50/30 hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Router Mikrotik RB951</p>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">INV-TKJ-045</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    Inventaris
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900">1 Unit</p>
                                <p class="text-xs text-green-600 mt-0.5">Kondisi: Baik</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5"></span>
                                    Dipinjam (Budi S.)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('toolman.barang.edit') }}" class="inline-flex text-gray-400 hover:text-primary-600 transition-colors p-1 rounded-md hover:bg-gray-100" title="Edit Barang">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                            </td>
                        </tr>

                        <!-- Dummy Row 2 (Barang Tersedia) -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Crimping Tool RJ45</p>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">INV-TKJ-012</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    Inventaris
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900">5 Unit</p>
                                <p class="text-xs text-green-600 mt-0.5">Kondisi: Baik</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Tersedia
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('toolman.barang.edit') }}" class="inline-flex text-gray-400 hover:text-primary-600 transition-colors p-1 rounded-md hover:bg-gray-100" title="Edit Barang">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                            </td>
                        </tr>

                        <!-- Dummy Row 3 (Bahan Habis Pakai) -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Kabel UTP Cat6 (Belden)</p>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">BHP-TKJ-001</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                    Habis Pakai
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 font-semibold">2.5 Roll</p>
                                <p class="text-xs text-gray-500 mt-0.5">Batas Min: 1 Roll</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Tersedia
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('toolman.barang.edit') }}" class="inline-flex text-gray-400 hover:text-primary-600 transition-colors p-1 rounded-md hover:bg-gray-100" title="Edit Barang">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                            </td>
                        </tr>

                        <!-- Dummy Row 4 (Barang Rusak) -->
                        <tr class="hover:bg-gray-50 transition-colors opacity-75">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Switch Hub 24 Port TP-Link</p>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">INV-TKJ-022</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    Inventaris
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900">1 Unit</p>
                                <p class="text-xs text-red-600 mt-0.5 font-medium">Kondisi: Rusak Total</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Perlu Perbaikan
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('toolman.barang.edit') }}" class="inline-flex text-gray-400 hover:text-primary-600 transition-colors p-1 rounded-md hover:bg-gray-100" title="Edit Barang">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Mockup -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-sm text-gray-600">Menampilkan 1 sampai 4 dari 45 barang</span>
                <div class="flex space-x-1">
                    <button
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-500 cursor-not-allowed">Mundur</button>
                    <button
                        class="px-3 py-1 text-sm border border-primary-600 rounded-md bg-primary-50 text-primary-700 font-medium">1</button>
                    <button
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">2</button>
                    <button
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">3</button>
                    <button
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">Maju</button>
                </div>
            </div>
        </div>

    </div>
@endsection
