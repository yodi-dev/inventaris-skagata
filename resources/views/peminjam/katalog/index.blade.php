@extends('layouts.peminjam')

@section('title', 'Katalog Peminjam')

@section('content')
    <div class="space-y-6">

        <!-- Welcome Banner & Search -->
        <div
            class="bg-gradient-to-r from-primary-800 to-primary-600 rounded-2xl p-6 sm:p-8 text-white shadow-md flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-2 text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Mau pinjam alat apa hari ini? 🛠️</h2>
                <p class="text-primary-100 text-sm max-w-xl">Cari peralatan praktik atau bahan habis pakai yang tersedia di
                    bengkel jurusanmu dengan cepat dan mudah.</p>
            </div>

            <!-- Search Box di dalam Banner -->
            <div class="w-full md:w-80 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                    class="block w-full pl-10 pr-4 py-3 bg-white text-gray-900 rounded-xl text-sm focus:ring-2 focus:ring-primary-400 focus:outline-none shadow-sm"
                    placeholder="Cari alat atau bahan...">
            </div>
        </div>

        <!-- Filter Kategori & Tipe -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
            <!-- Filter Tabs Kecil -->
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button class="px-4 py-2 bg-primary-600 text-white text-xs font-semibold rounded-lg shadow-sm">Semua
                    Barang</button>
                <button
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold rounded-lg transition-colors">Alat
                    Inventaris</button>
                <button
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold rounded-lg transition-colors">Bahan
                    Habis Pakai</button>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-auto">
                <select
                    class="block w-full sm:w-auto text-xs border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 py-2">
                    <option value="tersedia">Hanya yang Tersedia</option>
                    <option value="semua">Tampilkan Semua Status</option>
                </select>
            </div>
        </div>

        <!-- Grid Katalog Barang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- CARD 1: Barang Inventaris (Tersedia) -->
            <div
                class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <!-- Thumbnail / Ilustrasi Placeholder -->
                    <div class="h-40 bg-slate-100 flex items-center justify-center relative border-b border-gray-100">
                        <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span
                            class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Tersedia (5 Unit)
                        </span>
                    </div>

                    <!-- Detail Info -->
                    <div class="p-5 space-y-2">
                        <div class="flex justify-between items-start">
                            <span
                                class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Inventaris</span>
                            <span class="text-xs font-mono text-gray-400">INV-TKJ-012</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base">Crimping Tool RJ45</h3>
                        <p class="text-xs text-gray-500 line-clamp-2">Alat untuk crimping konektor kabel LAN UTP ke jack
                            RJ45 standar industri.</p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="p-5 pt-0">
                    <a href="{{ route('peminjam.pengajuan.create') }}"
                        class="w-full block text-center py-2.5 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        Pinjam Alat Ini
                    </a>
                </div>
            </div>

            <!-- CARD 2: Bahan Habis Pakai (Tersedia) -->
            <div
                class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="h-40 bg-slate-100 flex items-center justify-center relative border-b border-gray-100">
                        <svg class="w-16 h-16 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span
                            class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Tersedia (2.5 Roll)
                        </span>
                    </div>

                    <div class="p-5 space-y-2">
                        <div class="flex justify-between items-start">
                            <span
                                class="text-[10px] font-semibold uppercase tracking-wider text-orange-600 bg-orange-50 px-2 py-0.5 rounded border border-orange-200">Habis
                                Pakai</span>
                            <span class="text-xs font-mono text-gray-400">BHP-TKJ-001</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base">Kabel UTP Cat6 (Belden)</h3>
                        <p class="text-xs text-gray-500 line-clamp-2">Kabel jaringan LAN berkualitas tinggi untuk instalasi
                            switch dan router.</p>
                    </div>
                </div>

                <div class="p-5 pt-0">
                    <a href="{{ route('peminjam.pengajuan.create') }}"
                        class="w-full block text-center py-2.5 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        Minta Bahan Ini
                    </a>
                </div>
            </div>

            <!-- CARD 3: Barang Inventaris (Sedang Dipinjam Orang Lain) -->
            <div
                class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col justify-between opacity-80">
                <div>
                    <div class="h-40 bg-slate-100 flex items-center justify-center relative border-b border-gray-100">
                        <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span
                            class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Sedang Dipinjam
                        </span>
                    </div>

                    <div class="p-5 space-y-2">
                        <div class="flex justify-between items-start">
                            <span
                                class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Inventaris</span>
                            <span class="text-xs font-mono text-gray-400">INV-TKJ-045</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base">Router Mikrotik RB951</h3>
                        <p class="text-xs text-gray-500 line-clamp-2">Router board untuk praktik konfigurasi jaringan LAN &
                            WAN.</p>
                    </div>
                </div>

                <div class="p-5 pt-0">
                    <button disabled
                        class="w-full block text-center py-2.5 px-4 bg-gray-100 text-gray-400 text-sm font-semibold rounded-lg cursor-not-allowed">
                        Stok Kosong / Dipinjam
                    </button>
                </div>
            </div>

        </div>

    </div>
@endsection
