@extends('layouts.admin')

@section('title', 'Persetujuan Pengadaan')
@section('header_title', 'Persetujuan Pengadaan (RAB)')

@section('content')
    <!-- Alert Success -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show"
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <h4 class="font-bold text-sm">Berhasil!</h4>
                <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Alert Error -->
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show"
            class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <h4 class="font-bold text-sm">Perhatian!</h4>
                <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-red-500 hover:text-red-700 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div x-data="{
        // Modal States (Approve, Revisi, Reject)
        showApproveModal: false,
        showRevisiModal: false,
        showRejectModal: false,

        // Active Selected Item Data
        activeItem: {
            id: '',
            kode: '',
            bengkel: '',
            judul: '',
            barang: '',
            totalBiaya: '',
            reviewUrl: ''
        },

        // Open Modal Handlers
        openApprove(data) {
            this.activeItem = Object.assign({}, data);
            this.showApproveModal = true;
        },
        openRevisi(data) {
            this.activeItem = Object.assign({}, data);
            this.showRevisiModal = true;
        },
        openReject(data) {
            this.activeItem = Object.assign({}, data);
            this.showRejectModal = true;
        }
    }" class="space-y-6">

        <!-- Breadcrumb & Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-emerald-600 transition-colors">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-800 font-semibold">Persetujuan Pengadaan (RAB)</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-xl font-bold text-gray-800">Daftar Pengajuan RAB Masuk</h2>
                <p class="text-sm text-gray-500 mt-0.5">Tinjau usulan Rencana Anggaran Biaya (RAB) dari seluruh bengkel dan berikan keputusan disposisi.</p>
            </div>
        </div>

        <!-- 5 KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total Pengajuan Masuk -->
            <div class="p-4 bg-white border border-gray-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total RAB</div>
                        <div class="text-xl font-bold text-gray-900">{{ number_format($totalRAB, 0, ',', '.') }} <span class="text-xs font-normal text-gray-500">Usulan</span></div>
                    </div>
                </div>
            </div>

            <!-- Menunggu Review (Pending) -->
            <div class="p-4 bg-white border border-amber-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center font-bold shrink-0 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        @if ($totalPending > 0)
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-500 rounded-full animate-ping"></span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Menunggu Waka</div>
                        <div class="text-xl font-bold text-amber-700">{{ number_format($totalPending, 0, ',', '.') }} <span class="text-xs font-normal text-amber-600">Pending</span></div>
                    </div>
                </div>
            </div>

            <!-- Perlu Revisi -->
            <div class="p-4 bg-white border border-orange-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-orange-600 uppercase tracking-wider">Perlu Revisi</div>
                        <div class="text-xl font-bold text-orange-700">{{ number_format($totalRevisi, 0, ',', '.') }} <span class="text-xs font-normal text-orange-600">Berkas</span></div>
                    </div>
                </div>
            </div>

            <!-- Disetujui (Approved) -->
            <div class="p-4 bg-white border border-emerald-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Disetujui</div>
                        <div class="text-xl font-bold text-emerald-700">{{ number_format($totalApproved, 0, ',', '.') }} <span class="text-xs font-normal text-emerald-600">Acc</span></div>
                    </div>
                </div>
            </div>

            <!-- Total Anggaran Disetujui -->
            <div class="p-4 bg-white border border-gray-200 rounded-2xl flex items-center shadow-xs min-w-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Pagu Disetujui</div>
                        <div class="text-sm font-bold text-blue-800 truncate" title="Rp {{ number_format($totalAnggaranDisetujui, 0, ',', '.') }}">
                            Rp {{ number_format($totalAnggaranDisetujui, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <form action="{{ route('superadmin.pengadaan.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Pencarian Kata Kunci
                        </label>
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ $search }}"
                                placeholder="Cari judul RAB, alat, toolman..."
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm py-2 pl-9 pr-3 border shadow-xs outline-none transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Bengkel -->
                    <div>
                        <label for="bengkel_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Bengkel / Unit Praktik
                        </label>
                        <select id="bengkel_id" name="bengkel_id"
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm py-2 px-3 border shadow-xs outline-none transition-all bg-white">
                            <option value="">Semua Bengkel</option>
                            @foreach ($bengkels as $b)
                                <option value="{{ $b->id }}" {{ (string) $filterBengkel === (string) $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }} ({{ $b->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Status Pengajuan
                        </label>
                        <select id="status" name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm py-2 px-3 border shadow-xs outline-none transition-all bg-white">
                            <option value="" {{ $filterStatus === '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Menunggu Disposisi (Pending)</option>
                            <option value="revisi" {{ $filterStatus === 'revisi' ? 'selected' : '' }}>Perlu Revisi (Revisi)</option>
                            <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                            <option value="rejected" {{ $filterStatus === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        </select>
                    </div>

                    <!-- Tombol Aksi Filter -->
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Terapkan Filter
                        </button>
                        @if ($filterStatus || $filterBengkel || $search)
                            <a href="{{ route('superadmin.pengadaan.index') }}"
                                class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors border border-gray-200"
                                title="Reset Filter">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-semibold">Tanggal / ID</th>
                            <th class="px-6 py-4 font-semibold">Bengkel / Pemohon</th>
                            <th class="px-6 py-4 font-semibold">Judul RAB &amp; Spesifikasi Barang</th>
                            <th class="px-6 py-4 font-semibold">Item &amp; Est. Biaya</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">

                        @forelse ($pengadaans as $rab)
                            @php
                                $totalItemsCount = $rab->detailPengadaans->count();
                                $totalQty = $rab->detailPengadaans->sum('jumlah');
                                $totalBiaya = $rab->detailPengadaans->sum(fn($d) => $d->jumlah * $d->harga_satuan);

                                $namaBarangList = $rab->detailPengadaans->pluck('nama_barang')->join(', ');

                                $statusConfig = match ($rab->status) {
                                    'pending' => [
                                        'label' => 'Pending',
                                        'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'dot'   => 'bg-amber-500 animate-pulse',
                                    ],
                                    'revisi' => [
                                        'label' => 'Perlu Revisi',
                                        'class' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'dot'   => 'bg-orange-500',
                                    ],
                                    'approved' => [
                                        'label' => 'Disetujui',
                                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'dot'   => 'bg-emerald-500',
                                    ],
                                    'rejected' => [
                                        'label' => 'Ditolak',
                                        'class' => 'bg-red-50 text-red-700 border-red-200',
                                        'dot'   => 'bg-red-500',
                                    ],
                                    default => [
                                        'label' => $rab->status,
                                        'class' => 'bg-gray-50 text-gray-600 border-gray-200',
                                        'dot'   => 'bg-gray-400',
                                    ],
                                };

                                $alpineData = json_encode([
                                    'id'         => $rab->id,
                                    'kode'       => '#RAB-' . str_pad($rab->id, 4, '0', STR_PAD_LEFT),
                                    'bengkel'    => $rab->bengkel->nama ?? '-',
                                    'judul'      => $rab->judul,
                                    'barang'     => $namaBarangList,
                                    'totalBiaya' => 'Rp ' . number_format($totalBiaya, 0, ',', '.'),
                                    'reviewUrl'  => route('superadmin.pengadaan.review', $rab->id),
                                ]);
                            @endphp

                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                {{-- Tanggal / ID --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-gray-900">
                                        {{ $rab->diajukan_pada ? $rab->diajukan_pada->translatedFormat('d M Y') : $rab->created_at->translatedFormat('d M Y') }}
                                    </span>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">
                                        #RAB-{{ str_pad($rab->id, 4, '0', STR_PAD_LEFT) }}
                                    </p>
                                </td>

                                {{-- Bengkel / Pemohon --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-gray-800">{{ $rab->bengkel->nama ?? '-' }}</span>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        {{ $rab->dibuatOleh->name ?? '-' }}
                                    </p>
                                </td>

                                {{-- Judul & Rincian Barang --}}
                                <td class="px-6 py-4">
                                    <a href="{{ route('superadmin.pengadaan.show', $rab->id) }}"
                                        class="font-bold text-gray-900 hover:text-emerald-600 transition-colors line-clamp-1">
                                        {{ $rab->judul }}
                                    </a>
                                    <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $namaBarangList ?: 'Belum ada rincian item' }}</p>
                                </td>

                                {{-- Kuantitas & Estimasi Biaya --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-bold text-gray-900">{{ $totalItemsCount }} Item ({{ $totalQty }} unit)</p>
                                    <p class="text-xs text-emerald-700 font-semibold mt-0.5">
                                        Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusConfig['dot'] }}"></span>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Tombol Detail / Review RAB (Langsung Buka Halaman Show) --}}
                                        <a href="{{ route('superadmin.pengadaan.show', $rab->id) }}"
                                            class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all shadow-2xs"
                                            title="Lihat Rincian Lengkap & Disposisi RAB">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>

                                        @if (in_array($rab->status, ['pending', 'revisi']))
                                            {{-- Tombol Quick Approve --}}
                                            <button type="button" @click="openApprove({{ $alpineData }})"
                                                class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 border border-emerald-200/60 rounded-lg transition-all shadow-2xs"
                                                title="Setujui RAB Langsung">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>

                                            {{-- Tombol Quick Revisi --}}
                                            <button type="button" @click="openRevisi({{ $alpineData }})"
                                                class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 border border-amber-200/60 rounded-lg transition-all shadow-2xs"
                                                title="Minta Revisi ke Toolman">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>

                                            {{-- Tombol Quick Reject --}}
                                            <button type="button" @click="openReject({{ $alpineData }})"
                                                class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all shadow-2xs"
                                                title="Tolak Pengajuan RAB">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="text-sm font-semibold text-gray-600">Tidak ada pengajuan RAB yang cocok.</p>
                                        <p class="text-xs text-gray-400 max-w-sm">
                                            Tidak ditemukan berkas RAB dengan filter yang dipilih. Coba atur ulang status atau kata kunci pencarian.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Menampilkan {{ $pengadaans->firstItem() ?? 0 }}–{{ $pengadaans->lastItem() ?? 0 }}
                    dari {{ $pengadaans->total() }} total pengajuan RAB
                </p>
                <div class="flex items-center gap-1">
                    {{ $pengadaans->links('pagination::simple-tailwind') }}
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 1. MODAL SETUJUI PENGADAAN (APPROVE MODAL) -->
        <!-- ==================================================== -->
        <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showApproveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
                @click="showApproveModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showApproveModal"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 pr-12">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200/80 ring-4 ring-emerald-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">Setujui Pengajuan RAB?</h3>
                                <p class="text-xs text-gray-500 mt-1">Pengadaan ini akan disetujui untuk realisasi kebutuhan bengkel.</p>
                            </div>
                        </div>

                        <!-- Card Preview -->
                        <div class="mt-4 p-3 bg-emerald-50/60 border border-emerald-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold text-emerald-900">
                                <span x-text="activeItem.kode"></span>
                                <span class="font-bold text-sm text-emerald-800" x-text="activeItem.totalBiaya"></span>
                            </div>
                            <div class="text-gray-700 font-semibold" x-text="activeItem.judul"></div>
                            <div class="text-gray-500" x-text="'Bengkel: ' + activeItem.bengkel"></div>
                        </div>
                    </div>

                    <!-- Form Persetujuan -->
                    <form :action="activeItem.reviewUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="approved">

                        <!-- Catatan Disetujui -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Catatan Persetujuan Disposisi <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea name="catatan_review" rows="3"
                                placeholder="Contoh: Disetujui untuk pengadaan termin 1 tahun ajaran berjalan. Koordinasikan dengan bendahara sarpras."
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showApproveModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Ya, Setujui Pengadaan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 2. MODAL MINTA REVISI PENGADAAN -->
        <!-- ==================================================== -->
        <div x-show="showRevisiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showRevisiModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
                @click="showRevisiModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showRevisiModal"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 pr-12">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200/80 ring-4 ring-amber-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">Kirim Catatan Revisi RAB</h3>
                                <p class="text-xs text-gray-500 mt-1">Minta toolman bengkel untuk memperbaiki jumlah kuantitas, spesifikasi, atau estimasi harga.</p>
                            </div>
                        </div>

                        <!-- Card Preview -->
                        <div class="mt-4 p-3 bg-amber-50/60 border border-amber-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold text-amber-900">
                                <span x-text="activeItem.kode"></span>
                                <span class="font-bold text-sm text-amber-800" x-text="activeItem.totalBiaya"></span>
                            </div>
                            <div class="text-gray-700 font-semibold" x-text="activeItem.judul"></div>
                            <div class="text-gray-500" x-text="'Bengkel: ' + activeItem.bengkel"></div>
                        </div>
                    </div>

                    <!-- Form Revisi -->
                    <form :action="activeItem.reviewUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="revisi">

                        <!-- Poin Catatan Revisi -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Poin / Catatan yang Perlu Diperbaiki <span class="text-red-500">*</span>
                            </label>
                            <textarea name="catatan_review" rows="3" required
                                placeholder="Contoh: Mohon kurangi jumlah kuantitas bor tangan dari 5 unit menjadi 3 unit terlebih dahulu karena keterbatasan pagu anggaran, atau ganti merk ke alternatif yang lebih hemat..."
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-amber-600 focus:ring-amber-600 shadow-sm"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showRevisiModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kirim Catatan Revisi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 3. MODAL TOLAK PENGADAAN (REJECT MODAL) -->
        <!-- ==================================================== -->
        <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showRejectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
                @click="showRejectModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showRejectModal"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 pr-12">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 border border-red-200/80 ring-4 ring-red-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">Tolak Pengajuan RAB?</h3>
                                <p class="text-xs text-gray-500 mt-1">Pengadaan barang ini tidak akan disetujui untuk direalisasikan.</p>
                            </div>
                        </div>

                        <!-- Card Preview -->
                        <div class="mt-4 p-3 bg-red-50/60 border border-red-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold text-red-900">
                                <span x-text="activeItem.kode"></span>
                                <span class="font-bold text-sm text-red-800" x-text="activeItem.totalBiaya"></span>
                            </div>
                            <div class="text-gray-700 font-semibold" x-text="activeItem.judul"></div>
                            <div class="text-gray-500" x-text="'Bengkel: ' + activeItem.bengkel"></div>
                        </div>
                    </div>

                    <!-- Form Penolakan -->
                    <form :action="activeItem.reviewUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="rejected">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Alasan Penolakan Pengadaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="catatan_review" rows="3" required
                                placeholder="Contoh: Alokasi pagu belanja modal untuk jurusan ini telah habis terpakai, atau stok alat di gudang pusat masih mencukupi..."
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-red-600 focus:ring-red-600 shadow-sm"></textarea>
                        </div>

                        <div class="p-3 bg-red-50/80 border border-red-200/80 rounded-xl text-xs text-red-800 flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <span>Status usulan akan berubah menjadi ditolak dan catatan ini akan terbaca oleh Toolman pengusul.</span>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showRejectModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Ya, Tolak Pengadaan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection
