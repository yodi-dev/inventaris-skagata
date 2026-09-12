@extends('layouts.admin')

@section('title', 'Detail RAB #' . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT))
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

    @php
        $statusConfig = match ($pengadaan->status) {
            'pending' => [
                'label' => 'Menunggu Review (Pending)',
                'badge' => 'bg-amber-50 text-amber-800 border-amber-200',
                'dot'   => 'bg-amber-500 animate-pulse',
            ],
            'revisi' => [
                'label' => 'Perlu Revisi',
                'badge' => 'bg-orange-50 text-orange-800 border-orange-200',
                'dot'   => 'bg-orange-500',
            ],
            'approved' => [
                'label' => 'Disetujui (Approved)',
                'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'dot'   => 'bg-emerald-500',
            ],
            'rejected' => [
                'label' => 'Ditolak (Rejected)',
                'badge' => 'bg-red-50 text-red-800 border-red-200',
                'dot'   => 'bg-red-500',
            ],
            default => [
                'label' => $pengadaan->status,
                'badge' => 'bg-gray-50 text-gray-700 border-gray-200',
                'dot'   => 'bg-gray-400',
            ],
        };
    @endphp

    <div x-data="{ activeTab: 'approve' }" class="space-y-6 max-w-7xl mx-auto">
        <!-- Top Navigation & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.pengadaan.index') }}"
                    class="p-2 bg-white hover:bg-gray-100 text-gray-700 rounded-xl border border-gray-200 shadow-2xs transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <nav class="flex text-xs text-gray-500 mb-0.5" aria-label="Breadcrumb">
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
                                    <a href="{{ route('superadmin.pengadaan.index') }}" class="hover:text-emerald-600 transition-colors">
                                        Pengadaan (RAB)
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-800 font-semibold">#RAB-{{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Detail Usulan RAB Pengadaan</h2>
                </div>
            </div>

            <!-- Tombol Aksi: Cetak Lembar RAB (Buka Pratinjau Cetak Resmi) -->
            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.pengadaan.print', $pengadaan->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-sm transition-all group"
                    title="Buka Pratinjau Dokumen Cetak & Cetak Lembar RAB">
                    <svg class="w-4 h-4 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    <span>Cetak Lembar RAB</span>
                </a>
            </div>
        </div>

        <!-- Header Card: Info Pengajuan & Status -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-gray-100">
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                            #RAB-{{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusConfig['dot'] }}"></span>
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $pengadaan->judul }}</h1>
                    <p class="text-xs text-gray-500 flex items-center gap-2">
                        <span>Waktu Pengajuan:</span>
                        <span class="font-semibold text-gray-700">
                            {{ $pengadaan->diajukan_pada ? $pengadaan->diajukan_pada->translatedFormat('d F Y, H:i') : $pengadaan->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                    </p>
                </div>

                <!-- Total Anggaran Callout -->
                <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-4 lg:w-72 shrink-0">
                    <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Total Estimasi Anggaran</span>
                    <div class="text-2xl font-bold text-emerald-900 mt-1">
                        Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-emerald-700 mt-1 flex items-center justify-between">
                        <span>{{ $pengadaan->detailPengadaans->count() }} Jenis Item</span>
                        <span>{{ $totalItems }} Unit Total</span>
                    </div>
                </div>
            </div>

            <!-- Metadata Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 text-sm">
                <!-- Bengkel Info -->
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Bengkel Pemohon</span>
                    <p class="font-bold text-gray-900">{{ $pengadaan->bengkel->nama ?? '-' }}</p>
                    <p class="text-xs text-gray-500 font-mono">Kode: {{ $pengadaan->bengkel->kode ?? '-' }}</p>
                    @if ($pengadaan->bengkel->deskripsi)
                        <p class="text-xs text-gray-500 line-clamp-2 mt-1">{{ $pengadaan->bengkel->deskripsi }}</p>
                    @endif
                </div>

                <!-- Toolman Info -->
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Toolman Pengusul</span>
                    <p class="font-bold text-gray-900">{{ $pengadaan->dibuatOleh->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $pengadaan->dibuatOleh->email ?? '-' }}</p>
                    @if ($pengadaan->dibuatOleh->nomor_wa)
                        <p class="text-xs text-emerald-600 font-medium">WA: {{ $pengadaan->dibuatOleh->nomor_wa }}</p>
                    @endif
                </div>

                <!-- Reviewer Info -->
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Status Peninjauan Waka</span>
                    @if ($pengadaan->direviewOleh)
                        <p class="font-bold text-gray-900">{{ $pengadaan->direviewOleh->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $pengadaan->direview_pada ? $pengadaan->direview_pada->translatedFormat('d F Y, H:i') : '-' }} WIB
                        </p>
                    @else
                        <p class="text-amber-600 font-medium text-xs flex items-center gap-1.5 mt-1">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Belum ditinjau Waka Sarpras
                        </p>
                    @endif
                </div>
            </div>

            <!-- Catatan Pengusul / Justifikasi Kebutuhan -->
            <div class="mt-6 pt-5 border-t border-gray-100">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">
                    Justifikasi / Catatan Pengusul:
                </span>
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 text-xs text-gray-700 leading-relaxed">
                    {{ $pengadaan->catatan ?: 'Tidak ada justifikasi khusus yang dicantumkan oleh pengusul.' }}
                </div>
            </div>

            <!-- Catatan Review Waka Sarpras (Jika ada) -->
            @if ($pengadaan->catatan_review)
                <div class="mt-4">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">
                        Catatan Keputusan / Arahan Waka Sarpras:
                    </span>
                    <div class="p-3.5 rounded-xl text-xs leading-relaxed {{ $pengadaan->status === 'revisi' ? 'bg-amber-50 border border-amber-200 text-amber-900' : ($pengadaan->status === 'rejected' ? 'bg-red-50 border border-red-200 text-red-900' : 'bg-emerald-50 border border-emerald-200 text-emerald-900') }}">
                        {{ $pengadaan->catatan_review }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Table Card: Rincian Barang (Detail Items) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/70 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Rincian Barang Pengadaan (RAB)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar item alat inventaris dan bahan yang diajukan beserta estimasi harga satuannya.</p>
                </div>
                <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 shadow-2xs">
                    {{ $pengadaan->detailPengadaans->count() }} Jenis Item
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-3.5 font-semibold text-center w-12">No</th>
                            <th class="px-6 py-3.5 font-semibold">Nama Barang &amp; Referensi</th>
                            <th class="px-6 py-3.5 font-semibold">Spesifikasi Teknis</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Kuantitas</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Harga Satuan (Est.)</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Subtotal (Est.)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse ($pengadaan->detailPengadaans as $index => $item)
                            @php
                                $subtotal = $item->jumlah * $item->harga_satuan;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 text-center font-bold text-gray-400 text-xs">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $item->nama_barang }}</p>
                                    @if ($item->barang)
                                        <p class="text-xs text-emerald-600 font-mono mt-0.5 flex items-center gap-1">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Katalog Master: {{ $item->barang->kode_barang }} ({{ $item->barang->nama }})
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-0.5 italic">
                                            (Barang usulan baru / belum di master)
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600 max-w-xs">
                                    {{ $item->spesifikasi ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-gray-900">{{ $item->jumlah }}</span>
                                    <span class="text-xs text-gray-500 font-medium ml-0.5">{{ $item->satuan }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap text-xs font-mono font-medium text-gray-700">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap text-xs font-mono font-bold text-emerald-700">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-xs">
                                    Tidak ada item barang dalam pengajuan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-emerald-50/90 border-t-2 border-emerald-200 font-bold text-emerald-950">
                            <td colspan="3" class="px-6 py-4 text-xs uppercase tracking-wider text-right">
                                Total Estimasi Anggaran Pengadaan:
                            </td>
                            <td class="px-6 py-4 text-center text-xs font-bold text-emerald-900">
                                {{ $totalItems }} Unit
                            </td>
                            <td class="px-6 py-4 text-right text-xs">
                                Total:
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-base font-extrabold text-emerald-900">
                                Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Panel Form Keputusan Review Waka Sarpras -->
        @if (in_array($pengadaan->status, ['pending', 'revisi']))
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 bg-slate-50 border-b border-gray-200">
                    <h3 class="text-base font-bold text-gray-900">Form Disposisi &amp; Keputusan Waka Sarpras</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pilih keputusan untuk menyetujui, meminta revisi spesifikasi/kuantitas, atau menolak pengajuan RAB ini.
                    </p>

                    <!-- Tabs Switcher -->
                    <div class="flex items-center gap-2 mt-4">
                        <button type="button" @click="activeTab = 'approve'"
                            :class="activeTab === 'approve'
                                ? 'bg-emerald-600 text-white shadow-xs'
                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            1. Setujui Pengadaan
                        </button>

                        <button type="button" @click="activeTab = 'revisi'"
                            :class="activeTab === 'revisi'
                                ? 'bg-amber-600 text-white shadow-xs'
                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            2. Minta Revisi
                        </button>

                        <button type="button" @click="activeTab = 'reject'"
                            :class="activeTab === 'reject'
                                ? 'bg-red-600 text-white shadow-xs'
                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            3. Tolak Pengadaan
                        </button>
                    </div>
                </div>

                <!-- Tab 1: Form Setujui (Approve) -->
                <div x-show="activeTab === 'approve'" class="p-6 space-y-4">
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Konfirmasi Persetujuan RAB</p>
                            <p class="mt-0.5 leading-relaxed">
                                Pengajuan RAB sebesar <strong>Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</strong> untuk <strong>{{ $pengadaan->bengkel->nama }}</strong> akan disetujui. Sesuai PRD, persetujuan ini tidak langsung menambah stok fisik sampai barang selesai dibelanjakan dan diterima oleh Toolman.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.pengadaan.review', $pengadaan->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="approved">

                        <div>
                            <label for="catatan_review_approve" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Catatan Persetujuan Disposisi Waka <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea id="catatan_review_approve" name="catatan_review" rows="3"
                                placeholder="Contoh: Disetujui untuk pengadaan termin 1 tahun berjalan. Silakan koordinasikan pencairan dana dengan bendahara..."
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs"></textarea>
                        </div>

                        <div class="pt-2 flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Ya, Setujui Pengajuan RAB
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab 2: Form Minta Revisi -->
                <div x-show="activeTab === 'revisi'" x-cloak class="p-6 space-y-4">
                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div>
                            <p class="font-bold">Arahan Revisi untuk Toolman</p>
                            <p class="mt-0.5 leading-relaxed">
                                Pengajuan akan dikembalikan ke Toolman untuk diperbaiki. Berikan poin catatan revisi yang spesifik agar Toolman dapat menyesuaikan draf usulan.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.pengadaan.review', $pengadaan->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="revisi">

                        <div>
                            <label for="catatan_review_revisi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Poin Instruksi Revisi <span class="text-red-500">*</span>
                            </label>
                            <textarea id="catatan_review_revisi" name="catatan_review" rows="3" required
                                placeholder="Contoh: Mohon kurangi kuantitas bor tangan dari 5 unit menjadi 3 unit terlebih dahulu karena keterbatasan pagu anggaran, atau ganti tipe ke alternatif yang lebih efisien..."
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-amber-600 focus:ring-amber-600 shadow-xs"></textarea>
                        </div>

                        <div class="pt-2 flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kembalikan Berkas untuk Direvisi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Form Tolak (Reject) -->
                <div x-show="activeTab === 'reject'" x-cloak class="p-6 space-y-4">
                    <div class="p-4 bg-red-50/70 border border-red-200 rounded-xl text-xs text-red-800 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Penolakan Usulan RAB</p>
                            <p class="mt-0.5 leading-relaxed">
                                Pengajuan RAB ini akan ditolak secara permanen dan tidak dialokasikan ke anggaran sekolah. Berikan alasan resmi penolakan.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.pengadaan.review', $pengadaan->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="rejected">

                        <div>
                            <label for="catatan_review_reject" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Alasan Penolakan Pengadaan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="catatan_review_reject" name="catatan_review" rows="3" required
                                placeholder="Contoh: Stok alat sejenis di bengkel pusat masih sangat mencukupi, atau pagu anggaran belanja barang semester ini telah habis dialokasikan..."
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-red-600 focus:ring-red-600 shadow-xs"></textarea>
                        </div>

                        <div class="pt-2 flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Ya, Tolak Pengajuan Ini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- Banner Jika Pengajuan Sudah Selesai Diproses (Approved / Rejected) -->
            <div class="p-6 rounded-2xl border {{ $pengadaan->status === 'approved' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-red-50 border-red-200 text-red-900' }} shadow-xs">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl {{ $pengadaan->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} flex items-center justify-center shrink-0">
                        @if ($pengadaan->status === 'approved')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 text-sm">
                        <h4 class="font-bold text-base">
                            Pengajuan RAB Ini Telah Selesai Diproses ({{ ucfirst($pengadaan->status) }})
                        </h4>
                        <p class="mt-1 text-xs opacity-90 leading-relaxed">
                            Keputusan telah dicatat oleh <strong>{{ $pengadaan->direviewOleh->name ?? 'Waka Sarpras' }}</strong> pada {{ $pengadaan->direview_pada ? $pengadaan->direview_pada->translatedFormat('d F Y, H:i') : '-' }} WIB.
                        </p>
                        @if ($pengadaan->catatan_review)
                            <div class="mt-3 p-3 bg-white/70 rounded-xl border border-current/20 text-xs font-medium">
                                <strong>Catatan Disposisi:</strong> {{ $pengadaan->catatan_review }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
