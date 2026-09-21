@extends('layouts.peminjam')

@section('title', 'Dashboard Peminjam')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 sm:space-y-8">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-emerald-50 border-2 border-emerald-300 text-emerald-950 rounded-2xl p-4 flex items-start gap-3 shadow-xs">
                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-extrabold text-emerald-950">Berhasil!</h4>
                    <p class="text-emerald-900 mt-0.5 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border-2 border-rose-300 text-rose-950 rounded-2xl p-4 flex items-start gap-3 shadow-xs">
                <div class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-extrabold text-rose-950">Peringatan:</h4>
                    <p class="text-rose-900 mt-0.5 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Banner Overdue Jika Ada Keterlambatan -->
        @if ($hasOverdue)
            <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 sm:p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                style="background-color: #fff1f2; border-color: #fda4af;">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md"
                        style="background-color: #e11d48; color: #ffffff;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-950 border border-rose-300"
                                style="background-color: #fecdd3; color: #4c0519;">
                                Terlambat
                            </span>
                            <h4 class="font-black text-sm sm:text-base text-rose-950" style="color: #4c0519;">
                                Peringatan Keterlambatan Pengembalian!
                            </h4>
                        </div>
                        <p class="text-xs sm:text-sm text-rose-900 font-bold leading-relaxed" style="color: #881337;">
                            Terdapat pinjaman alat fisik Anda yang melewati batas waktu pengembalian. Harap segera kembalikan ke meja Toolman.
                        </p>
                    </div>
                </div>
                <a href="{{ route('peminjam.tiket.index', ['status' => 'terlambat']) }}"
                    class="inline-flex items-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs sm:text-sm rounded-xl shadow-sm transition-colors shrink-0"
                    style="background-color: #e11d48; color: #ffffff;">
                    Cek Pinjaman Terlambat &rarr;
                </a>
            </div>
        @endif

        <!-- ======================================================== -->
        <!-- 1. GREETING & HEADER BANNER (Muncul 10 Detik Pertama)     -->
        <!-- ======================================================== -->
        <div x-data="{
                showBanner: true,
                timeLeft: 10,
                interval: null,
                init() {
                    this.interval = setInterval(() => {
                        if (this.timeLeft > 1) {
                            this.timeLeft--;
                        } else {
                            this.showBanner = false;
                            clearInterval(this.interval);
                        }
                    }, 1000);
                },
                dismiss() {
                    this.showBanner = false;
                    if (this.interval) clearInterval(this.interval);
                }
            }"
            x-show="showBanner"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in-out duration-500 transform"
            x-transition:leave-start="opacity-100 scale-100 max-h-[600px]"
            x-transition:leave-end="opacity-0 scale-95 max-h-0 -translate-y-4"
            class="relative overflow-hidden bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-8 text-white shadow-md">
            
            <!-- Countdown Timer & Close Button (Pojok Kanan Atas) -->
            <div class="absolute top-4 right-4 z-20 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-950/60 backdrop-blur-md text-[10px] font-mono font-bold text-emerald-200 border border-emerald-400/20 shadow-2xs">
                    <svg class="w-3 h-3 text-emerald-300 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="timeLeft + 's'">10s</span>
                </span>
                <button type="button" @click="dismiss()"
                    class="p-1.5 rounded-full bg-emerald-950/60 hover:bg-emerald-950/90 text-emerald-200 hover:text-white border border-emerald-400/20 transition-colors shadow-2xs"
                    title="Tutup banner">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Background Decorative Soft Lights -->
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-56 h-56 bg-emerald-400/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 pr-12 sm:pr-0">
                <div class="space-y-2.5">
                    <!-- Status Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-950/70 backdrop-blur-md border border-emerald-400/40 text-xs font-bold text-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        @if ($user->isGuru())
                            <span>Akses Guru / Pendidik &bull; Multi-Bengkel</span>
                        @else
                            <span>Siswa &bull; Bengkel {{ $bengkel->nama ?? 'SMKN 3 Yogyakarta' }}</span>
                        @endif
                    </div>

                    <!-- Welcome Title -->
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight leading-tight text-white">
                        Selamat Datang, {{ $user->name }}! 👋
                    </h1>

                    <!-- Description Text with Crisp Contrast -->
                    <p class="text-xs sm:text-sm text-emerald-50 max-w-xl leading-relaxed font-medium">
                        Sistem Inventaris & Sirkulasi Bengkel (SIBENKA) SMKN 3 Yogyakarta. Pilih menu di bawah untuk meminjam alat praktik baru atau mengembalikan alat yang sedang Anda gunakan.
                    </p>
                </div>

                <!-- Date & Info Box -->
                <div class="hidden lg:flex flex-col items-end text-right bg-emerald-950/50 backdrop-blur-md rounded-2xl p-4 border border-emerald-400/30 shrink-0 min-w-[210px]">
                    <span class="text-[11px] text-emerald-200 font-extrabold uppercase tracking-wider">Hari & Tanggal</span>
                    <span class="text-base font-black text-white mt-1">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}
                    </span>
                    <div class="flex items-center gap-1.5 mt-1.5 text-xs font-semibold text-emerald-100">
                        <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>SIBENKA SKAGATA</span>
                    </div>
                </div>
            </div>

            <!-- Progress Bar 10 Detik di Bagian Bawah Banner -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/20 overflow-hidden">
                <div class="h-full bg-emerald-300 transition-all duration-1000 ease-linear"
                    :style="'width: ' + (timeLeft * 10) + '%'"></div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 2. DUA TOMBOL UTAMA: PINJAM & KEMBALIKAN (DENGAN GAMBAR) -->
        <!-- ======================================================== -->
        <div>
            <div class="mb-4">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">Menu Utama Peminjam</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-0.5 font-medium">Silakan pilih menu transaksi yang ingin Anda lakukan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">

                <!-- MENU 1: PINJAM (DENGAN GAMBAR ICON RELEVAN) -->
                <a href="{{ route('peminjam.katalog.index') }}"
                    class="group relative bg-white hover:bg-emerald-50/20 rounded-3xl p-6 sm:p-7 border-2 border-emerald-300 hover:border-emerald-600 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <!-- Top Accent Line -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-400"></div>

                    <div>
                        <!-- Header with 3D Illustration Icon & Badge -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-emerald-50 p-1.5 border border-emerald-200 shadow-sm shrink-0 group-hover:scale-105 transition-transform duration-300">
                                <img src="{{ asset('images/dashboard/pinjam.jpg') }}" alt="Icon Pinjam Alat & Bahan"
                                    class="w-full h-full object-cover rounded-xl">
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                Katalog Alat & Bahan
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl sm:text-3xl font-black text-gray-900 group-hover:text-emerald-700 transition-colors">
                            Pinjam
                        </h3>

                        <!-- Description with high contrast -->
                        <p class="text-xs sm:text-sm text-gray-700 font-medium mt-2 leading-relaxed">
                            Cari dan ajukan peminjaman perkakas tangan, mesin, alat ukur, instrumen, maupun bahan habis pakai (BHP) untuk kebutuhan praktik bengkel.
                        </p>
                    </div>

                    <!-- Prominent Tangible CTA Button -->
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <span class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-emerald-600 group-hover:bg-emerald-700 text-white font-extrabold text-sm shadow-sm group-hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Buka Katalog Pinjam &rarr;</span>
                        </span>
                    </div>
                </a>

                <!-- MENU 2: KEMBALIKAN (DENGAN GAMBAR ICON RELEVAN) -->
                <a href="{{ route('peminjam.tiket.index') }}"
                    class="group relative bg-white hover:bg-blue-50/20 rounded-3xl p-6 sm:p-7 border-2 border-blue-300 hover:border-blue-600 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <!-- Top Accent Line -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-500"></div>

                    <div>
                        <!-- Header with 3D Illustration Icon & Badge -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-blue-50 p-1.5 border border-blue-200 shadow-sm shrink-0 group-hover:scale-105 transition-transform duration-300">
                                <img src="{{ asset('images/dashboard/kembali.jpg') }}" alt="Icon Kembalikan Alat"
                                    class="w-full h-full object-cover rounded-xl">
                            </div>
                            @if ($countActive > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $hasOverdue ? 'bg-rose-100 text-rose-900 border border-rose-300' : 'bg-blue-100 text-blue-900 border border-blue-300' }}">
                                    <span class="w-2 h-2 rounded-full {{ $hasOverdue ? 'bg-rose-600' : 'bg-blue-600' }} animate-pulse mr-1.5"></span>
                                    {{ $countActive }} Sedang Dipinjam
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-300">
                                    Tiket Peminjaman
                                </span>
                            @endif
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl sm:text-3xl font-black text-gray-900 group-hover:text-blue-700 transition-colors">
                            Kembalikan
                        </h3>

                        <!-- Description with high contrast -->
                        <p class="text-xs sm:text-sm text-gray-700 font-medium mt-2 leading-relaxed">
                            Pantau batas tenggat waktu pinjaman, ajukan pengembalian, dan serahkan alat fisik ke meja Toolman bengkel untuk pengecekan kondisi akhir.
                        </p>
                    </div>

                    <!-- Prominent Tangible CTA Button -->
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <span class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-blue-600 group-hover:bg-blue-700 text-white font-extrabold text-sm shadow-sm group-hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Buka Halaman Tiket &rarr;</span>
                        </span>
                    </div>
                </a>

            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 4. RINGKASAN STATUS TIKET PEMINJAM                       -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <!-- Sedang Dipinjam -->
            <a href="{{ route('peminjam.tiket.index', ['status' => 'active']) }}"
                class="bg-white p-4 rounded-2xl border-2 border-gray-200 hover:border-blue-400 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600">Sedang Dipinjam</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-black text-gray-900">{{ $countActive }}</span>
                    <span class="text-xs font-semibold text-gray-500">tiket</span>
                </div>
            </a>

            <!-- Menunggu Acc -->
            <a href="{{ route('peminjam.tiket.index', ['status' => 'pending']) }}"
                class="bg-white p-4 rounded-2xl border-2 border-gray-200 hover:border-amber-400 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600">Menunggu Acc</span>
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-black text-gray-900">{{ $countPending }}</span>
                    <span class="text-xs font-semibold text-gray-500">tiket</span>
                </div>
            </a>

            <!-- Cek Fisik Toolman -->
            <a href="{{ route('peminjam.tiket.index', ['status' => 'menunggu_pengecekan']) }}"
                class="bg-white p-4 rounded-2xl border-2 border-gray-200 hover:border-purple-400 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600">Proses Kembali</span>
                    <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-black text-gray-900">{{ $countMenungguPengecekan }}</span>
                    <span class="text-xs font-semibold text-gray-500">tiket</span>
                </div>
            </a>

            <!-- Selesai -->
            <a href="{{ route('peminjam.tiket.index', ['status' => 'selesai']) }}"
                class="bg-white p-4 rounded-2xl border-2 border-gray-200 hover:border-emerald-400 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600">Telah Selesai</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-black text-gray-900">{{ $countSelesai }}</span>
                    <span class="text-xs font-semibold text-gray-500">tiket</span>
                </div>
            </a>
        </div>

        <!-- ======================================================== -->
        <!-- 5. DAFTAR PINJAMAN AKTIF YANG PERLU DIKEMBALIKAN         -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl border-2 border-gray-200 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-gray-900">Pinjaman Aktif Perlu Perhatian</h3>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium">Daftar alat yang sedang Anda pinjam dan harus dikembalikan sesuai batas waktu.</p>
                </div>
                <a href="{{ route('peminjam.tiket.index') }}"
                    class="text-xs sm:text-sm font-extrabold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center gap-1">
                    Lihat Semua Tiket &rarr;
                </a>
            </div>

            @if ($peminjamanAktif->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($peminjamanAktif as $item)
                        @php
                            $isLate = $item->status === 'terlambat' || ($item->batas_kembali && \Carbon\Carbon::parse($item->batas_kembali)->isPast());
                        @endphp
                        <div class="py-4 first:pt-1 last:pb-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-extrabold text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded border border-gray-200">
                                        #TRX-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-gray-400">&bull;</span>
                                    <span class="text-xs font-bold text-gray-800">
                                        {{ $item->bengkel->nama ?? 'Bengkel' }}
                                    </span>
                                    @if ($item->status === 'terlambat' || $isLate)
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-900 border border-rose-300">
                                            Terlambat
                                        </span>
                                    @elseif ($item->status === 'menunggu_pengecekan')
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-purple-100 text-purple-900 border border-purple-300">
                                            Cek Meja Toolman
                                        </span>
                                    @else
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 border border-blue-300">
                                            Sedang Dipinjam
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs sm:text-sm text-gray-800">
                                    <strong class="text-gray-900">Barang:</strong>
                                    {{ $item->detailPeminjamans->pluck('barang.nama')->filter()->implode(', ') ?: 'Detail barang tidak ditemukan' }}
                                </div>
                                <div class="text-xs text-gray-600 font-medium">
                                    Batas Kembali:
                                    <strong class="{{ $isLate ? 'text-rose-700 font-black' : 'text-gray-900 font-bold' }}">
                                        @if ($item->batas_kembali)
                                            {{ \Carbon\Carbon::parse($item->batas_kembali)->translatedFormat('d M Y, H:i') }} WIB
                                        @else
                                            Bahan Habis Pakai (BHP)
                                        @endif
                                    </strong>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center">
                                <a href="{{ route('peminjam.tiket.show', $item->id) }}"
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl border border-gray-300 transition-colors">
                                    Detail
                                </a>
                                @if (in_array($item->status, ['active', 'terlambat']))
                                    <form method="POST" action="{{ route('peminjam.tiket.kembalikan', $item->id) }}"
                                        onsubmit="return confirm('Ajukan pengembalian untuk tiket #TRX-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}? Siapkan barang fisik untuk diserahkan ke Toolman.')">
                                        @csrf
                                        <button type="submit"
                                            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors flex items-center gap-1.5">
                                            <!-- Standard U-Turn Return Icon -->
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                            <span>Kembalikan</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-8 px-4 bg-slate-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-extrabold text-gray-900">Tidak Ada Pinjaman Aktif</h4>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium max-w-sm mx-auto mt-1">
                        Semua peminjaman Anda sudah tuntas. Jika membutuhkan perkakas atau bahan praktik baru, silakan buka katalog.
                    </p>
                    <a href="{{ route('peminjam.katalog.index') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 mt-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Mulai Pinjam Alat</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- 6. PANDUAN ALUR SIRKULASI BENGKEL (Clean Light Theme)     -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border-2 border-gray-200 shadow-xs space-y-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-gray-900">Alur Peminjaman & Pengembalian Alat Bengkel</h3>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium">4 langkah mudah meminjam dan mengembalikan fasilitas praktik di SKAGATA.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs pt-1">
                <!-- Step 1 -->
                <div class="bg-slate-50 hover:bg-emerald-50/50 p-4 rounded-2xl border border-slate-200 transition-colors">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs mb-2.5 shadow-2xs">
                        1
                    </div>
                    <strong class="block text-sm font-black text-gray-900 mb-1">Pilih Alat & Bahan</strong>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Buka katalog dan pilih perkakas atau bahan habis pakai yang Anda perlukan untuk praktik.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-50 hover:bg-emerald-50/50 p-4 rounded-2xl border border-slate-200 transition-colors">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs mb-2.5 shadow-2xs">
                        2
                    </div>
                    <strong class="block text-sm font-black text-gray-900 mb-1">Ajukan Permohonan</strong>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Tentukan batas waktu pengembalian serta keperluan praktik, lalu kirim tiket ke Toolman bengkel.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-50 hover:bg-emerald-50/50 p-4 rounded-2xl border border-slate-200 transition-colors">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs mb-2.5 shadow-2xs">
                        3
                    </div>
                    <strong class="block text-sm font-black text-gray-900 mb-1">Ambil di Meja Toolman</strong>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Setelah disetujui, ambil alat fisik di meja Toolman dan gunakan secara bertanggung jawab.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-50 hover:bg-emerald-50/50 p-4 rounded-2xl border border-slate-200 transition-colors">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs mb-2.5 shadow-2xs">
                        4
                    </div>
                    <strong class="block text-sm font-black text-gray-900 mb-1">Kembalikan & Verifikasi</strong>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Klik tombol "Kembalikan", lalu serahkan alat fisik ke Toolman untuk diverifikasi kelengkapannya.
                    </p>
                </div>
            </div>
        </div>

    </div>
@endsection
