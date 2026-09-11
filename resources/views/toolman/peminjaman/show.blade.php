@extends('layouts.admin')

@section('title', 'Detail Tiket Peminjaman #' . $peminjaman->id)
@section('header_title', 'Sirkulasi - Detail Tiket Peminjaman')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">

        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="{{ route('toolman.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('toolman.peminjaman.index') }}"
                        class="hover:text-primary-600 transition-colors">Persetujuan Peminjaman</a>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Tiket #{{ $peminjaman->id }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Tiket Peminjaman
                        #{{ $peminjaman->id }}</h3>
                    @if ($peminjaman->status === 'pending' || $peminjaman->status === 'menunggu_acc')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            Menunggu Persetujuan
                        </span>
                    @elseif ($peminjaman->status === 'active' || $peminjaman->status === 'aktif')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Sedang Dipinjam
                        </span>
                    @elseif ($peminjaman->status === 'selesai')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Selesai
                        </span>
                    @elseif ($peminjaman->status === 'ditolak')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                            Ditolak
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 capitalize">
                            {{ $peminjaman->status }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('toolman.peminjaman.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Antrean
                </a>
            </div>
        </div>

        <!-- Grid: Info Peminjam & Jadwal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card 1: Data Peminjam -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
                <h4
                    class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Peminjam
                </h4>
                <div class="flex items-center space-x-4">
                    <div
                        class="h-12 w-12 rounded-full {{ $peminjaman->user && $peminjaman->user->isGuru() ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-gray-900">{{ $peminjaman->user->name ?? '-' }}</h5>
                        <p class="text-xs text-gray-500">
                            {{ $peminjaman->user && $peminjaman->user->isGuru() ? 'Guru / Instruktur' : 'Siswa Bengkel' }}
                        </p>
                    </div>
                </div>
                <div class="space-y-2 pt-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Nomor Identitas (NIS/NIP):</span>
                        <span
                            class="font-mono font-medium text-gray-900">{{ $peminjaman->user->nomor_identitas ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Email:</span>
                        <span class="text-gray-900">{{ $peminjaman->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">WhatsApp:</span>
                        <span class="text-gray-900">{{ $peminjaman->user->nomor_wa ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Jadwal & Keperluan -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
                <h4
                    class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Waktu & Keperluan
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Bengkel:</span>
                        <span class="font-medium text-gray-900">{{ $peminjaman->bengkel->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Tanggal Pengajuan:</span>
                        <span
                            class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y, H:i') }}
                            WIB</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Batas Pengembalian:</span>
                        <span
                            class="font-medium text-gray-900">{{ $peminjaman->batas_kembali ? \Carbon\Carbon::parse($peminjaman->batas_kembali)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari yang sama' }}</span>
                    </div>
                    <div class="py-1">
                        <span class="text-gray-500 block mb-1">Keperluan / Keterangan:</span>
                        <p class="text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100 font-medium">
                            {{ $peminjaman->keperluan ?? 'Tidak ada catatan keperluan.' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Table: Rincian Barang Diminta -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h4 class="font-bold text-gray-900 text-sm">Daftar Barang Diminta</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Kode & Nama Barang</th>
                            <th class="px-6 py-3.5 font-semibold">Tipe</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Jumlah Diminta</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Stok Tersedia</th>
                            <th class="px-6 py-3.5 font-semibold">Kondisi Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($peminjaman->detailPeminjamans as $detail)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $detail->barang->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">
                                        {{ $detail->barang->kode_barang ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $detail->barang && $detail->barang->jenis_barang === 'bhp' ? 'bg-orange-50 text-orange-700' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $detail->barang && $detail->barang->jenis_barang === 'bhp' ? 'Bahan Habis Pakai' : 'Inventaris' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900">
                                    {{ $detail->jumlah }} {{ $detail->barang->satuan ?? 'Unit' }}
                                </td>
                                <td
                                    class="px-6 py-4 text-center font-medium {{ ($detail->barang->stok_tersedia ?? 0) < $detail->jumlah ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                    {{ $detail->barang->stok_tersedia ?? 0 }} {{ $detail->barang->satuan ?? 'Unit' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    @if ($peminjaman->status === 'selesai')
                                        <span class="text-green-600 font-medium">Kembali Baik:
                                            {{ $detail->jumlah_baik ?? $detail->jumlah }}</span>
                                        @if ($detail->jumlah_rusak > 0)
                                            <br><span class="text-red-600">Rusak: {{ $detail->jumlah_rusak }}</span>
                                        @endif
                                        @if ($detail->jumlah_hilang > 0)
                                            <br><span class="text-red-600">Hilang: {{ $detail->jumlah_hilang }}</span>
                                        @endif
                                    @else
                                        <span>Layak Dipinjam</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada detail barang pada
                                    tiket ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action / Processing Status Card -->
        @if (in_array($peminjaman->status, ['pending', 'menunggu_acc']))
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h4 class="text-base font-bold text-gray-900">Konfirmasi Persetujuan Peminjaman</h4>
                        <p class="text-sm text-gray-500 mt-1">
                            Pastikan ketersediaan fisik alat & bahan di bengkel sebelum menyerahkannya kepada peminjam.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <!-- Form Tolak Modal -->
                        <form action="{{ route('toolman.peminjaman.reject', $peminjaman->id) }}" method="POST" class="inline"
                              data-confirm="true"
                              data-title="Tolak Permohonan Peminjaman"
                              data-message="Berikan alasan penolakan tiket <b>#PINJAM-{{ str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }}</b> milik <b>{{ addslashes($peminjaman->user->name ?? 'Peminjam') }}</b>:"
                              data-type="danger"
                              data-confirm-text="Tolak Pengajuan"
                              data-with-input="true"
                              data-input-name="alasan"
                              data-input-label="Alasan Penolakan (Wajib):"
                              data-input-placeholder="Contoh: Alat sedang dalam perbaikan berkala..."
                              data-input-required="true">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-xl shadow-sm transition-colors">
                                Tolak Pengajuan
                            </button>
                        </form>

                        <!-- Form Approve & Serahkan Modal -->
                        <form action="{{ route('toolman.peminjaman.approve', $peminjaman->id) }}" method="POST" class="inline"
                              data-confirm="true"
                              data-title="Setujui Peminjaman & Serahkan Barang"
                              data-message="Apakah Anda yakin ingin menyetujui peminjaman tiket <b>#PINJAM-{{ str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }}</b> untuk <b>{{ addslashes($peminjaman->user->name ?? 'Peminjam') }}</b>? Pastikan barang fisik telah diserahkan di bengkel."
                              data-type="success"
                              data-confirm-text="Ya, Setujui & Serahkan">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve & Serahkan Barang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Informasi Proses Tiket -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-3">
                <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Pemrosesan Tiket
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Diproses Oleh:</span>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $peminjaman->diprosesOleh->name ?? 'Toolman' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Waktu Diproses:</span>
                        <p class="font-semibold text-gray-900 mt-0.5">
                            {{ $peminjaman->diproses_pada ? \Carbon\Carbon::parse($peminjaman->diproses_pada)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                        </p>
                    </div>
                </div>
                @if ($peminjaman->status === 'ditolak' && $peminjaman->alasan_penolakan)
                    <div class="mt-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm">
                        <p class="font-bold text-red-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Alasan Penolakan:
                        </p>
                        <p class="text-red-700 mt-1 pl-6">{{ $peminjaman->alasan_penolakan }}</p>
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- Modal Tolak Pengajuan -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRejectModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="rejectForm" method="POST" action="">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 text-red-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Tolak Pengajuan Peminjaman</h3>
                                <p class="text-sm text-gray-500 mt-1" id="rejectPeminjamName">
                                    Berikan alasan penolakan agar peminjam dapat memahami alasan pembatalan.
                                </p>
                                <div class="mt-4">
                                    <label for="alasan_penolakan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="alasan_penolakan" id="alasan_penolakan" rows="3" required
                                        placeholder="Contoh: Alat sedang proses maintenance berkala atau peruntukan praktikum belum disetujui guru pembimbing."
                                        class="w-full text-sm border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 p-3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3.5 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                            Konfirmasi Tolak
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-xl transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(id, peminjamName) {
            const form = document.getElementById('rejectForm');
            form.action = "{{ url('/toolman/peminjaman') }}/" + id + "/reject";
            document.getElementById('rejectPeminjamName').innerText = "Pengajuan oleh: " + peminjamName + ". Masukkan alasan penolakan:";
            document.getElementById('alasan_penolakan').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection
