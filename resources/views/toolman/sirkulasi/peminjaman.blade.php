@extends('layouts.admin')

@section('title', 'Persetujuan Peminjaman')
@section('header_title', 'Sirkulasi - Persetujuan Peminjaman')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header & Tabs -->
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Antrean Persetujuan</h3>
                <p class="text-sm text-gray-500 mt-1">Cek dan verifikasi pengajuan alat/bahan dari siswa dan guru.</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <a href="#"
                        class="border-primary-500 text-primary-600 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Menunggu Persetujuan (3)
                    </a>
                    <a href="#"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                        Riwayat Persetujuan
                    </a>
                </nav>
            </div>
        </div>

        <!-- List of Request Tickets -->
        <div class="space-y-5">

            <!-- TICKET 1: Siswa meminjam Alat Inventaris -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <!-- Ticket Header -->
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
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Menunggu Acc
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Diajukan: 10 menit yang lalu</p>
                    </div>
                </div>

                <!-- Ticket Body -->
                <div class="px-5 py-4">
                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Rincian Permintaan:</h5>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Nama Barang</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600">Jml Diminta</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600">Stok Tersedia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 font-medium">Crimping Tool RJ45 <span
                                            class="text-xs text-gray-500 font-normal ml-1">(Inventaris)</span></td>
                                    <td class="px-4 py-2 text-center font-bold text-gray-900">1 Unit</td>
                                    <td class="px-4 py-2 text-center text-green-600 font-medium">5 Unit</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 font-medium">LAN Tester <span
                                            class="text-xs text-gray-500 font-normal ml-1">(Inventaris)</span></td>
                                    <td class="px-4 py-2 text-center font-bold text-gray-900">1 Unit</td>
                                    <td class="px-4 py-2 text-center text-green-600 font-medium">3 Unit</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Waktu & Catatan -->
                    <div
                        class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500">Jadwal Pinjam - Kembali</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">Hari ini, 08:00 WIB &mdash; Besok, 14:00 WIB
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tujuan Penggunaan</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">Praktik Jaringan Dasar Pak Yono</p>
                        </div>
                    </div>
                </div>

                <!-- Ticket Footer (Actions) -->
                <div class="px-5 py-4 border-t border-gray-200 bg-white flex justify-end gap-3">
                    <button
                        class="px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Tolak Pengajuan
                    </button>
                    <button
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Approve & Serahkan
                    </button>
                </div>
            </div>

            <!-- TICKET 2: Guru meminjam Alat & Bahan Habis Pakai -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <!-- Ticket Header -->
                <div
                    class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div class="flex items-center space-x-3">
                        <div
                            class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                            P
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Pak Yono</h4>
                            <p class="text-xs text-gray-500">Guru - Produktif TKJ</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Menunggu Acc
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Diajukan: 45 menit yang lalu</p>
                    </div>
                </div>

                <!-- Ticket Body -->
                <div class="px-5 py-4">
                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Rincian Permintaan:</h5>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Nama Barang</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600">Jml Diminta</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600">Stok Tersedia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 font-medium">Proyektor Epson <span
                                            class="text-xs text-gray-500 font-normal ml-1">(Inventaris)</span></td>
                                    <td class="px-4 py-2 text-center font-bold text-gray-900">1 Unit</td>
                                    <td class="px-4 py-2 text-center text-green-600 font-medium">2 Unit</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 font-medium">Kabel UTP Cat6 <span
                                            class="text-xs text-orange-500 font-normal ml-1">(Bahan Habis Pakai)</span></td>
                                    <td class="px-4 py-2 text-center font-bold text-gray-900">10 Meter</td>
                                    <!-- Simulasi warning stok mepet -->
                                    <td class="px-4 py-2 text-center text-orange-500 font-bold">12 Meter</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Waktu & Catatan -->
                    <div
                        class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500">Jadwal Pinjam - Kembali</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">
                                Hari ini, 09:00 WIB &mdash; Hari ini, 15:00 WIB
                                <br><span class="text-xs text-orange-600 font-medium">*Bahan habis pakai tidak
                                    dikembalikan</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tujuan Penggunaan</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">Mengajar kelas XI TKJ 1 dan demo kabel</p>
                        </div>
                    </div>
                </div>

                <!-- Ticket Footer (Actions) -->
                <div class="px-5 py-4 border-t border-gray-200 bg-white flex justify-end gap-3">
                    <button
                        class="px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Tolak Pengajuan
                    </button>
                    <button
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Approve & Serahkan
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection
