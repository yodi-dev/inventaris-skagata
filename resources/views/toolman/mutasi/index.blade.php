@extends('layouts.admin')

@section('title', 'Riwayat Perubahan Stok')
@section('header_title', 'Riwayat Perubahan Stok & Mutasi')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        {{ $bengkel->nama ?? 'Bengkel Aktif' }}
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Log Mutasi & Pergerakan Stok</h3>
                <p class="text-sm text-gray-500 mt-1">Audit log otomatis dari setiap transaksi stok masuk, peminjaman,
                    pengembalian, dan bahan habis pakai.</p>
            </div>

            <div
                class="text-xs text-gray-500 bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm self-start sm:self-auto">
                Total tercatat: <span class="font-bold text-gray-900">{{ $movements->total() }}</span> mutasi
            </div>
        </div>

        <!-- Filter Form Controls -->
        <form method="GET" action="{{ route('toolman.mutasi.index') }}"
            class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Search Barang -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cari Barang</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama atau kode barang..."
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-xs focus:ring-primary-500 focus:border-primary-500 bg-white transition-colors">
                    </div>
                </div>

                <!-- Jenis Mutasi -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Jenis Mutasi</label>
                    <select name="jenis" onchange="this.form.submit()"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-xs focus:ring-primary-500 focus:border-primary-500 bg-white transition-colors">
                        <option value="">Semua Jenis Mutasi</option>
                        <option value="stok_masuk" {{ request('jenis') === 'stok_masuk' ? 'selected' : '' }}>Stok Masuk /
                            Pengadaan</option>
                        <option value="peminjaman" {{ request('jenis') === 'peminjaman' ? 'selected' : '' }}>Peminjaman
                            Keluar</option>
                        <option value="pengembalian_baik" {{ request('jenis') === 'pengembalian_baik' ? 'selected' : '' }}>
                            Pengembalian Baik</option>
                        <option value="pengembalian_rusak"
                            {{ request('jenis') === 'pengembalian_rusak' ? 'selected' : '' }}>Pengembalian Rusak</option>
                        <option value="bhp_keluar" {{ request('jenis') === 'bhp_keluar' ? 'selected' : '' }}>Pengeluaran BHP
                        </option>
                        <option value="penyesuaian" {{ request('jenis') === 'penyesuaian' ? 'selected' : '' }}>Penyesuaian /
                            Koreksi</option>
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        onchange="this.form.submit()"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-xs focus:ring-primary-500 focus:border-primary-500 bg-white transition-colors">
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-xs focus:ring-primary-500 focus:border-primary-500 bg-white transition-colors">
                </div>
            </div>

            <!-- Hidden Submit for Enter Key Accessibility on Search Input -->
            <button type="submit" class="hidden" aria-hidden="true"></button>

            @if (request()->anyFilled(['search', 'jenis', 'start_date', 'end_date']))
                <!-- Status & Tombol Reset Tunggal -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 text-xs text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Filter aktif diterapkan
                        </span>
                        <span class="text-xs text-gray-400 hidden sm:inline">&bull; Data diperbarui otomatis</span>
                    </div>

                    <a href="{{ route('toolman.mutasi.index') }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50/70 hover:bg-red-100/80 border border-red-200/80 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        Reset Filter
                    </a>
                </div>
            @endif
        </form>

        <!-- Table Log Mutasi -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Waktu & ID</th>
                            <th class="px-6 py-3.5">Barang & Kode</th>
                            <th class="px-6 py-3.5 text-center">Jenis Mutasi</th>
                            <th class="px-6 py-3.5 text-center">Perubahan Qty</th>
                            <th class="px-6 py-3.5">Petugas / Aktor</th>
                            <th class="px-6 py-3.5">Keterangan & Referensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($movements as $m)
                            @php
                                $jenisConfig = match ($m->jenis) {
                                    'stok_masuk' => [
                                        'label' => 'Stok Masuk',
                                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'sign' => '+',
                                        'sign_class' => 'text-emerald-700 font-bold',
                                    ],
                                    'peminjaman' => [
                                        'label' => 'Peminjaman',
                                        'class' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'sign' => '-',
                                        'sign_class' => 'text-blue-700 font-bold',
                                    ],
                                    'pengembalian_baik' => [
                                        'label' => 'Kembali (Baik)',
                                        'class' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'sign' => '+',
                                        'sign_class' => 'text-teal-700 font-bold',
                                    ],
                                    'pengembalian_rusak' => [
                                        'label' => 'Kembali (Rusak)',
                                        'class' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'sign' => '',
                                        'sign_class' => 'text-orange-700 font-bold',
                                    ],
                                    'bhp_keluar' => [
                                        'label' => 'BHP Keluar',
                                        'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'sign' => '-',
                                        'sign_class' => 'text-amber-700 font-bold',
                                    ],
                                    'penyesuaian' => [
                                        'label' => 'Penyesuaian',
                                        'class' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'sign' => '±',
                                        'sign_class' => 'text-purple-700 font-bold',
                                    ],
                                    default => [
                                        'label' => ucfirst(str_replace('_', ' ', $m->jenis)),
                                        'class' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'sign' => '',
                                        'sign_class' => 'text-gray-700',
                                    ],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <!-- Waktu & ID -->
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <span class="font-mono text-xs text-gray-500 font-semibold block">
                                        #LOG-{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <p class="text-xs text-gray-900 font-medium mt-0.5">
                                        {{ $m->created_at->translatedFormat('d M Y') }}
                                    </p>
                                    <p class="text-[11px] text-gray-400">
                                        {{ $m->created_at->format('H:i') }} WIB
                                    </p>
                                </td>

                                <!-- Barang & Kode -->
                                <td class="px-6 py-4 align-top">
                                    @if ($m->barang)
                                        <div class="font-bold text-gray-900">
                                            {{ $m->barang->nama }}
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span
                                                class="font-mono text-xs text-gray-500 font-medium">{{ $m->barang->kode_barang }}</span>
                                            <span class="text-gray-300">&bull;</span>
                                            <span
                                                class="text-[11px] px-1.5 py-0.2 rounded bg-gray-100 text-gray-600 uppercase font-semibold">
                                                {{ $m->barang->tipe ?? 'ASET' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Barang Terhapus</span>
                                    @endif
                                </td>

                                <!-- Jenis Mutasi -->
                                <td class="px-6 py-4 text-center whitespace-nowrap align-top">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $jenisConfig['class'] }}">
                                        {{ $jenisConfig['label'] }}
                                    </span>
                                </td>

                                <!-- Perubahan Qty -->
                                <td class="px-6 py-4 text-center whitespace-nowrap align-top">
                                    <span class="text-sm font-bold {{ $jenisConfig['sign_class'] }}">
                                        {{ $jenisConfig['sign'] }}{{ $m->jumlah }}
                                    </span>
                                    <span class="text-xs text-gray-500 block">
                                        {{ $m->barang->satuan ?? 'unit' }}
                                    </span>
                                </td>

                                <!-- Petugas / Aktor -->
                                <td class="px-6 py-4 align-top whitespace-nowrap">
                                    <p class="font-medium text-gray-900 text-xs">{{ $m->user->name ?? 'Sistem Otomatis' }}
                                    </p>
                                    <p class="text-[11px] text-gray-400">{{ $m->user->role ?? '-' }}</p>
                                </td>

                                <!-- Keterangan & Referensi -->
                                <td class="px-6 py-4 align-top max-w-sm">
                                    <p class="text-xs text-gray-700 leading-relaxed">
                                        {{ $m->keterangan ?? '-' }}
                                    </p>
                                    @if ($m->referensi_tipe && $m->referensi_id)
                                        <span
                                            class="inline-flex items-center text-[10px] font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded mt-1">
                                            Ref: {{ ucfirst($m->referensi_tipe) }} #{{ $m->referensi_id }}
                                        </span>
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
                                        <h4 class="text-sm font-bold text-gray-900">Belum Ada Catatan Mutasi</h4>
                                        <p class="text-xs text-gray-500 mt-1">Tidak ditemukan riwayat mutasi stok untuk
                                            rentang filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
