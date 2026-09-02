@extends('layouts.admin')

@section('title', 'Buat Pengajuan RAB')
@section('header_title', 'Pengadaan - Rencana Anggaran Biaya')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header & Auto-Generate Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Buat Pengajuan Baru</h3>
                <p class="text-sm text-gray-500 mt-1">Buat draf RAB (Rencana Anggaran Biaya) untuk diajukan ke Waka Sarpras.
                </p>
            </div>
            <div>
                <!-- Tombol Pintar "Generate" dengan style stand-out (outline primary) -->
                <button type="button"
                    class="inline-flex items-center px-4 py-2 bg-primary-50 border border-primary-200 hover:bg-primary-100 text-primary-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Generate dari Stok Limit & Rusak
                </button>
            </div>
        </div>

        <!-- Form Container -->
        <form action="#" method="POST" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <!-- Section 1: Informasi Umum Pengajuan -->
            <div class="p-6 border-b border-gray-200 space-y-5 bg-gray-50/50">
                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Informasi Pengajuan</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Judul Pengajuan -->
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Pengajuan</label>
                        <input type="text" id="judul" name="judul"
                            value="Pengadaan Alat Praktik Jaringan Genap 2026"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                    </div>
                    <!-- Kategori/Tujuan -->
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori / Tujuan</label>
                        <select id="kategori" name="kategori"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                            <option value="rutin">Belanja Rutin (Bahan Habis Pakai)</option>
                            <option value="praktik" selected>Fasilitas Praktik Siswa</option>
                            <option value="perbaikan">Perbaikan / Penggantian Alat Rusak</option>
                        </select>
                    </div>
                    <!-- Keterangan Tambahan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan Tambahan
                            (Opsional)</label>
                        <textarea id="keterangan" name="keterangan" rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm"
                            placeholder="Contoh: Diajukan untuk persiapan UKK bulan depan..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Daftar Barang (Tabel Dinamis) -->
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Daftar Barang (Item)</h4>
                    <button type="button"
                        class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                        + Tambah Baris Kosong
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium w-1/3">Nama & Spesifikasi Barang</th>
                                <th class="px-4 py-3 text-center font-medium w-24">Jumlah</th>
                                <th class="px-4 py-3 text-left font-medium w-40">Estimasi Satuan</th>
                                <th class="px-4 py-3 text-left font-medium w-40">Subtotal</th>
                                <th class="px-4 py-3 text-center font-medium w-16">Aksi</th>
                            </tr>
                        </thead>
                        <!-- Hasil simulasi klik tombol "Generate" -->
                        <tbody class="divide-y divide-gray-100 bg-white text-sm">

                            <!-- Row 1: Generate dari Stok Limit -->
                            <tr class="group">
                                <td class="px-4 py-3">
                                    <input type="text" value="Kabel UTP Cat6 (Belden)"
                                        class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 mb-2">
                                    <input type="text" value="Minimal panjang 300 meter per roll"
                                        placeholder="Spesifikasi teknis..."
                                        class="block w-full border-gray-300 bg-gray-50 rounded-md text-xs focus:ring-primary-500 focus:border-primary-500 text-gray-600">
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center gap-1">
                                        <input type="number" value="2"
                                            class="block w-full text-center border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                        <span class="text-xs text-gray-500">Roll</span>
                                    </div>
                                    <p class="text-[10px] text-orange-500 mt-1 font-medium text-center">Stok sisa: 0.2 Roll
                                    </p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                                            Rp</div>
                                        <input type="text" value="1.850.000"
                                            class="block w-full pl-8 border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 text-right">
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                                            Rp</div>
                                        <input type="text" value="3.700.000" readonly
                                            class="block w-full pl-8 border-transparent bg-gray-50 rounded-md text-sm text-gray-700 font-medium text-right focus:ring-0">
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top text-center">
                                    <button type="button" class="text-gray-400 hover:text-red-600 transition-colors mt-2"
                                        title="Hapus baris">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2: Generate dari Alat Rusak -->
                            <tr class="group bg-red-50/20">
                                <td class="px-4 py-3">
                                    <input type="text" value="Switch Hub 24 Port TP-Link"
                                        class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 mb-2">
                                    <input type="text" value="Gigabit Smart Managed Switch"
                                        placeholder="Spesifikasi teknis..."
                                        class="block w-full border-gray-300 bg-gray-50 rounded-md text-xs focus:ring-primary-500 focus:border-primary-500 text-gray-600">
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center gap-1">
                                        <input type="number" value="1"
                                            class="block w-full text-center border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                        <span class="text-xs text-gray-500">Unit</span>
                                    </div>
                                    <p class="text-[10px] text-red-500 mt-1 font-medium text-center">Rusak Total: 1 Unit
                                    </p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                                            Rp</div>
                                        <input type="text" value="1.200.000"
                                            class="block w-full pl-8 border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500 text-right">
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                                            Rp</div>
                                        <input type="text" value="1.200.000" readonly
                                            class="block w-full pl-8 border-transparent bg-gray-50 rounded-md text-sm text-gray-700 font-medium text-right focus:ring-0">
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top text-center">
                                    <button type="button" class="text-gray-400 hover:text-red-600 transition-colors mt-2"
                                        title="Hapus baris">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Total Estimasi -->
                <div class="flex justify-end mt-4">
                    <div class="w-full sm:w-1/3 bg-primary-50 border border-primary-100 rounded-lg p-4 text-right">
                        <p class="text-xs font-semibold text-primary-600 uppercase">Total Estimasi Anggaran</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rp 4.900.000</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Footer Action -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button"
                    class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    Simpan sebagai Draf
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Kirim Pengajuan ke Waka
                </button>
            </div>

        </form>
    </div>
@endsection
