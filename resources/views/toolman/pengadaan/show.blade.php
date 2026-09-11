@extends('layouts.admin')

@section('title', 'Detail Usulan RAB - ' . $pengadaan->judul)
@section('header_title', 'Detail Usulan Pengadaan RAB')

@section('content')
    @php
        $totalBiaya = $pengadaan->detailPengadaans->sum(fn($d) => $d->jumlah * $d->harga_satuan);
        $totalItem = $pengadaan->detailPengadaans->count();
        $totalQty = $pengadaan->detailPengadaans->sum('jumlah');

        $statusConfig = match ($pengadaan->status) {
            'draft' => [
                'label' => 'Draf Disimpan',
                'class' => 'bg-gray-100 text-gray-800 border-gray-300',
                'dot' => 'bg-gray-500',
                'desc' => 'Draf usulan pengadaan ini tersimpan dan belum diajukan ke Waka Sarpras.',
            ],
            'pending' => [
                'label' => 'Menunggu Approval Waka',
                'class' => 'bg-amber-100 text-amber-900 border-amber-300',
                'dot' => 'bg-amber-600 animate-pulse',
                'desc' => 'Usulan telah diajukan dan sedang menunggu proses verifikasi/persetujuan oleh Waka Sarpras.',
            ],
            'revisi' => [
                'label' => 'Perlu Revisi',
                'class' => 'bg-orange-100 text-orange-900 border-orange-300',
                'dot' => 'bg-orange-600',
                'desc' => 'Waka Sarpras meminta penyesuaian/revisi rincian anggaran barang.',
            ],
            'approved' => [
                'label' => 'Disetujui',
                'class' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                'dot' => 'bg-emerald-600',
                'desc' => 'Usulan RAB telah disetujui Waka Sarpras untuk realisasi belanja pengadaan.',
            ],
            'selesai' => [
                'label' => 'Barang Fisik Diterima (Selesai)',
                'class' => 'bg-teal-100 text-teal-900 border-teal-300',
                'dot' => 'bg-teal-600',
                'desc' => 'Seluruh barang fisik telah diterima dan kuota stok telah ditambahkan ke sistem inventaris bengkel.',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'class' => 'bg-red-100 text-red-900 border-red-300',
                'dot' => 'bg-red-600',
                'desc' => 'Usulan pengadaan ini ditolak oleh Waka Sarpras.',
            ],
            default => [
                'label' => ucfirst($pengadaan->status),
                'class' => 'bg-gray-100 text-gray-800 border-gray-300',
                'dot' => 'bg-gray-500',
                'desc' => '',
            ],
        };
    @endphp

    <div class="max-w-6xl mx-auto space-y-6 pb-12">
        <!-- Session Flash Notification -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Breadcrumb & Top Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="{{ route('toolman.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('toolman.pengadaan.index') }}"
                        class="hover:text-primary-600 transition-colors">Pengadaan RAB</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">#RAB-{{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $pengadaan->judul }}</h3>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-xs {{ $statusConfig['class'] }}">
                        <span class="w-2 h-2 rounded-full mr-2 {{ $statusConfig['dot'] }}"></span>
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-600 mt-2">
                    <span
                        class="font-mono font-semibold bg-gray-100 text-gray-800 px-2 py-0.5 rounded border border-gray-200">
                        #RAB-{{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="text-gray-400">&bull;</span>
                    <span class="font-medium text-gray-700">Bengkel: <strong
                            class="text-gray-900">{{ $pengadaan->bengkel->nama ?? $bengkel->nama }}</strong></span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('toolman.pengadaan.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>

                @if ($pengadaan->status === 'draft')
                    <form action="{{ route('toolman.pengadaan.destroy', $pengadaan->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus draf usulan RAB ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-3.5 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            Hapus Draf
                        </button>
                    </form>
                    <a href="{{ route('toolman.pengadaan.edit', $pengadaan->id) }}"
                        class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        Edit Draf
                    </a>
                    <form action="{{ route('toolman.pengadaan.submit', $pengadaan->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Apakah Anda yakin ingin mengajukan usulan RAB ini ke Waka Sarpras?');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Ajukan ke Waka
                        </button>
                    </form>
                @elseif ($pengadaan->status === 'revisi')
                    <a href="{{ route('toolman.pengadaan.edit', $pengadaan->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Perbaiki Revisi Sekarang
                    </a>
                @elseif ($pengadaan->status === 'approved')
                    <form action="{{ route('toolman.pengadaan.receive', $pengadaan->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Konfirmasi penerimaan barang fisik: Seluruh kuota barang pada usulan ini akan otomatis ditambahkan ke stok inventaris bengkel dan dicatat pada mutasi stok masuk. Lanjutkan?');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Konfirmasi Terima Fisik Barang
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Status Alert Banner -->
        @if ($pengadaan->status === 'revisi')
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 shadow-sm space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div
                            class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center shrink-0 mt-0.5 border border-orange-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-orange-950">Catatan Waka Sarpras: Usulan Perlu Direvisi</h4>
                            <p class="text-xs text-orange-800 mt-0.5">
                                Silakan sesuaikan item dan estimasi harga sesuai catatan review di bawah ini sebelum
                                diajukan kembali.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('toolman.pengadaan.edit', $pengadaan->id) }}"
                        class="shrink-0 inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-xs font-bold rounded-lg shadow-sm transition-colors self-start">
                        Perbaiki Usulan
                    </a>
                </div>

                <div class="bg-white p-4 rounded-lg border border-orange-200 text-sm text-gray-900 shadow-xs">
                    <span class="text-xs font-bold text-orange-900 uppercase tracking-wider block mb-1">Isi Catatan
                        Review:</span>
                    <p class="leading-relaxed font-medium text-gray-900">
                        "{{ $pengadaan->catatan_review ?? 'Harap lengkapi rincian harga referensi dan spesifikasi teknis barang.' }}"
                    </p>
                    <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                        <span>Peninjau: <strong
                                class="text-gray-700">{{ $pengadaan->direviewOleh->name ?? 'Waka Sarpras' }}</strong></span>
                        <span>&bull;</span>
                        <span>Waktu:
                            {{ $pengadaan->direview_pada ? \Carbon\Carbon::parse($pengadaan->direview_pada)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                </div>
            </div>
        @elseif ($pengadaan->status === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-xl p-5 shadow-sm space-y-3">
                <div class="flex items-start gap-3.5">
                    <div
                        class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0 mt-0.5 border border-red-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-red-950">Usulan RAB Tidak Disetujui (Ditolak)</h4>
                        <p class="text-xs text-red-800 mt-0.5">
                            Pengajuan ini belum dapat direalisasikan pada alokasi anggaran periode berjalan.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-red-200 text-sm text-gray-900 shadow-xs">
                    <span class="text-xs font-bold text-red-900 uppercase tracking-wider block mb-1">Alasan
                        Penolakan:</span>
                    <p class="leading-relaxed font-medium text-gray-900">
                        "{{ $pengadaan->catatan_review ?? 'Pengadaan tidak disetujui untuk periode ini.' }}"
                    </p>
                    <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                        <span>Ditinjau oleh: <strong
                                class="text-gray-700">{{ $pengadaan->direviewOleh->name ?? 'Waka Sarpras' }}</strong></span>
                        <span>&bull;</span>
                        <span>Waktu:
                            {{ $pengadaan->direview_pada ? \Carbon\Carbon::parse($pengadaan->direview_pada)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                </div>
            </div>
        @elseif ($pengadaan->status === 'approved')
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 shadow-sm space-y-3">
                <div class="flex items-start gap-3.5">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-emerald-950">Usulan RAB Telah Disetujui</h4>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Usulan pengadaan ini telah disetujui Waka Sarpras. Lakukan koordinasi terkait penerimaan barang
                            fisik.
                        </p>
                    </div>
                </div>

                @if ($pengadaan->catatan_review)
                    <div class="bg-white p-4 rounded-lg border border-emerald-200 text-sm text-gray-900 shadow-xs">
                        <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block mb-1">Catatan
                            Persetujuan:</span>
                        <p class="leading-relaxed font-medium text-gray-900">
                            "{{ $pengadaan->catatan_review }}"
                        </p>
                        <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                            <span>Disetujui oleh: <strong
                                    class="text-gray-700">{{ $pengadaan->direviewOleh->name ?? 'Waka Sarpras' }}</strong></span>
                            <span>&bull;</span>
                            <span>Waktu:
                                {{ $pengadaan->direview_pada ? \Carbon\Carbon::parse($pengadaan->direview_pada)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- 2-Column Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Card 1: Informasi Usulan (2 Kolom) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h4
                    class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Informasi Pengajuan RAB
                </h4>

                <div class="space-y-3 text-sm">
                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-center py-2 border-b border-gray-50 gap-1">
                        <span class="text-gray-500 text-xs font-medium">Bengkel / Jurusan:</span>
                        <span class="font-semibold text-gray-900">
                            {{ $pengadaan->bengkel->nama ?? '-' }}
                            <span
                                class="text-xs font-normal text-gray-500">({{ $pengadaan->bengkel->kode ?? '-' }})</span>
                        </span>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-center py-2 border-b border-gray-50 gap-1">
                        <span class="text-gray-500 text-xs font-medium">Pengusul (Toolman):</span>
                        <div class="sm:text-right">
                            <span
                                class="font-semibold text-gray-900">{{ $pengadaan->dibuatOleh->name ?? 'Toolman Bengkel' }}</span>
                            <span class="text-xs text-gray-500 block">{{ $pengadaan->dibuatOleh->email ?? '-' }}</span>
                        </div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-center py-2 border-b border-gray-50 gap-1">
                        <span class="text-gray-500 text-xs font-medium">Tanggal Dibuat:</span>
                        <span
                            class="font-medium text-gray-900">{{ $pengadaan->created_at->translatedFormat('d F Y, H:i') }}
                            WIB</span>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-center py-2 border-b border-gray-50 gap-1">
                        <span class="text-gray-500 text-xs font-medium">Tanggal Diajukan Resmi:</span>
                        <span class="font-medium text-gray-900">
                            {{ $pengadaan->diajukan_pada ? \Carbon\Carbon::parse($pengadaan->diajukan_pada)->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum diajukan (Status Draf)' }}
                        </span>
                    </div>

                    @if ($pengadaan->catatan)
                        <div class="pt-2">
                            <span class="text-gray-500 text-xs font-medium block mb-1">Catatan & Justifikasi
                                Kebutuhan:</span>
                            <div
                                class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-xs text-gray-800 leading-relaxed italic">
                                "{{ $pengadaan->catatan }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 2: Ringkasan Finansial (Clean & High Contrast) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between space-y-4">
                <div>
                    <h4
                        class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Ringkasan Estimasi
                    </h4>

                    <!-- Highlight Box Total -->
                    <div class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-4 text-center my-3">
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Estimasi Anggaran</p>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-950 mt-1 tracking-tight">
                            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Breakdown Items -->
                    <div class="space-y-2 pt-1 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 text-xs">Jumlah Macam Barang:</span>
                            <span class="font-bold text-gray-900">{{ $totalItem }} Item</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 text-xs">Total Unit/Kuantitas:</span>
                            <span class="font-bold text-gray-900">{{ $totalQty }} Satuan</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Status Usulan:</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold {{ $statusConfig['class'] }}">
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Table Rincian Barang -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div
                class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2 bg-gray-50/70">
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Rincian Barang & Estimasi Biaya Satuan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar item belanja, spesifikasi teknis, serta estimasi harga
                        satuan.</p>
                </div>
                <span
                    class="text-xs font-bold bg-white text-gray-700 px-3 py-1 rounded-lg border border-gray-200 shadow-2xs self-start sm:self-center">
                    {{ $totalItem }} Item Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead
                        class="bg-slate-50 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">No</th>
                            <th class="px-6 py-4">Nama & Spesifikasi Barang</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4 text-right">Estimasi Satuan</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($pengadaan->detailPengadaans as $idx => $detail)
                            @php
                                $subtotal = $detail->jumlah * $detail->harga_satuan;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 text-center text-xs text-gray-500 font-mono font-medium">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm">
                                        {{ $detail->nama_barang }}
                                    </div>
                                    @if ($detail->spesifikasi)
                                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                            {{ $detail->spesifikasi }}
                                        </p>
                                    @endif
                                    @if ($detail->barang)
                                        <span
                                            class="inline-flex items-center text-[11px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded mt-1.5 border border-emerald-200">
                                            Kode Barang: {{ $detail->barang->kode_barang }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-gray-900 text-sm">{{ $detail->jumlah }}</span>
                                    <span class="text-xs font-medium text-gray-500 block">{{ $detail->satuan }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-medium text-gray-700 whitespace-nowrap">
                                    Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-xs text-gray-500">
                                    Belum ada item barang yang dicantumkan dalam usulan RAB ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <th colspan="4"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Total Estimasi Anggaran:
                            </th>
                            <th class="px-6 py-4 text-right font-mono text-lg font-black text-emerald-900">
                                Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
