@extends('layouts.peminjam')

@section('title', 'Form Pengajuan Peminjaman')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Form Pengajuan Peminjaman 📋</h3>
                <p class="text-sm text-gray-500 mt-1">Lengkapi detail keperluan peminjaman alat atau bahan praktik.</p>
            </div>
            <a href="{{ route('peminjam.katalog.index') }}"
                class="text-sm font-medium text-primary-600 hover:text-primary-700">
                &larr; Kembali ke Katalog
            </a>
        </div>

        <!-- Form Container -->
        <form action="{{ route('peminjam.tiket.index') }}" method="GET"
            class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="p-6 space-y-6">

                <!-- Ringkasan Barang yang Dipilih -->
                <div class="bg-primary-50/50 border border-primary-100 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="h-12 w-12 rounded-lg bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                            🛠️
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-semibold uppercase tracking-wider text-primary-700 bg-primary-100 px-2 py-0.5 rounded">Alat
                                Inventaris</span>
                            <h4 class="text-base font-bold text-gray-900 mt-0.5">Crimping Tool RJ45</h4>
                            <p class="text-xs text-gray-500">Kode: INV-TKJ-012 &bull; Stok Tersedia: 5 Unit</p>
                        </div>
                    </div>
                </div>

                <!-- Input Jumlah & Tipe Pilihan (Simulasi Trigger Sistem Pintar) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah yang Dipinjam</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="5"
                                class="block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                            <span class="text-sm text-gray-500 font-medium">Unit</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Maksimal peminjaman 2 unit per siswa.</p>
                    </div>

                    <div>
                        <label for="tipe_barang" class="block text-sm font-medium text-gray-700">Kategori Barang</label>
                        <!-- Select di bawah ini memiliki fungsi onchange (simulasi interaksi UI pintar) -->
                        <select id="tipe_barang" name="tipe_barang" onchange="toggleKalender(this.value)"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                            <option value="inventaris" selected>Alat Inventaris (Wajib Dikembalikan)</option>
                            <option value="bahan">Bahan Habis Pakai (Tidak Dikembalikan)</option>
                        </select>
                    </div>
                </div>

                <!-- KELOMPOK KALENDER PENGEMBALIAN (Sistem Pintar Conditional) -->
                <div id="wrapper-kalender"
                    class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-gray-200 transition-all duration-200">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal & Jam Mulai
                            Pinjam</label>
                        <input type="datetime-local" id="tgl_mulai" name="tgl_mulai"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                    </div>

                    <div>
                        <label for="tgl_kembali" class="block text-sm font-medium text-gray-700">Tanggal & Jam
                            Pengembalian</label>
                        <input type="datetime-local" id="tgl_kembali" name="tgl_kembali"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                        <p class="text-[11px] text-orange-600 mt-1 font-medium">*Alat wajib dikembalikan sebelum batas waktu
                            habis.</p>
                    </div>
                </div>

                <!-- Catatan / Tujuan Penggunaan -->
                <div>
                    <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan Penggunaan /
                        Praktik</label>
                    <textarea id="tujuan" name="tujuan" rows="3" required
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm"
                        placeholder="Contoh: Untuk keperluan praktik Jaringan Dasar modul perutean bersama Pak Yono..."></textarea>
                </div>

            </div>

            <!-- Footer Action -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('peminjam.katalog.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    Ajukan Peminjaman Sekarang
                </button>
            </div>

        </form>
    </div>

    <!-- Skrip Sederhana untuk Simulasi Sistem Pintar Kalender -->
    <script>
        function toggleKalender(nilai) {
            const wrapperKalender = document.getElementById('wrapper-kalender');
            if (nilai === 'bahan') {
                // Jika bahan habis pakai dipilih, sembunyikan kalender
                wrapperKalender.style.opacity = '0';
                wrapperKalender.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    wrapperKalender.style.display = 'none';
                }, 150);
            } else {
                // Jika inventaris, tampilkan kembali kalender
                wrapperKalender.style.display = 'grid';
                setTimeout(() => {
                    wrapperKalender.style.opacity = '1';
                    wrapperKalender.style.transform = 'scale(1)';
                }, 50);
            }
        }
    </script>
@endsection
