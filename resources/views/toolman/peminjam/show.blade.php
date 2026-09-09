@extends('layouts.admin')

@section('title', 'Detail Peminjam - ' . $peminjam->name)
@section('header_title', 'Detail Informasi Peminjam')

@section('content')
    @php
        $totalSirkulasi = $peminjam->peminjamans->count();
        $pinjamanAktif = $peminjam->peminjamans->whereIn('status', ['pending', 'aktif', 'terlambat'])->count();
        $pinjamanSelesai = $peminjam->peminjamans->where('status', 'selesai')->count();
        $pinjamanTerlambat = $peminjam->peminjamans->where('status', 'terlambat')->count();

        $initial = strtoupper(substr($peminjam->name, 0, 1));

        $statusConfig = match ($peminjam->status) {
            'aktif' => [
                'label' => 'Akun Aktif',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            'menunggu_acc' => [
                'label' => 'Menunggu Approval',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500 animate-pulse',
            ],
            'suspend' => [
                'label' => 'Ditangguhkan (Suspend)',
                'class' => 'bg-red-50 text-red-700 border-red-200',
                'dot' => 'bg-red-600',
            ],
            default => [
                'label' => ucfirst($peminjam->status),
                'class' => 'bg-gray-100 text-gray-700 border-gray-200',
                'dot' => 'bg-gray-400',
            ],
        };
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Breadcrumb & Top Controls -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="{{ route('toolman.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('toolman.peminjam.index') }}" class="hover:text-primary-600 transition-colors">Manajemen
                        Peminjam</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">{{ $peminjam->name }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $peminjam->name }}</h3>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['class'] }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusConfig['dot'] }}"></span>
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Peran: <span
                        class="font-semibold text-gray-700">{{ ucfirst($peminjam->jenis_peminjam ?? 'Siswa') }}</span>
                    &bull; Bengkel Akses: <span
                        class="font-semibold text-gray-700">{{ $bengkel->nama ?? 'Semua Bengkel' }}</span></p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('toolman.peminjam.index') }}"
                    class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    &larr; Kembali
                </a>

                @if ($peminjam->nomor_wa)
                    @php
                        $cleanWa = preg_replace('/[^0-9]/', '', $peminjam->nomor_wa);
                        if (str_starts_with($cleanWa, '0')) {
                            $cleanWa = '62' . substr($cleanWa, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanWa }}" target="_blank"
                        class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2z" />
                        </svg>
                        WhatsApp Peminjam
                    </a>
                @endif
            </div>
        </div>

        <!-- Sanksi Alert jika status suspend -->
        @if ($peminjam->status === 'suspend')
            <div class="bg-red-50 border border-red-200 rounded-xl p-5 flex items-start gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                        </path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-red-900">Akun Peminjam Sedang Ditangguhkan (Suspend)</h4>
                    <p class="text-xs text-red-800 mt-1 leading-relaxed">
                        Peminjam ini tidak memiliki hak untuk membuat tiket peminjaman baru di katalog. Penangguhan akun
                        disebabkan oleh pelanggaran (kerusakan alat, kehilangan alat, atau keterlambatan berulang).
                        Pemulihan akun dapat dilakukan di halaman Manajemen Peminjam setelah urusan ganti rugi diselesaikan.
                    </p>
                </div>
            </div>
        @endif

        <!-- 4 Metrik Kartu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tiket</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalSirkulasi }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Semua riwayat peminjaman</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-blue-900 mt-1">{{ $pinjamanAktif }}</p>
                <p class="text-xs text-blue-500 mt-0.5">Tanggungan aktif</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Telah Dikembalikan</p>
                <p class="text-2xl font-bold text-emerald-900 mt-1">{{ $pinjamanSelesai }}</p>
                <p class="text-xs text-emerald-500 mt-0.5">Selesai & kondisi baik</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Terlambat</p>
                <p class="text-2xl font-bold text-amber-900 mt-1">{{ $pinjamanTerlambat }}</p>
                <p class="text-xs text-amber-500 mt-0.5">Lewat batas waktu</p>
            </div>
        </div>

        <!-- Profil Details & Bengkel Info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Informasi Profil & Kontak</h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div
                    class="flex items-center space-x-4 md:col-span-1 border-b md:border-b-0 md:border-r border-gray-100 pb-4 md:pb-0 pr-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-primary-100 text-primary-700 font-black text-2xl flex items-center justify-center border border-primary-200 shrink-0">
                        {{ $initial }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-base">{{ $peminjam->name }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $peminjam->email }}</p>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 mt-1.5">
                            {{ ucfirst($peminjam->jenis_peminjam ?? 'Siswa') }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-gray-400 block">Nomor Identitas (NIS/NIP)</span>
                        <span class="font-mono font-semibold text-gray-800">{{ $peminjam->nomor_identitas ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Nomor WhatsApp</span>
                        <span class="font-semibold text-gray-800">{{ $peminjam->nomor_wa ?? '-' }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-gray-400 block">Bengkel Terdaftar</span>
                        <span
                            class="font-semibold text-gray-800">{{ $peminjam->bengkel->nama ?? ($peminjam->jenis_peminjam === 'guru' ? 'Lintas Jurusan (Guru)' : '-') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Terdaftar Pada Sistem</span>
                        <span class="font-medium text-gray-800">{{ $peminjam->created_at->translatedFormat('d F Y, H:i') }}
                            WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Peminjaman di Bengkel Ini -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Riwayat Sirkulasi Peminjaman Bengkel</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar transaksi peminjaman alat dan bahan pada
                        {{ $bengkel->nama ?? 'Bengkel Ini' }}.</p>
                </div>
                <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-lg">
                    {{ $totalSirkulasi }} Tiket
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-slate-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">ID Tiket & Waktu</th>
                            <th class="px-6 py-3.5">Jadwal Pinjam</th>
                            <th class="px-6 py-3.5">Daftar Barang</th>
                            <th class="px-6 py-3.5">Keperluan</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($peminjam->peminjamans as $p)
                            @php
                                $statusLoan = match ($p->status) {
                                    'pending' => [
                                        'label' => 'Menunggu Approval',
                                        'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'dot' => 'bg-amber-500 animate-pulse',
                                    ],
                                    'disetujui', 'aktif' => [
                                        'label' => 'Aktif Dipinjam',
                                        'class' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'dot' => 'bg-blue-500',
                                    ],
                                    'terlambat' => [
                                        'label' => 'Terlambat',
                                        'class' => 'bg-red-50 text-red-700 border-red-200',
                                        'dot' => 'bg-red-500 animate-ping',
                                    ],
                                    'selesai' => [
                                        'label' => 'Selesai Dikembalikan',
                                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'dot' => 'bg-emerald-500',
                                    ],
                                    'ditolak' => [
                                        'label' => 'Ditolak',
                                        'class' => 'bg-gray-100 text-gray-700 border-gray-300',
                                        'dot' => 'bg-gray-400',
                                    ],
                                    default => [
                                        'label' => ucfirst($p->status),
                                        'class' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'dot' => 'bg-gray-400',
                                    ],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                        #TK-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $p->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <p class="text-gray-800 font-medium">Pinjam:
                                        {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}</p>
                                    <p class="text-gray-500 mt-0.5">Batas:
                                        {{ \Carbon\Carbon::parse($p->tanggal_kembali_rencana)->translatedFormat('d M Y') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="space-y-0.5 text-xs text-gray-800">
                                        @foreach ($p->detailPeminjamans as $d)
                                            <p class="line-clamp-1">
                                                &bull; <span class="font-semibold">{{ $d->barang->nama ?? 'Item' }}</span>
                                                ({{ $d->jumlah }} {{ $d->barang->satuan ?? 'unit' }})
                                            </p>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-6 py-4 max-w-xs text-xs text-gray-600">
                                    <p class="line-clamp-2 italic">"{{ $p->keperluan ?? '-' }}"</p>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusLoan['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusLoan['dot'] }}"></span>
                                        {{ $statusLoan['label'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('toolman.peminjaman.show', $p->id) }}"
                                        class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg shadow-sm transition-colors">
                                        Lihat Tiket
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-xs text-gray-500">
                                    Belum ada catatan riwayat peminjaman di bengkel ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
