@extends('layouts.admin')

@section('title', 'Dashboard Superadmin')
@section('header_title', 'Dashboard Waka Sarpras')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Selamat datang, Bapak/Ibu Waka Sarpras</h2>
                <p class="text-sm text-gray-500 mt-1">Berikut adalah ringkasan inventaris dan sirkulasi barang hari ini.</p>
            </div>
            <div class="flex gap-2">
                <a href="#"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 shadow-sm transition-colors">
                    Unduh Laporan
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stat Card 1: Total Aset -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Aset Barang</p>
                    <p class="text-2xl font-bold text-gray-800">2,450</p>
                </div>
                <div class="bg-primary-50 text-primary-600 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>

            <!-- Stat Card 2: Sedang Dipinjam -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-gray-800">124</p>
                </div>
                <div class="bg-blue-50 text-blue-600 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>

            <!-- Stat Card 3: Barang Rusak -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Kondisi Rusak</p>
                    <p class="text-2xl font-bold text-gray-800">18</p>
                </div>
                <div class="bg-red-50 text-red-500 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Stat Card 4: Stok Habis / Menipis -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Stok Bahan Menipis</p>
                    <p class="text-2xl font-bold text-gray-800">7</p>
                </div>
                <div class="bg-amber-50 text-amber-500 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Kolom Kiri: Sering Dipinjam & Alert Stok (Porsi 2/3) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Alert Stok Menipis / Habis -->
                <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
                    <div class="bg-amber-50 px-6 py-4 border-b border-amber-100 flex items-center">
                        <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <h3 class="text-sm font-semibold text-amber-800">Alert: Stok Bahan Habis Pakai Menipis</h3>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex justify-between items-center bg-white border border-gray-100 p-3 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Kabel UTP Cat6 (Roll)</p>
                                    <p class="text-xs text-gray-500">Bengkel TKJ</p>
                                </div>
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Sisa 1
                                    Roll</span>
                            </div>
                            <div class="flex justify-between items-center bg-white border border-gray-100 p-3 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Tepung Terigu Protein Tinggi</p>
                                    <p class="text-xs text-gray-500">Bengkel Tata Boga</p>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded text-xs font-bold">Sisa 3
                                    Kg</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Barang Sering Dipinjam -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Barang Paling Sering Dipinjam (Bulan Ini)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-medium">Nama Barang</th>
                                    <th class="px-6 py-4 font-medium">Bengkel</th>
                                    <th class="px-6 py-4 font-medium text-center">Total Peminjaman</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">Tang Crimping RJ45</td>
                                    <td class="px-6 py-4">Teknik Komputer Jaringan</td>
                                    <td class="px-6 py-4 text-center"><span class="font-bold text-primary-600">45x</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">Multitester Digital</td>
                                    <td class="px-6 py-4">Teknik Audio Video</td>
                                    <td class="px-6 py-4 text-center"><span class="font-bold text-primary-600">38x</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">Mixer Roti Berdiri</td>
                                    <td class="px-6 py-4">Tata Boga</td>
                                    <td class="px-6 py-4 text-center"><span class="font-bold text-primary-600">22x</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Kondisi Barang (Porsi 1/3) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-fit">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Kondisi Inventaris</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Progress 1 -->
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-gray-700">Kondisi Baik</span>
                            <span class="text-gray-500">92%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>

                    <!-- Progress 2 -->
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-gray-700">Perlu Perbaikan</span>
                            <span class="text-gray-500">6%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-amber-400 h-2.5 rounded-full" style="width: 6%"></div>
                        </div>
                    </div>

                    <!-- Progress 3 -->
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-gray-700">Rusak Berat / Afkir</span>
                            <span class="text-gray-500">2%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-red-500 h-2.5 rounded-full" style="width: 2%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
