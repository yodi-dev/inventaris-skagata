@extends('layouts.peminjam')

@section('title', 'Katalog Barang')

@section('content')
<div x-data="katalogPeminjamApp()" x-cloak class="space-y-6">

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 left-4 sm:left-auto sm:right-6 z-50 max-w-sm bg-white border shadow-xl rounded-2xl p-4 flex items-start gap-3"
         :class="{
             'border-emerald-200 bg-emerald-50/90 text-emerald-900': toast.type === 'success',
             'border-amber-200 bg-amber-50/90 text-amber-900': toast.type === 'warning',
             'border-blue-200 bg-blue-50/90 text-blue-900': toast.type === 'info',
             'border-rose-200 bg-rose-50/90 text-rose-900': toast.type === 'error'
         }">
        <div class="shrink-0 mt-0.5">
            <template x-if="toast.type === 'success'">
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </template>
            <template x-if="toast.type === 'warning'">
                <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </template>
            <template x-if="toast.type === 'info'">
                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </template>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-xs sm:text-sm font-bold truncate" x-text="toast.title"></h4>
            <p class="text-xs text-gray-600 mt-0.5" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- ============================================================= -->
    <!-- 1. HERO BANNER (MOBILE-OPTIMIZED WITH COMPACT TOUCH CONTROLS)  -->
    <!-- ============================================================= -->
    <div class="bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-5 sm:p-7 text-white shadow-md relative overflow-hidden">
        <!-- Background Graphic Elements -->
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-12 top-4 w-24 h-24 bg-emerald-400/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <!-- Greeting & Info -->
            <div class="space-y-1.5 text-left">
                <div class="inline-flex items-center gap-1.5 bg-emerald-900/50 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold text-emerald-200 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Bengkel TKJ &bull; SMKN 3 Yogyakarta</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight leading-tight">
                    Mau pinjam alat apa hari ini? 🛠️
                </h1>
                <p class="text-emerald-100/90 text-xs sm:text-sm max-w-lg leading-relaxed">
                    Cari alat praktik atau bahan habis pakai yang tersedia di bengkel jurusanmu dengan mudah dan transparan.
                </p>
            </div>

            <!-- Search Bar in Banner -->
            <div class="w-full md:w-88 space-y-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="search"
                           placeholder="Cari alat (Crimping, Router, Solder)..."
                           class="block w-full pl-10 pr-9 py-2.5 sm:py-3 bg-white text-gray-900 rounded-2xl text-xs sm:text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-400 focus:outline-none shadow-sm transition-all">
                    <!-- Clear search button -->
                    <button x-show="search" 
                            @click="search = ''" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Quick Keywords Tag Chips (Click to Search) -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 text-[11px] text-emerald-100">
                    <span class="text-emerald-300/80 shrink-0">Populer:</span>
                    <button @click="search = 'Crimping'" class="shrink-0 px-2 py-0.5 rounded-md bg-white/10 hover:bg-white/20 transition-colors">Crimping</button>
                    <button @click="search = 'Mikrotik'" class="shrink-0 px-2 py-0.5 rounded-md bg-white/10 hover:bg-white/20 transition-colors">Mikrotik</button>
                    <button @click="search = 'Kabel'" class="shrink-0 px-2 py-0.5 rounded-md bg-white/10 hover:bg-white/20 transition-colors">Kabel UTP</button>
                    <button @click="search = 'Tester'" class="shrink-0 px-2 py-0.5 rounded-md bg-white/10 hover:bg-white/20 transition-colors">LAN Tester</button>
                    <button @click="search = 'Solder'" class="shrink-0 px-2 py-0.5 rounded-md bg-white/10 hover:bg-white/20 transition-colors">Solder</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 2. FILTER & KATEGORI (TOUCH-FRIENDLY SCROLLABLE CHIPS)         -->
    <!-- ============================================================= -->
    <div class="space-y-3">
        <!-- Horizontal Scrollable Category Chips -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 w-full sm:w-auto text-xs font-semibold">
                <!-- Chip: Semua -->
                <button @click="filterTipe = 'all'"
                        class="px-3.5 py-2 rounded-xl shrink-0 transition-all flex items-center gap-1.5 shadow-2xs"
                        :class="filterTipe === 'all' 
                            ? 'bg-primary-600 text-white shadow-sm ring-2 ring-primary-200' 
                            : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <span>Semua Barang</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full"
                          :class="filterTipe === 'all' ? 'bg-primary-800 text-white' : 'bg-gray-100 text-gray-600'"
                          x-text="items.length"></span>
                </button>

                <!-- Chip: Alat Inventaris -->
                <button @click="filterTipe = 'inventaris'"
                        class="px-3.5 py-2 rounded-xl shrink-0 transition-all flex items-center gap-1.5 shadow-2xs"
                        :class="filterTipe === 'inventaris' 
                            ? 'bg-primary-600 text-white shadow-sm ring-2 ring-primary-200' 
                            : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <span>🛠️ Alat Praktik (Wajib Kembali)</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full"
                          :class="filterTipe === 'inventaris' ? 'bg-primary-800 text-white' : 'bg-gray-100 text-gray-600'"
                          x-text="items.filter(i => i.tipe === 'inventaris').length"></span>
                </button>

                <!-- Chip: Bahan Habis Pakai -->
                <button @click="filterTipe = 'bahan'"
                        class="px-3.5 py-2 rounded-xl shrink-0 transition-all flex items-center gap-1.5 shadow-2xs"
                        :class="filterTipe === 'bahan' 
                            ? 'bg-amber-600 text-white shadow-sm ring-2 ring-amber-200' 
                            : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <span>📦 Bahan Habis Pakai (BHP)</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full"
                          :class="filterTipe === 'bahan' ? 'bg-amber-800 text-white' : 'bg-gray-100 text-gray-600'"
                          x-text="items.filter(i => i.tipe === 'bahan').length"></span>
                </button>
            </div>

            <!-- Reset dummy data button for testing -->
            <button @click="resetDummyData()" 
                    class="hidden sm:inline-flex items-center text-xs text-gray-400 hover:text-gray-600 hover:underline shrink-0">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Reset Data Demo
            </button>
        </div>

        <!-- Sub-filter row (Availability status & Sorting) -->
        <div class="flex flex-wrap items-center justify-between gap-2.5 text-xs text-gray-600 bg-white p-2.5 sm:p-3 rounded-2xl border border-gray-200 shadow-2xs">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full sm:w-auto">
                <span class="text-gray-400 font-medium shrink-0">Filter Status:</span>
                <select x-model="filterStatus" class="text-xs bg-gray-50 border-gray-200 rounded-xl py-1.5 px-2.5 focus:ring-primary-500 focus:border-primary-500 font-medium">
                    <option value="all">Semua Status Stok</option>
                    <option value="tersedia">✅ Hanya yang Tersedia</option>
                    <option value="mepet">⚠️ Stok Menipis</option>
                    <option value="dipinjam">🔒 Sedang Dipinjam / Habis</option>
                </select>

                <select x-model="filterBengkel" class="text-xs bg-gray-50 border-gray-200 rounded-xl py-1.5 px-2.5 focus:ring-primary-500 focus:border-primary-500 font-medium">
                    <option value="TKJ">Bengkel TKJ (Jurusan Saya)</option>
                    <option value="all">Semua Bengkel SMKN 3</option>
                    <option value="TAV">Teknik Audio Video (TAV)</option>
                    <option value="TITL">Teknik Listrik (TITL)</option>
                    <option value="TKR">Teknik Otomotif (TKR)</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="flex items-center justify-between w-full sm:w-auto gap-2">
                <span class="text-gray-500 text-[11px]" x-text="'Ditemukan ' + filteredItems.length + ' barang'"></span>
                <template x-if="search || filterTipe !== 'all' || filterStatus !== 'all' || filterBengkel !== 'TKJ'">
                    <button @click="resetFilters()" class="text-primary-600 hover:text-primary-700 font-semibold underline text-xs">
                        Reset
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 3. GRID KATALOG BARANG (MOBILE FIRST 1-COL / 2-COL SM / 3-COL LG) -->
    <!-- ============================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        <template x-for="item in filteredItems" :key="item.id">
            <div class="bg-white border rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between transition-all hover:shadow-md hover:border-primary-200 group"
                 :class="{
                     'border-gray-200': item.stok > 0,
                     'border-gray-200 bg-slate-50/50 opacity-80': item.stok === 0
                 }">
                
                <!-- Card Header with Icon / Badge -->
                <div class="p-4 sm:p-5">
                    <!-- Top Info row: Category Badge & Item Code -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <template x-if="item.tipe === 'inventaris'">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/80">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                Alat Inventaris
                            </span>
                        </template>
                        <template x-if="item.tipe === 'bahan'">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/80">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Bahan Habis Pakai
                            </span>
                        </template>

                        <span class="font-mono text-[11px] text-gray-400 font-semibold" x-text="item.kode"></span>
                    </div>

                    <!-- Item Thumbnail / Illustration Box (Clickable to open Detail) -->
                    <div @click="openItemDetail(item)" 
                         class="cursor-pointer mb-3.5 p-4 rounded-xl flex items-center justify-between transition-transform group-hover:scale-[1.01]"
                         :class="{
                             'bg-emerald-50/60 border border-emerald-100': item.tipe === 'inventaris',
                             'bg-amber-50/60 border border-amber-100': item.tipe === 'bahan'
                         }">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-2xs shrink-0"
                                 :class="{
                                     'bg-emerald-100 text-emerald-700': item.tipe === 'inventaris',
                                     'bg-amber-100 text-amber-700': item.tipe === 'bahan'
                                 }"
                                 x-text="item.icon">
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-500 flex items-center gap-1 font-medium">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <span x-text="item.lokasi"></span>
                                </p>
                                <p class="text-xs text-gray-400 font-normal mt-0.5" x-text="item.bengkel"></p>
                            </div>
                        </div>

                        <!-- Stock Badge status -->
                        <div>
                            <template x-if="item.stok > 2">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span x-text="'Tersedia: ' + item.stok + ' ' + item.satuan"></span>
                                </span>
                            </template>
                            <template x-if="item.stok > 0 && item.stok <= 2">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span x-text="'Sisa: ' + item.stok + ' ' + item.satuan"></span>
                                </span>
                            </template>
                            <template x-if="item.stok === 0">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-200 text-gray-600">
                                    Sedang Dipinjam
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Title & Spec -->
                    <div @click="openItemDetail(item)" class="cursor-pointer space-y-1">
                        <h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-primary-600 transition-colors leading-snug" 
                            x-text="item.nama"></h3>
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed" x-text="item.deskripsi"></p>
                    </div>
                </div>

                <!-- Card Footer Action Buttons -->
                <div class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                    <!-- Jika Stok Tersedia -->
                    <template x-if="item.stok > 0">
                        <div class="flex items-center gap-2">
                            <!-- Quick Detail Button -->
                            <button @click="openItemDetail(item)" 
                                    class="p-2.5 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl border border-gray-200 transition-colors"
                                    title="Lihat Rincian Alat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            <!-- Add to Cart or Stepper if already in cart -->
                            <template x-if="!isInCart(item.id)">
                                <button @click="addToCart(item, 1)" 
                                        type="button"
                                        class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all flex items-center justify-center gap-1.5 active:scale-98"
                                        :class="item.tipe === 'inventaris' 
                                            ? 'bg-primary-600 hover:bg-primary-700 text-white' 
                                            : 'bg-amber-600 hover:bg-amber-700 text-white'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    <span x-text="item.tipe === 'inventaris' ? 'Pinjam Alat' : 'Minta Bahan'"></span>
                                </button>
                            </template>

                            <!-- When already in Cart: Show Stepper Controls -->
                            <template x-if="isInCart(item.id)">
                                <div class="flex-1 flex items-center justify-between bg-primary-50 border border-primary-300 rounded-xl px-2 py-1">
                                    <button @click="decreaseQty(item.id)" class="w-7 h-7 rounded-lg bg-white text-primary-700 font-bold hover:bg-primary-100 flex items-center justify-center shadow-2xs">
                                        -
                                    </button>
                                    <span class="text-xs font-bold text-primary-900" x-text="getCartQty(item.id) + ' ' + item.satuan"></span>
                                    <button @click="increaseQty(item.id)" 
                                            :disabled="getCartQty(item.id) >= item.stok"
                                            class="w-7 h-7 rounded-lg bg-white text-primary-700 font-bold hover:bg-primary-100 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center shadow-2xs">
                                        +
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Jika Stok Kosong -->
                    <template x-if="item.stok === 0">
                        <button disabled 
                                class="w-full py-2.5 px-4 bg-gray-100 text-gray-400 text-xs sm:text-sm font-semibold rounded-xl cursor-not-allowed flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Stok Kosong / Dipinjam
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <template x-if="filteredItems.length === 0">
        <div class="bg-white rounded-3xl p-8 sm:p-12 text-center border border-gray-200 shadow-xs max-w-md mx-auto">
            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 mx-auto flex items-center justify-center mb-4 text-2xl">
                🔍
            </div>
            <h3 class="text-base font-bold text-gray-900">Barang tidak ditemukan</h3>
            <p class="text-xs text-gray-500 mt-1">Coba gunakan kata kunci lain atau ubah filter tipe dan status ketersediaan.</p>
            <button @click="resetFilters()" 
                    class="mt-5 px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 font-semibold rounded-xl text-xs transition-colors">
                Reset Semua Filter
            </button>
        </div>
    </template>

    <!-- ============================================================= -->
    <!-- 4. FLOATING ACTION CART BAR (MOBILE BOTTOM SHEET TOGGLE)       -->
    <!-- ============================================================= -->
    <div x-show="cart.length > 0" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-16 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-16 opacity-0"
         class="fixed bottom-16 sm:bottom-6 left-4 right-4 sm:left-auto sm:right-6 z-40 sm:max-w-md">
        <div class="bg-gradient-to-r from-gray-900 to-slate-800 text-white rounded-2xl p-3.5 sm:p-4 shadow-2xl border border-white/10 flex items-center justify-between gap-3">
            <div class="flex items-center space-x-3 cursor-pointer" @click="cartDrawerOpen = true">
                <div class="w-10 h-10 rounded-xl bg-primary-500 text-white flex items-center justify-center font-bold relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-extrabold border-2 border-gray-900"
                          x-text="totalCartCount"></span>
                </div>
                <div>
                    <p class="text-xs font-bold leading-tight" x-text="totalCartCount + ' Barang Dipilih'"></p>
                    <p class="text-[11px] text-gray-300" x-text="cartInventarisCount + ' Alat, ' + cartBahanCount + ' BHP'"></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button @click="openCheckoutModal()" 
                        class="px-4 py-2 bg-primary-500 hover:bg-primary-600 active:scale-98 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1">
                    <span>Ajukan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 5. MODAL DETAIL BARANG (BOTTOM SHEET DI MOBILE / MODAL DI PC)  -->
    <!-- ============================================================= -->
    <div x-show="detailModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-detail" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="detailModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="detailModalOpen = false"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-screen items-end sm:items-center justify-center p-0 sm:p-4 text-center">
            <div x-show="detailModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 opacity-0"
                 x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 opacity-0"
                 class="relative transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-lg border border-gray-100 max-h-[92vh] flex flex-col">
                
                <!-- Mobile Drag Indicator -->
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mt-3 sm:hidden shrink-0"></div>

                <!-- Modal Header -->
                <div class="p-5 sm:p-6 pb-4 border-b border-gray-100 flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-2xs shrink-0"
                             :class="{
                                 'bg-emerald-100 text-emerald-700': activeItem?.tipe === 'inventaris',
                                 'bg-amber-100 text-amber-700': activeItem?.tipe === 'bahan'
                             }"
                             x-text="activeItem?.icon">
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded"
                                      :class="activeItem?.tipe === 'inventaris' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                      x-text="activeItem?.tipe === 'inventaris' ? 'Alat Inventaris' : 'Bahan Habis Pakai'"></span>
                                <span class="font-mono text-xs text-gray-400 font-semibold" x-text="activeItem?.kode"></span>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-1" x-text="activeItem?.nama"></h3>
                        </div>
                    </div>

                    <button @click="detailModalOpen = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-5 sm:p-6 space-y-4 overflow-y-auto flex-1">
                    <!-- Stock & Location Badges -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Ketersediaan</p>
                            <p class="text-sm font-bold mt-0.5" 
                               :class="activeItem?.stok > 0 ? 'text-emerald-700' : 'text-rose-600'"
                               x-text="activeItem?.stok > 0 ? activeItem?.stok + ' ' + activeItem?.satuan + ' Tersedia' : 'Kosong / Dipinjam'"></p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Lokasi Penyimpanan</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5" x-text="activeItem?.lokasi"></p>
                        </div>
                    </div>

                    <!-- Description & Specs -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Deskripsi & Spesifikasi</h4>
                        <p class="text-xs sm:text-sm text-gray-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-gray-100" 
                           x-text="activeItem?.deskripsiLengkap || activeItem?.deskripsi"></p>
                    </div>

                    <!-- Business Rules Notification (From PRD) -->
                    <div class="p-3.5 rounded-xl text-xs border flex items-start gap-2.5"
                         :class="activeItem?.tipe === 'inventaris' 
                             ? 'bg-blue-50/70 border-blue-200 text-blue-900' 
                             : 'bg-amber-50/70 border-amber-200 text-amber-900'">
                        <span class="text-base">ℹ️</span>
                        <div>
                            <strong class="font-semibold block" x-text="activeItem?.tipe === 'inventaris' ? 'Ketentuan Alat Inventaris' : 'Ketentuan Bahan Habis Pakai'"></strong>
                            <p class="mt-0.5" x-text="activeItem?.tipe === 'inventaris' 
                                ? 'Alat wajib dikembalikan pada hari yang sama sebelum jam bengkel berakhir (15:30 WIB). Pastikan kondisi fisik kembali dalam keadaan baik.' 
                                : 'Bahan habis pakai ini tidak perlu dikembalikan. Pengajuan akan memotong stok secara permanen setelah disetujui Toolman.'"></p>
                        </div>
                    </div>

                    <!-- Quantity Input (If Available) -->
                    <template x-if="activeItem?.stok > 0">
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div>
                                <span class="text-xs font-bold text-gray-700 block">Jumlah yang Diminta:</span>
                                <span class="text-[11px] text-gray-400" x-text="'Maksimal ' + activeItem?.stok + ' ' + activeItem?.satuan"></span>
                            </div>

                            <!-- Stepper -->
                            <div class="flex items-center border border-gray-300 rounded-xl bg-white shadow-2xs overflow-hidden">
                                <button type="button" @click="detailQty > 1 && detailQty--" class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 font-bold text-base">-</button>
                                <span class="px-3 py-1.5 text-sm font-bold text-gray-900 min-w-8 text-center" x-text="detailQty"></span>
                                <button type="button" @click="detailQty < activeItem.stok && detailQty++" class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 font-bold text-base">+</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer CTA -->
                <div class="p-4 sm:p-5 bg-gray-50 border-t border-gray-100 flex items-center gap-3">
                    <button @click="detailModalOpen = false" 
                            type="button" 
                            class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>

                    <template x-if="activeItem?.stok > 0">
                        <button @click="addToCart(activeItem, detailQty); detailModalOpen = false;" 
                                type="button" 
                                class="flex-2 py-2.5 px-4 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5"
                                :class="activeItem?.tipe === 'inventaris' ? 'bg-primary-600 hover:bg-primary-700' : 'bg-amber-600 hover:bg-amber-700'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span>+ Masukkan Keranjang</span>
                        </button>
                    </template>
                </div>

            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 6. DRAWER KERANJANG PEMINJAMAN (TOUCH FRIENDLY DRAWER)        -->
    <!-- ============================================================= -->
    <div x-show="cartDrawerOpen" 
         class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="cartDrawerOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="cartDrawerOpen = false"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="cartDrawerOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">

                <!-- Drawer Header -->
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-none">Keranjang Peminjaman</h3>
                            <p class="text-xs text-gray-500 mt-1" x-text="totalCartCount + ' item siap diajukan'"></p>
                        </div>
                    </div>
                    <button @click="cartDrawerOpen = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Drawer Body (Items list) -->
                <div class="p-5 flex-1 overflow-y-auto space-y-3">
                    <template x-if="cart.length === 0">
                        <div class="py-12 text-center text-gray-400">
                            <div class="w-16 h-16 rounded-full bg-gray-100 mx-auto flex items-center justify-center text-2xl mb-3">
                                🛒
                            </div>
                            <h4 class="text-sm font-bold text-gray-800">Keranjang masih kosong</h4>
                            <p class="text-xs text-gray-500 mt-1">Pilih alat praktik atau bahan dari katalog untuk dipinjam.</p>
                        </div>
                    </template>

                    <template x-for="cItem in cart" :key="cItem.id">
                        <div class="p-3.5 bg-white border border-gray-200 rounded-2xl shadow-2xs flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0"
                                     :class="cItem.tipe === 'inventaris' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
                                     x-text="cItem.icon">
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] font-bold uppercase px-1.5 py-0.2 rounded"
                                              :class="cItem.tipe === 'inventaris' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                              x-text="cItem.tipe === 'inventaris' ? 'Inventaris' : 'BHP'"></span>
                                        <span class="text-[10px] font-mono text-gray-400" x-text="cItem.kode"></span>
                                    </div>
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 truncate mt-0.5" x-text="cItem.nama"></p>
                                    <p class="text-[11px] text-gray-500" x-text="'Stok maks: ' + cItem.stok + ' ' + cItem.satuan"></p>
                                </div>
                            </div>

                            <!-- Stepper & Trash -->
                            <div class="flex items-center space-x-2 shrink-0">
                                <div class="flex items-center border border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
                                    <button @click="decreaseQty(cItem.id)" class="px-2 py-1 text-gray-600 hover:bg-gray-200 font-bold text-xs">-</button>
                                    <span class="px-2 py-1 text-xs font-bold text-gray-800 min-w-5 text-center" x-text="cItem.qty"></span>
                                    <button @click="increaseQty(cItem.id)" 
                                            :disabled="cItem.qty >= cItem.stok"
                                            class="px-2 py-1 text-gray-600 hover:bg-gray-200 font-bold text-xs disabled:opacity-30 disabled:cursor-not-allowed">+</button>
                                </div>
                                <button @click="removeFromCart(cItem.id)" class="text-gray-400 hover:text-red-600 p-1 transition-colors" title="Hapus dari keranjang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Footer Checkout -->
                <div class="p-5 border-t border-gray-100 bg-slate-50 space-y-3">
                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-xs space-y-1">
                        <div class="flex justify-between text-gray-600">
                            <span>Alat Inventaris (Wajib Kembali):</span>
                            <span class="font-bold text-emerald-700" x-text="cartInventarisCount + ' item'"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Bahan Habis Pakai (Permanen):</span>
                            <span class="font-bold text-amber-700" x-text="cartBahanCount + ' item'"></span>
                        </div>
                    </div>

                    <button @click="openCheckoutModal(); cartDrawerOpen = false;"
                            :disabled="cart.length === 0"
                            class="w-full py-3 bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                        <span>Lanjut Buat Pengajuan Peminjaman</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 7. MODAL CHECKOUT / PENGAJUAN (SMART LOGIC PER PRD)           -->
    <!-- ============================================================= -->
    <div x-show="checkoutModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-checkout" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="checkoutModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="checkoutModalOpen = false"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-3 sm:p-4 text-center">
            <div x-show="checkoutModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="scale-100 opacity-100"
                 x-transition:leave-end="scale-95 opacity-0"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-gray-100">
                
                <form @submit.prevent="submitBorrowRequest()">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-5 sm:p-6 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded bg-white/20 text-emerald-300">
                                SIBENKA SKAGATA
                            </span>
                            <h3 class="text-base sm:text-lg font-bold mt-1">Form Pengajuan Peminjaman 📋</h3>
                            <p class="text-xs text-slate-300 mt-0.5">Pemohon: <strong>Budi Santoso</strong> &bull; XI TKJ 1</p>
                        </div>
                        <button @click="checkoutModalOpen = false" type="button" class="text-slate-400 hover:text-white p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-5 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- Daftar Ringkasan Barang -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Barang yang Diajukan:</h4>
                            <div class="bg-slate-50 rounded-xl p-3 border border-gray-200 divide-y divide-gray-100 text-xs space-y-2">
                                <template x-for="item in cart" :key="item.id">
                                    <div class="flex items-center justify-between pt-1.5 first:pt-0">
                                        <div class="flex items-center space-x-2">
                                            <span x-text="item.icon"></span>
                                            <span class="font-bold text-gray-900" x-text="item.nama"></span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded"
                                                  :class="item.tipe === 'inventaris' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                                  x-text="item.tipe === 'inventaris' ? 'Alat' : 'BHP'"></span>
                                        </div>
                                        <span class="font-bold text-gray-800" x-text="item.qty + ' ' + item.satuan"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- SISTEM PINTAR JADWAL (PRD SECTION 3 & 6: Kalender sembunyi otomatis jika HANYA bahan habis pakai!) -->
                        <template x-if="cartInventarisCount > 0">
                            <!-- Kasus: Ada Alat Inventaris -> Wajib isi waktu kembali -->
                            <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-4 space-y-3 text-xs">
                                <div class="flex items-center gap-2 text-amber-900 font-bold">
                                    <span>⏰</span>
                                    <span>Jadwal Pengembalian Alat Inventaris</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700">Waktu Pinjam Mulai:</label>
                                        <input type="text" value="Hari Ini (Sekarang)" readonly class="mt-1 block w-full text-xs bg-white border-gray-300 rounded-lg shadow-2xs font-medium text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700">Batas Pengembalian (Wajib):</label>
                                        <input type="text" value="Hari Ini, 15:30 WIB" readonly class="mt-1 block w-full text-xs bg-white border-amber-300 text-amber-900 font-bold rounded-lg shadow-2xs">
                                    </div>
                                </div>
                                <p class="text-[11px] text-amber-800 font-medium">
                                    *Aturan Bengkel: Alat wajib dikembalikan pada hari yang sama sebelum jam bengkel berakhir.
                                </p>
                            </div>
                        </template>

                        <template x-if="cartInventarisCount === 0 && cartBahanCount > 0">
                            <!-- Kasus: Hanya Bahan Habis Pakai -> Kalender disembunyikan otomatis -->
                            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs text-emerald-900 flex items-start gap-2.5">
                                <span class="text-base">✅</span>
                                <div>
                                    <strong class="font-bold block">Sistem Pintar: Bahan Habis Pakai</strong>
                                    <p class="mt-0.5">Semua item yang kamu minta adalah Bahan Habis Pakai (BHP). Tidak diperlukan tanggal pengembalian dan barang tidak perlu dikembalikan.</p>
                                </div>
                            </div>
                        </template>

                        <!-- Tujuan Penggunaan / Mata Pelajaran -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tujuan Penggunaan / Praktik:</label>
                            <select x-model="checkoutForm.tujuan" class="w-full text-xs border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 py-2">
                                <option value="Praktik Jaringan Dasar (Pak Yono)">Praktik Jaringan Dasar (Pak Yono)</option>
                                <option value="Praktikum Modul Routing & Switch Mikrotik">Praktikum Modul Routing & Switch Mikrotik</option>
                                <option value="Persiapan Uji Kompetensi Keahlian (UKK)">Persiapan Uji Kompetensi Keahlian (UKK)</option>
                                <option value="Tugas Mandiri / Remidial Lab">Tugas Mandiri / Remidial Lab</option>
                            </select>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan (Opsional):</label>
                            <textarea x-model="checkoutForm.catatan" 
                                      rows="2" 
                                      placeholder="Contoh: Digunakan untuk kelompok 3 di meja 4..."
                                      class="w-full text-xs border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 placeholder:text-gray-400"></textarea>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 sm:p-5 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2.5">
                        <button type="button" @click="checkoutModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm transition-all flex items-center gap-1.5 active:scale-98">
                            <span>Kirim Pengajuan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 8. MODAL SUKSES PENGAJUAN (TIKET BERHASIL DIBUAT)             -->
    <!-- ============================================================= -->
    <div x-show="successModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-center shadow-2xl transition-all w-full max-w-sm border border-gray-100">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl mb-4">
                    🎉
                </div>
                <h3 class="text-lg font-black text-gray-900">Pengajuan Berhasil Dikirim!</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Tiket peminjamanmu sudah diteruskan ke Toolman bengkel untuk diverifikasi.
                </p>

                <!-- Ticket Info Pill -->
                <div class="my-4 p-3 bg-slate-50 border border-gray-200 rounded-xl text-center">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Nomor Tiket Peminjaman:</p>
                    <p class="text-base font-mono font-black text-primary-700 mt-0.5" x-text="lastTicketId"></p>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('peminjam.tiket.index') }}" 
                       class="w-full block py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-colors">
                        Lihat Tiket Saya 🎟️
                    </a>
                    <button @click="successModalOpen = false" 
                            type="button" 
                            class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors">
                        Lanjut Cari Barang Lain
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Alpine.js Application Logic -->
<script>
    function katalogPeminjamApp() {
        const DEFAULT_ITEMS = [
            {
                id: 1,
                kode: "INV-TKJ-012",
                nama: "Crimping Tool RJ45 (Cat5/Cat6)",
                icon: "🛠️",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 5,
                satuan: "Unit",
                lokasi: "Lemari Toolkit A-01",
                deskripsi: "Tang press konektor kabel LAN RJ45 standar industri kejuruan.",
                deskripsiLengkap: "Crimping tool multifungsi untuk kabel UTP Cat5e dan Cat6 dengan cutter dan wire stripper terintegrasi. Kondisi prima dan presisi tinggi."
            },
            {
                id: 2,
                kode: "BHP-TKJ-001",
                nama: "Kabel UTP Cat6 (Belden Original)",
                icon: "📦",
                tipe: "bahan",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 3,
                satuan: "Roll",
                lokasi: "Rak Bahan B-02",
                deskripsi: "Kabel jaringan LAN Gigabit tembaga murni untuk instalasi router & PC lab.",
                deskripsiLengkap: "Kabel UTP Cat6 kecepatan gigabit 1000 Mbps. Khusus bahan praktik pembuatan patch cord dan perakitan jaringan komputer siswa."
            },
            {
                id: 3,
                kode: "INV-TKJ-045",
                nama: "Router Mikrotik RB951Ui-2HnD",
                icon: "📶",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 0,
                satuan: "Unit",
                lokasi: "Rak Jaringan 01",
                deskripsi: "Router board 5 port Ethernet dengan wifi 2.4GHz untuk praktikum routing.",
                deskripsiLengkap: "RouterOS level 4 untuk praktik konfigurasi VLAN, DHCP Server, Firewall NAT, dan Hotspot login. Saat ini 4 unit sedang aktif dipinjam kelas XII."
            },
            {
                id: 4,
                kode: "INV-TKJ-008",
                nama: "Digital LAN Cable Tester RJ45/RJ11",
                icon: "📟",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 4,
                satuan: "Unit",
                lokasi: "Lemari Toolkit A-02",
                deskripsi: "Alat uji kontinuitas dan urutan pin kabel jaringan T568A / T568B.",
                deskripsiLengkap: "Tester kabel otomatis dengan lampu indikator LED 1-8 dan pin ground. Memeriksa kabel straight-through dan cross-over dengan cepat."
            },
            {
                id: 5,
                kode: "BHP-TKJ-004",
                nama: "Konektor RJ45 Cat6 AMP (Pack)",
                icon: "🔌",
                tipe: "bahan",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 12,
                satuan: "Pack",
                lokasi: "Laci Bahan B-01",
                deskripsi: "Konektor modular plug emas 50 micron isi 50 pcs per kemasan.",
                deskripsiLengkap: "Konektor Cat6 bermutu tinggi dengan pin lapis emas untuk transmisi sinyal stabil dan tahan oksidasi."
            },
            {
                id: 6,
                kode: "INV-TKJ-023",
                nama: "Digital Multimeter Sanwa CD800a",
                icon: "⚡",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 1,
                satuan: "Unit",
                lokasi: "Lemari Alat Ukur C-01",
                deskripsi: "Multimeter digital akurat untuk pengukuran tegangan DC/AC, resistansi & kontinuitas.",
                deskripsiLengkap: "Multitester Sanwa original dengan proteksi overload dan buzzer kontinuitas. Tersisa 1 unit di lemari alat ukur."
            },
            {
                id: 7,
                kode: "INV-TKJ-019",
                nama: "Solder Station Digital Atten 938D",
                icon: "🔥",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 6,
                satuan: "Unit",
                lokasi: "Meja Praktik Lab 2",
                deskripsi: "Pemanas solder suhu teratur dengan pengatur suhu digital cepat panas.",
                deskripsiLengkap: "Stasiun solder daya 60W dengan fitur auto-sleep dan mata solder keramik untuk perbaikan komponen elektronika komputer."
            },
            {
                id: 8,
                kode: "BHP-TKJ-015",
                nama: "Timah Solder 60/40 Asahi (Roll)",
                icon: "⚪",
                tipe: "bahan",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 1,
                satuan: "Roll",
                lokasi: "Laci Bahan B-03",
                deskripsi: "Kawat timah solder fluks inti resin diameter 0.8mm murni.",
                deskripsiLengkap: "Timah solder kualitas tinggi dengan titik leleh ideal, menghasilkan sambungan solder mengkilap dan kuat."
            },
            {
                id: 9,
                kode: "INV-TKJ-031",
                nama: "Switch TP-Link Gigabit 16-Port",
                icon: "🖧",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 2,
                satuan: "Unit",
                lokasi: "Rak Server Praktik",
                deskripsi: "Switch unmanaged 16 port gigabit untuk topologi LAN laboratorium.",
                deskripsiLengkap: "Switch rackmount 16 port RJ45 10/100/1000 Mbps untuk simulasi interkoneksi LAN siswa."
            },
            {
                id: 10,
                kode: "INV-TKJ-009",
                nama: "Tang Kupas & Potong Kabel Tekiro",
                icon: "✂️",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 8,
                satuan: "Unit",
                lokasi: "Toolboard Gantung",
                deskripsi: "Wire stripper presisi pengupas jaket kabel UTP tanpa memutus kawat tembaga.",
                deskripsiLengkap: "Tang potong dan kupas ergonomis dengan pegangan anti slip dan mata pisau tajam."
            },
            {
                id: 11,
                kode: "INV-TKJ-014",
                nama: "Obeng Set Presisi 32-in-1 Jakemy",
                icon: "🪛",
                tipe: "inventaris",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 3,
                satuan: "Set",
                lokasi: "Lemari Toolkit A-03",
                deskripsi: "Set obeng magnetik lengkap untuk bongkar pasang casing CPU, laptop & monitor.",
                deskripsiLengkap: "Set mata obeng minus, plus, torx bintang, pentalobe, lengkap dengan pinset anti statis untuk perakitan PC."
            },
            {
                id: 12,
                kode: "BHP-TKJ-009",
                nama: "Barrel Sambungan RJ45 Female-to-Female",
                icon: "🔗",
                tipe: "bahan",
                bengkel: "Bengkel TKJ",
                bengkelCode: "TKJ",
                stok: 25,
                satuan: "Pcs",
                lokasi: "Box Aksesoris B-04",
                deskripsi: "Konektor coupler penyambung antar 2 ujung kabel LAN RJ45.",
                deskripsiLengkap: "Modular coupler RJ45 untuk pengujian panjang kabel atau perpanjangan darurat saat praktikum."
            }
        ];

        return {
            items: [],
            cart: [],
            search: '',
            filterTipe: 'all',
            filterStatus: 'all',
            filterBengkel: 'TKJ',

            // Modal States
            detailModalOpen: false,
            activeItem: null,
            detailQty: 1,

            cartDrawerOpen: false,

            checkoutModalOpen: false,
            checkoutForm: {
                tujuan: 'Praktik Jaringan Dasar (Pak Yono)',
                catatan: ''
            },

            successModalOpen: false,
            lastTicketId: '',

            // Toast Alert
            toast: {
                show: false,
                title: '',
                message: '',
                type: 'success',
                timer: null
            },

            init() {
                // Load items from localStorage if available, or fallback
                const storedItems = localStorage.getItem('sibenka_katalog_items_v1');
                if (storedItems) {
                    try {
                        this.items = JSON.parse(storedItems);
                    } catch (e) {
                        this.items = JSON.parse(JSON.stringify(DEFAULT_ITEMS));
                    }
                } else {
                    this.items = JSON.parse(JSON.stringify(DEFAULT_ITEMS));
                }

                // Load cart
                this.loadCart();

                // Listen for toggle cart event from layout
                window.addEventListener('toggle-cart', () => {
                    this.cartDrawerOpen = !this.cartDrawerOpen;
                });
            },

            saveItems() {
                localStorage.setItem('sibenka_katalog_items_v1', JSON.stringify(this.items));
            },

            loadCart() {
                try {
                    this.cart = JSON.parse(localStorage.getItem('sibenka_cart') || '[]');
                } catch (e) {
                    this.cart = [];
                }
                window.dispatchEvent(new CustomEvent('cart-updated'));
            },

            saveCart() {
                localStorage.setItem('sibenka_cart', JSON.stringify(this.cart));
                window.dispatchEvent(new CustomEvent('cart-updated'));
            },

            resetDummyData() {
                this.items = JSON.parse(JSON.stringify(DEFAULT_ITEMS));
                this.saveItems();
                this.cart = [];
                this.saveCart();
                this.showToast('Data Direset', 'Katalog dan keranjang kembali ke status awal.', 'info');
            },

            resetFilters() {
                this.search = '';
                this.filterTipe = 'all';
                this.filterStatus = 'all';
                this.filterBengkel = 'TKJ';
            },

            get filteredItems() {
                let list = [...this.items];

                // Search query
                if (this.search.trim() !== '') {
                    const q = this.search.toLowerCase().trim();
                    list = list.filter(i => 
                        i.nama.toLowerCase().includes(q) ||
                        i.kode.toLowerCase().includes(q) ||
                        i.deskripsi.toLowerCase().includes(q) ||
                        i.lokasi.toLowerCase().includes(q)
                    );
                }

                // Filter Tipe (Inventaris vs Bahan)
                if (this.filterTipe !== 'all') {
                    list = list.filter(i => i.tipe === this.filterTipe);
                }

                // Filter Status
                if (this.filterStatus === 'tersedia') {
                    list = list.filter(i => i.stok > 0);
                } else if (this.filterStatus === 'mepet') {
                    list = list.filter(i => i.stok > 0 && i.stok <= 2);
                } else if (this.filterStatus === 'dipinjam') {
                    list = list.filter(i => i.stok === 0);
                }

                // Filter Bengkel
                if (this.filterBengkel !== 'all') {
                    list = list.filter(i => i.bengkelCode === this.filterBengkel);
                }

                return list;
            },

            get totalCartCount() {
                return this.cart.reduce((sum, item) => sum + item.qty, 0);
            },

            get cartInventarisCount() {
                return this.cart.filter(i => i.tipe === 'inventaris').reduce((sum, item) => sum + item.qty, 0);
            },

            get cartBahanCount() {
                return this.cart.filter(i => i.tipe === 'bahan').reduce((sum, item) => sum + item.qty, 0);
            },

            isInCart(itemId) {
                return this.cart.some(c => c.id === itemId);
            },

            getCartQty(itemId) {
                const found = this.cart.find(c => c.id === itemId);
                return found ? found.qty : 0;
            },

            openItemDetail(item) {
                this.activeItem = item;
                const inCart = this.cart.find(c => c.id === item.id);
                this.detailQty = inCart ? inCart.qty : 1;
                this.detailModalOpen = true;
            },

            addToCart(item, qty = 1) {
                if (item.stok <= 0) return;
                
                const existing = this.cart.find(c => c.id === item.id);
                if (existing) {
                    existing.qty = Math.min(item.stok, existing.qty + qty);
                } else {
                    this.cart.push({
                        id: item.id,
                        kode: item.kode,
                        nama: item.nama,
                        tipe: item.tipe,
                        icon: item.icon,
                        satuan: item.satuan,
                        stok: item.stok,
                        qty: Math.min(item.stok, qty)
                    });
                }
                this.saveCart();
                this.showToast('Ditambahkan ke Keranjang', `${qty} ${item.satuan} ${item.nama}`, 'success');
            },

            increaseQty(itemId) {
                const item = this.cart.find(c => c.id === itemId);
                if (item && item.qty < item.stok) {
                    item.qty++;
                    this.saveCart();
                }
            },

            decreaseQty(itemId) {
                const itemIdx = this.cart.findIndex(c => c.id === itemId);
                if (itemIdx !== -1) {
                    if (this.cart[itemIdx].qty > 1) {
                        this.cart[itemIdx].qty--;
                    } else {
                        this.cart.splice(itemIdx, 1);
                    }
                    this.saveCart();
                }
            },

            removeFromCart(itemId) {
                this.cart = this.cart.filter(c => c.id !== itemId);
                this.saveCart();
                this.showToast('Item Dihapus', 'Barang dikeluarkan dari keranjang.', 'info');
            },

            openCheckoutModal() {
                if (this.cart.length === 0) {
                    this.showToast('Keranjang Kosong', 'Pilih barang terlebih dahulu.', 'warning');
                    return;
                }
                this.checkoutModalOpen = true;
            },

            submitBorrowRequest() {
                if (this.cart.length === 0) return;

                // Generate ID Tiket Peminjaman (e.g. #TRX-2026-095)
                const randomNum = Math.floor(100 + Math.random() * 900);
                const ticketId = `TRX-2026-${randomNum}`;
                this.lastTicketId = `#${ticketId}`;

                // Deduct temporary stock from catalog items
                this.cart.forEach(c => {
                    const catalogItem = this.items.find(i => i.id === c.id);
                    if (catalogItem) {
                        catalogItem.stok = Math.max(0, catalogItem.stok - c.qty);
                    }
                });
                this.saveItems();

                // Save to localStorage tickets so it appears in "Tiket Saya"
                const newTicket = {
                    id: ticketId,
                    tanggal: "Hari ini, " + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + " WIB",
                    status: "pending",
                    statusLabel: "Menunggu Persetujuan Toolman",
                    tujuan: this.checkoutForm.tujuan,
                    catatan: this.checkoutForm.catatan,
                    batasKembali: this.cartInventarisCount > 0 ? "Hari Ini, 15:30 WIB" : "Tidak Perlu Pengembalian (BHP)",
                    items: this.cart.map(i => ({
                        nama: i.nama,
                        kode: i.kode,
                        qty: i.qty,
                        satuan: i.satuan,
                        tipe: i.tipe
                    }))
                };

                try {
                    const existingTickets = JSON.parse(localStorage.getItem('sibenka_tickets') || '[]');
                    existingTickets.unshift(newTicket);
                    localStorage.setItem('sibenka_tickets', JSON.stringify(existingTickets));
                } catch (e) {
                    console.error("Failed saving ticket", e);
                }

                // Clear cart
                this.cart = [];
                this.saveCart();

                // Close checkout modal & open success modal
                this.checkoutModalOpen = false;
                this.successModalOpen = true;
            },

            showToast(title, message, type = 'success') {
                if (this.toast.timer) clearTimeout(this.toast.timer);
                this.toast.title = title;
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;

                this.toast.timer = setTimeout(() => {
                    this.toast.show = false;
                }, 3500);
            }
        };
    }

    window.katalogPeminjamApp = katalogPeminjamApp;
    document.addEventListener('alpine:init', () => {
        if (window.Alpine) {
            window.Alpine.data('katalogPeminjamApp', katalogPeminjamApp);
        }
    });
</script>
@endsection
