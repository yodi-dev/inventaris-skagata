@extends('layouts.admin')

@section('title', 'Daftar Pengadaan RAB')
@section('header_title', 'Pengadaan Barang & RAB Bengkel')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header & Action Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        {{ $bengkel->nama ?? 'Bengkel Terpilih' }}
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Usulan Rencana Anggaran Biaya (RAB)</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola draf RAB, pengajuan baru ke Waka Sarpras, pantau status
                    persetujuan & perbaikan revisi.</p>
            </div>
            <a href="{{ route('toolman.pengadaan.create') }}"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Usulan RAB Baru
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Filter Tab Bar & Status Filter -->
        <div
            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Status Tabs (Desktop / Mobile) -->
            <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                @php
                    $statusTabs = [
                        '' => 'Semua',
                        'draft' => 'Draf',
                        'pending' => 'Menunggu Approval',
                        'revisi' => 'Perlu Revisi',
                        'approved' => 'Disetujui',
                        'selesai' => 'Selesai / Diterima',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                @foreach ($statusTabs as $val => $label)
                    <a href="{{ route('toolman.pengadaan.index', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filterStatus === $val ? 'bg-primary-50 text-primary-700 border border-primary-200' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Info Count -->
            <div class="text-xs text-gray-500 shrink-0 self-end md:self-center">
                Total usulan: <span class="font-bold text-gray-800">{{ $pengadaans->total() }}</span> RAB
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">ID & Tanggal</th>
                            <th class="px-6 py-4">Judul & Keterangan</th>
                            <th class="px-6 py-4">Item Pengadaan</th>
                            <th class="px-6 py-4 text-right">Estimasi Biaya</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($pengadaans as $rab)
                            @php
                                $totalItems = $rab->detailPengadaans->count();
                                $totalBiaya = $rab->detailPengadaans->sum(fn($d) => $d->jumlah * $d->harga_satuan);
                                $tanggalTampil = $rab->diajukan_pada
                                    ? \Carbon\Carbon::parse($rab->diajukan_pada)->translatedFormat('d M Y')
                                    : $rab->created_at->translatedFormat('d M Y');

                                $statusBadge = match ($rab->status) {
                                    'draft' => [
                                        'label' => 'Draf Disimpan',
                                        'class' => 'bg-gray-100 text-gray-700 border-gray-300',
                                        'dot' => 'bg-gray-400',
                                    ],
                                    'pending' => [
                                        'label' => 'Menunggu Waka',
                                        'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'dot' => 'bg-amber-500 animate-pulse',
                                    ],
                                    'revisi' => [
                                        'label' => 'Perlu Revisi',
                                        'class' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'dot' => 'bg-orange-500',
                                    ],
                                    'approved' => [
                                        'label' => 'Disetujui',
                                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'dot' => 'bg-emerald-500',
                                    ],
                                    'selesai' => [
                                        'label' => 'Barang Diterima',
                                        'class' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'dot' => 'bg-blue-500',
                                    ],
                                    'rejected' => [
                                        'label' => 'Ditolak',
                                        'class' => 'bg-red-50 text-red-700 border-red-200',
                                        'dot' => 'bg-red-500',
                                    ],
                                    default => [
                                        'label' => ucfirst($rab->status),
                                        'class' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        'dot' => 'bg-gray-400',
                                    ],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <!-- ID & Tanggal -->
                                <td class="px-6 py-4 align-top whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                        #RAB-{{ str_pad($rab->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1.5">{{ $tanggalTampil }}</p>
                                    <p class="text-[11px] text-gray-400">Oleh: {{ $rab->dibuatOleh->name ?? 'Toolman' }}</p>
                                </td>

                                <!-- Judul & Keterangan -->
                                <td class="px-6 py-4 align-top max-w-xs">
                                    <a href="{{ route('toolman.pengadaan.show', $rab->id) }}"
                                        class="font-bold text-gray-900 hover:text-primary-600 line-clamp-2 transition-colors">
                                        {{ $rab->judul }}
                                    </a>
                                    @if ($rab->catatan)
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-1 italic">
                                            "{{ $rab->catatan }}"
                                        </p>
                                    @endif

                                    @if ($rab->status === 'revisi' && $rab->catatan_review)
                                        <div
                                            class="mt-2 p-2 bg-orange-50 border border-orange-200 rounded-lg text-xs text-orange-800">
                                            <p class="font-bold flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Catatan Waka:
                                            </p>
                                            <p class="mt-0.5 text-orange-700">{{ $rab->catatan_review }}</p>
                                        </div>
                                    @elseif ($rab->status === 'approved' && $rab->direviewOleh)
                                        <p class="text-[11px] text-emerald-700 mt-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Disetujui: {{ $rab->direviewOleh->name ?? 'Waka Sarpras' }}
                                        </p>
                                    @endif
                                </td>

                                <!-- Item Pengadaan Summary -->
                                <td class="px-6 py-4 align-top">
                                    <div class="space-y-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $totalItems }} Item Barang
                                        </span>
                                        <ul class="text-xs text-gray-600 space-y-0.5 mt-1">
                                            @foreach ($rab->detailPengadaans->take(2) as $item)
                                                <li class="line-clamp-1">
                                                    &bull; {{ $item->nama_barang }} ({{ $item->jumlah }}
                                                    {{ $item->satuan }})
                                                </li>
                                            @endforeach
                                            @if ($totalItems > 2)
                                                <li class="text-[11px] text-gray-400 font-medium">
                                                    +{{ $totalItems - 2 }} item lainnya...
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>

                                <!-- Estimasi Biaya -->
                                <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                    <p class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Total Estimasi</p>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 align-top text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusBadge['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusBadge['dot'] }}"></span>
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 align-top text-right whitespace-nowrap space-x-1">
                                    <a href="{{ route('toolman.pengadaan.show', $rab->id) }}"
                                        class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg shadow-sm transition-colors"
                                        title="Lihat Rincian Pengadaan">
                                        <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Detail
                                    </a>

                                    @if (in_array($rab->status, ['draft', 'revisi']))
                                        <a href="{{ route('toolman.pengadaan.edit', $rab->id) }}"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-primary-50 border border-primary-200 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                                            title="{{ $rab->status === 'revisi' ? 'Perbaiki Revisi' : 'Lanjutkan Edit Draf' }}">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            {{ $rab->status === 'revisi' ? 'Revisi' : 'Edit' }}
                                        </a>
                                    @endif

                                    @if ($rab->status === 'approved')
                                        <a href="{{ route('toolman.pengadaan.show', $rab->id) }}"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                                            title="Konfirmasi Penerimaan Fisik Barang">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Terima Barang
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mx-auto text-gray-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900">Belum Ada Usulan RAB</h4>
                                        <p class="text-xs text-gray-500 mt-1">Tidak ditemukan usulan rencana anggaran biaya
                                            untuk status yang dipilih.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('toolman.pengadaan.create') }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                Buat Usulan Baru
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pengadaans->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $pengadaans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
