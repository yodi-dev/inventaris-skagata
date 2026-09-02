@extends('layouts.admin')

@section('title', 'Persetujuan Pengadaan')
@section('header_title', 'Persetujuan Pengadaan (RAB)')

@section('content')
    <div class="space-y-6">
        <!-- Header & Filter Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Pengajuan RAB Masuk</h2>
                <p class="text-sm text-gray-500 mt-0.5">Tinjau, setujui, berikan catatan revisi, atau tolak pengadaan barang
                    dari tiap bengkel.</p>
            </div>

            <!-- Filter Status / Jurusan -->
            <div class="flex items-center gap-3">
                <select class="text-sm border-gray-300 rounded-lg focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Persetujuan</option>
                    <option value="revisi">Direvisi</option>
                    <option value="approved">Disetujui</option>
                </select>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4 font-medium">Tanggal / ID</th>
                            <th class="px-6 py-4 font-medium">Bengkel / Pemohon</th>
                            <th class="px-6 py-4 font-medium">Nama Barang & Spesifikasi</th>
                            <th class="px-6 py-4 font-medium">Qty & Est. Biaya</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">

                        <!-- Row 1: Status Pending -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900">02 Jun 2026</span>
                                <p class="text-xs text-gray-400">#RAB-2026-089</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-800">Teknik Komputer Jaringan</span>
                                <p class="text-xs text-gray-500">Toolman TKJ</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">Router Mikrotik RB951</p>
                                <p class="text-xs text-gray-500">Keperluan praktikum jaringan dasar siswa kelas XI</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-semibold text-gray-900">5 Unit</p>
                                <p class="text-xs text-gray-500">Rp 4.500.000</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Approve -->
                                    <button onclick="alert('Setujui pengadaan ini?')"
                                        class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors"
                                        title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <!-- Tombol Revisi -->
                                    <button onclick="alert('Buka modal catatan revisi')"
                                        class="p-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors"
                                        title="Revisi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <!-- Tombol Reject -->
                                    <button onclick="alert('Tolak pengadaan ini?')"
                                        class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                        title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2: Status Pending -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900">01 Jun 2026</span>
                                <p class="text-xs text-gray-400">#RAB-2026-088</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-800">Teknik Audio Video</span>
                                <p class="text-xs text-gray-500">Toolman TAV</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">Osiloskop Digital Storage</p>
                                <p class="text-xs text-gray-500">Pengganti alat ukur lab elektronik yang rusak berat</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-semibold text-gray-900">2 Unit</p>
                                <p class="text-xs text-gray-500">Rp 8.000.000</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors"
                                        title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button
                                        class="p-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors"
                                        title="Revisi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button
                                        class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                        title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 3: Status Approved (Contoh riwayat/sudah diproses) -->
                        <tr class="hover:bg-gray-50 transition-colors opacity-75">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900">28 May 2026</span>
                                <p class="text-xs text-gray-400">#RAB-2026-085</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-800">Tata Boga</span>
                                <p class="text-xs text-gray-500">Toolman Boga</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">Oven Listrik Heavy Duty</p>
                                <p class="text-xs text-gray-500">Penambahan fasilitas praktik kue</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-semibold text-gray-900">1 Unit</p>
                                <p class="text-xs text-gray-500">Rp 5.200.000</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                    Disetujui
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-xs text-gray-400 italic">Selesai</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-500">Menampilkan 3 dari 12 total pengajuan</p>
                <div class="flex gap-1">
                    <button px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50
                        disabled" disabled>Sebelumnya</button>
                    <button class="px-3 py-1 bg-primary-600 text-white rounded-lg text-xs font-medium">1</button>
                    <button
                        class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50">2</button>
                    <button
                        class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-100">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>
@endsection
