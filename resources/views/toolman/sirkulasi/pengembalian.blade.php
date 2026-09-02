@extends('layouts.admin')

@section('title', 'Pengembalian Barang')
@section('header_title', 'Sirkulasi - Cek Fisik & Pengembalian')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header & Search -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Pengecekan Pengembalian</h3>
                <p class="text-sm text-gray-500 mt-1">Periksa kondisi fisik alat sebelum menyelesaikan transaksi.</p>
            </div>

            <!-- Search Bar untuk mencari tiket/nama siswa -->
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari nama peminjam...">
            </div>
        </div>

        <!-- List of Active Borrowings (Menunggu Dikembalikan) -->
        <div class="space-y-6">

            <!-- KARTU PENGEMBALIAN 1 (Status Normal/Tepat Waktu) -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <!-- Header Kartu -->
                <div
                    class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div class="flex items-center space-x-3">
                        <div
                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                            B
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Budi Santoso</h4>
                            <p class="text-xs text-gray-500">Siswa - XI TKJ 1</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Sedang Dipinjam
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Batas Kembali: Hari ini, 14:00 WIB</p>
                    </div>
                </div>

                <!-- Body Kartu: Form Pengecekan -->
                <div class="p-5">
                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Form Pengecekan Fisik:
                    </h5>

                    <div class="border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Alat Inventaris</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 w-32">Jumlah</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 w-48">Kondisi Fisik</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Catatan (Opsional)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 font-medium">
                                        Crimping Tool RJ45
                                        <div class="text-xs text-gray-500 font-normal mt-0.5">INV-TKJ-012</div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-900">1 Unit</td>
                                    <td class="px-4 py-3">
                                        <select
                                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                            <option value="baik" selected>✅ Kondisi Baik</option>
                                            <option value="rusak">⚠️ Rusak/Cacat</option>
                                            <option value="hilang">❌ Hilang</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" placeholder="Aman..."
                                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 font-medium">
                                        LAN Tester
                                        <div class="text-xs text-gray-500 font-normal mt-0.5">INV-TKJ-008</div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-900">1 Unit</td>
                                    <td class="px-4 py-3">
                                        <select
                                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                            <option value="baik" selected>✅ Kondisi Baik</option>
                                            <option value="rusak">⚠️ Rusak/Cacat</option>
                                            <option value="hilang">❌ Hilang</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" placeholder="Tambahkan catatan jika rusak..."
                                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Kartu: Action -->
                <div class="px-5 py-4 border-t border-gray-200 bg-white flex justify-end gap-3">
                    <button
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Konfirmasi Pengembalian
                    </button>
                </div>
            </div>

            <!-- KARTU PENGEMBALIAN 2 (Contoh Keterlambatan) -->
            <div class="bg-white border border-red-200 rounded-xl shadow-sm overflow-hidden">
                <!-- Header Kartu dengan aksen merah karena terlambat -->
                <div
                    class="px-5 py-3 border-b border-red-200 bg-red-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div class="flex items-center space-x-3">
                        <div
                            class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold">
                            A
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Andi Saputra</h4>
                            <p class="text-xs text-gray-600">Siswa - XII RPL 2</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                            Terlambat
                        </span>
                        <p class="text-xs text-red-600 font-medium mt-1">Batas Kembali: Kemarin, 15:00 WIB</p>
                    </div>
                </div>

                <!-- Body Kartu: Form Pengecekan -->
                <div class="p-5">
                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Form Pengecekan Fisik:
                    </h5>

                    <div class="border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Alat Inventaris</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 w-32">Jumlah</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 w-48">Kondisi Fisik</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Catatan (Opsional)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 font-medium">
                                        Kabel HDMI 10 Meter
                                        <div class="text-xs text-gray-500 font-normal mt-0.5">INV-RPL-033</div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-900">1 Unit</td>
                                    <td class="px-4 py-3">
                                        <select
                                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                            <option value="baik">✅ Kondisi Baik</option>
                                            <!-- Simulasi barang rusak karena telat -->
                                            <option value="rusak" selected>⚠️ Rusak/Cacat</option>
                                            <option value="hilang">❌ Hilang</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" value="Ujung kabel bengkok"
                                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Kartu: Action -->
                <div class="px-5 py-4 border-t border-gray-200 bg-white flex justify-end gap-3">
                    <button
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Konfirmasi Pengembalian
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection
