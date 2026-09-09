@extends('layouts.peminjam')

@section('title', 'Tiket Peminjaman Saya')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Flash Notification Alerts -->
        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl p-4 flex items-start gap-3 shadow-xs">
                <div
                    class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-bold">Berhasil!</h4>
                    <p class="text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl p-4 flex items-start gap-3 shadow-xs">
                <div
                    class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-bold">Peringatan:</h4>
                    <p class="text-rose-700 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Header & Search Controls -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Tiket Peminjaman Saya</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau status persetujuan, batas pengembalian, dan riwayat
                    sirkulasi alat praktik.</p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('peminjam.tiket.index') }}" class="w-full sm:w-72">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari ID tiket, nama barang..."
                        class="block w-full pl-9 pr-8 py-2 bg-white border border-gray-300 rounded-xl text-xs focus:ring-primary-500 focus:border-primary-500 shadow-2xs">
                    @if (request('search'))
                        <a href="{{ route('peminjam.tiket.index', array_merge(request()->query(), ['search' => null])) }}"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600">
                            &times;
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Status Tabs Bar -->
        <div
            class="bg-white p-2 sm:p-2.5 rounded-2xl border border-gray-200 shadow-2xs overflow-x-auto no-scrollbar flex items-center gap-1.5 text-xs font-semibold">
            @php
                $tabs = [
                    'all' => ['label' => 'Semua Tiket', 'count' => $counts['all']],
                    'pending' => ['label' => 'Menunggu Acc', 'count' => $counts['pending']],
                    'active' => ['label' => 'Sedang Dipinjam', 'count' => $counts['active']],
                    'menunggu_pengecekan' => ['label' => 'Cek Fisik', 'count' => $counts['menunggu_pengecekan']],
                    'selesai' => ['label' => 'Selesai', 'count' => $counts['selesai']],
                    'ditolak' => ['label' => 'Ditolak', 'count' => $counts['ditolak']],
                ];
            @endphp

            @foreach ($tabs as $key => $tab)
                <a href="{{ route('peminjam.tiket.index', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                    class="px-3 py-1.5 rounded-xl shrink-0 transition-colors flex items-center gap-1.5 {{ $filterStatus === $key ? 'bg-primary-600 text-white shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span
                        class="text-[10px] px-1.5 py-0.2 rounded-full {{ $filterStatus === $key ? 'bg-primary-800 text-white' : 'bg-gray-100 text-gray-600' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- List of Real Tickets -->
        <div class="space-y-4 sm:space-y-5">
            @forelse ($peminjamans as $tiket)
                @php
                    $isInventaris = $tiket->detailPeminjamans->some(
                        fn($d) => $d->barang && $d->barang->jenis_barang === 'inventaris',
                    );
                    $isLate =
                        $tiket->status === 'terlambat' ||
                        ($tiket->status === 'active' &&
                            $tiket->batas_kembali &&
                            \Carbon\Carbon::parse($tiket->batas_kembali)->isPast());

                    $statusConfig = match ($tiket->status) {
                        'pending' => [
                            'label' => 'Menunggu Persetujuan Toolman',
                            'badge' => 'bg-amber-100 text-amber-900 border-amber-300',
                            'dot' => 'bg-amber-600 animate-pulse',
                            'header' => 'border-amber-100 bg-amber-50/50',
                        ],
                        'active' => [
                            'label' => $isLate ? 'Terlambat Dikembalikan' : 'Aktif (Sedang Dipinjam)',
                            'badge' => $isLate
                                ? 'bg-red-100 text-red-900 border-red-300'
                                : 'bg-blue-100 text-blue-900 border-blue-300',
                            'dot' => $isLate ? 'bg-red-600 animate-pulse' : 'bg-blue-600 animate-pulse',
                            'header' => $isLate ? 'border-red-100 bg-red-50/50' : 'border-blue-100 bg-blue-50/50',
                        ],
                        'menunggu_pengecekan' => [
                            'label' => 'Menunggu Pengecekan Fisik',
                            'badge' => 'bg-purple-100 text-purple-900 border-purple-300',
                            'dot' => 'bg-purple-600',
                            'header' => 'border-purple-100 bg-purple-50/50',
                        ],
                        'selesai' => [
                            'label' => 'Selesai & Diverifikasi',
                            'badge' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            'dot' => 'bg-emerald-600',
                            'header' => 'border-gray-100 bg-gray-50/70',
                        ],
                        'ditolak' => [
                            'label' => 'Pengajuan Ditolak',
                            'badge' => 'bg-rose-100 text-rose-900 border-rose-300',
                            'dot' => 'bg-rose-600',
                            'header' => 'border-rose-100 bg-rose-50/50',
                        ],
                        default => [
                            'label' => ucfirst($tiket->status),
                            'badge' => 'bg-gray-100 text-gray-800 border-gray-300',
                            'dot' => 'bg-gray-500',
                            'header' => 'border-gray-100 bg-gray-50',
                        ],
                    };
                @endphp

                <div
                    class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden transition-all hover:shadow-sm">
                    <!-- Header Tiket -->
                    <div
                        class="px-4 sm:px-5 py-3 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 {{ $statusConfig['header'] }}">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="text-xs font-mono font-bold text-gray-800 bg-white px-2 py-0.5 rounded border border-gray-200">
                                #TRX-{{ str_pad($tiket->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-gray-300">&bull;</span>
                            <span class="text-xs text-gray-600 font-medium">
                                {{ \Carbon\Carbon::parse($tiket->tanggal_pinjam)->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                            <span class="text-gray-300">&bull;</span>
                            <span
                                class="text-xs font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                {{ $tiket->bengkel->nama ?? 'Bengkel' }}
                            </span>
                        </div>

                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusConfig['badge'] }} self-start sm:self-auto shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusConfig['dot'] }}"></span>
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>

                    <!-- Body Tiket -->
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <!-- Rincian Barang yang Diminta -->
                            <div class="space-y-2 flex-1">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Rincian Barang yang
                                    Diajukan:</h4>
                                <div
                                    class="bg-slate-50 border border-gray-200 rounded-xl p-3 space-y-2 divide-y divide-gray-100">
                                    @foreach ($tiket->detailPeminjamans as $detail)
                                        <div class="flex items-center justify-between text-xs sm:text-sm pt-1.5 first:pt-0">
                                            <div class="flex items-center space-x-2 min-w-0">
                                                <div
                                                    class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 {{ $detail->barang?->jenis_barang === 'inventaris' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    @if ($detail->barang?->jenis_barang === 'inventaris')
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <span
                                                    class="font-bold text-gray-900 truncate">{{ $detail->barang->nama ?? 'Barang Terhapus' }}</span>
                                                <span class="text-xs text-gray-500">({{ $detail->jumlah }}
                                                    {{ $detail->barang->satuan ?? 'Unit' }})</span>
                                                @if ($detail->barang && $detail->barang->jenis_barang === 'bhp')
                                                    <span
                                                        class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200 shrink-0">BHP</span>
                                                @endif
                                            </div>
                                            <span
                                                class="text-xs font-mono text-gray-400 shrink-0">{{ $detail->barang->kode_barang ?? '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Batas Waktu & Keperluan -->
                            <div
                                class="w-full md:w-72 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <p class="text-[11px] text-gray-400 font-semibold uppercase">Batas Pengembalian</p>
                                    <p
                                        class="text-xs sm:text-sm font-bold {{ $isLate ? 'text-rose-600' : 'text-gray-800' }}">
                                        @if ($tiket->batas_kembali)
                                            {{ \Carbon\Carbon::parse($tiket->batas_kembali)->translatedFormat('d M Y, H:i') }}
                                            WIB
                                        @else
                                            <span class="text-emerald-700">Tidak Perlu Pengembalian (BHP)</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 font-semibold uppercase">Keperluan Penggunaan</p>
                                    <p class="text-xs font-medium text-gray-800 line-clamp-2 italic leading-relaxed">
                                        "{{ $tiket->keperluan }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Tiket: Tombol Aksi -->
                    <div
                        class="px-4 sm:px-5 py-3 border-t border-gray-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-3">
                        <div class="text-xs text-gray-500 flex items-center gap-2 self-start sm:self-center">
                            @if ($tiket->status === 'active')
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-blue-700 font-medium">Harap kembalikan alat sebelum batas waktu untuk
                                    pengecekan fisik oleh Toolman.</span>
                            @elseif ($tiket->status === 'pending')
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-amber-800 font-medium">Menunggu verifikasi dari Toolman bengkel
                                    {{ $tiket->bengkel->nama }}.</span>
                            @elseif ($tiket->status === 'menunggu_pengecekan')
                                <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span class="text-purple-800 font-medium">Permohonan pengembalian terkirim. Bawa barang
                                    fisik ke meja Toolman sekarang.</span>
                            @elseif ($tiket->status === 'selesai')
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-emerald-800 font-medium">Peminjaman selesai dan telah diverifikasi oleh
                                    Toolman.</span>
                            @elseif ($tiket->status === 'ditolak')
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-rose-700 font-medium">Peminjaman tidak disetujui:
                                    {{ $tiket->alasan_penolakan ?? 'Kebutuhan bengkel belum terpenuhi.' }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <a href="{{ route('peminjam.tiket.show', $tiket->id) }}"
                                class="px-3.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl shadow-2xs transition-colors">
                                Lihat Detail
                            </a>

                            @if (in_array($tiket->status, ['active', 'terlambat']))
                                <form method="POST" action="{{ route('peminjam.tiket.kembalikan', $tiket->id) }}"
                                    onsubmit="return confirm('Apakah Anda yakin sudah siap mengembalikan alat fisik ke meja Toolman?')">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                        <span>Ajukan Pengembalian</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="bg-white rounded-3xl p-8 sm:p-12 text-center border border-gray-200 shadow-xs max-w-md mx-auto">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 mx-auto flex items-center justify-center mb-3.5">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Tidak Ada Tiket Peminjaman</h3>
                    <p class="text-xs text-gray-500 mt-1">Belum ada riwayat transaksi peminjaman untuk filter yang dipilih.
                    </p>
                    <a href="{{ route('peminjam.katalog.index') }}"
                        class="mt-4 inline-block px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 font-semibold rounded-xl text-xs transition-colors">
                        Buka Katalog Barang
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($peminjamans->hasPages())
            <div class="pt-4">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>
@endsection
