@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-slate-50 p-6">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Laporan Konsumsi Bahan</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau penggunaan barang habis pakai (BHP) di setiap bengkel/jurusan.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-green-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Export Excel
                </button>
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <!-- Filter Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date"
                        class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all">
                </div>

                <!-- Filter Tanggal Akhir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date"
                        class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all">
                </div>

                <!-- Filter Bengkel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bengkel / Jurusan</label>
                    <select name="bengkel"
                        class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all bg-white">
                        <option value="">Semua Bengkel</option>
                        <option value="tkj">Teknik Komputer Jaringan</option>
                        <option value="tkr">Teknik Kendaraan Ringan</option>
                        <option value="av">Audio Video</option>
                    </select>
                </div>

                <!-- Tombol Terapkan -->
                <div>
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">Barang & Kode</th>
                            <th class="py-3 px-4 font-semibold">Bengkel</th>
                            <th class="py-3 px-4 font-semibold text-center">Jumlah Dipakai</th>
                            <th class="py-3 px-4 font-semibold">Keterangan / Tujuan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">

                        <!-- Row 1: Meteran (Kabel) -->
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap">15 Okt 2023</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">Kabel UTP Cat 6</div>
                                <div class="text-xs text-gray-500">BHP-TKJ-021</div>
                            </td>
                            <td class="py-3 px-4">TKJ</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4">
                                        </path>
                                    </svg>
                                    50 Meter
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-gray-900">Ujian Praktek Jaringan (Kelas XII)</div>
                                <div class="text-xs text-gray-500">Oleh: Bpk. Budi Santoso (Guru)</div>
                            </td>
                        </tr>

                        <!-- Row 2: Liter (Oli) -->
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap">14 Okt 2023</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">Oli Mesin 10W-40</div>
                                <div class="text-xs text-gray-500">BHP-TKR-105</div>
                            </td>
                            <td class="py-3 px-4">TKR</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4">
                                        </path>
                                    </svg>
                                    4 Liter
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-gray-900">Praktek Servis Berkala</div>
                                <div class="text-xs text-gray-500">Oleh: Siswa Kelompok 2</div>
                            </td>
                        </tr>

                        <!-- Row 3: Pcs / Roll (Timah) -->
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap">12 Okt 2023</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">Timah Solder 0.8mm</div>
                                <div class="text-xs text-gray-500">BHP-AV-044</div>
                            </td>
                            <td class="py-3 px-4">Audio Video</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M20 12H4"></path>
                                    </svg>
                                    2 Roll
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-gray-900">Praktek Merakit Amplifier</div>
                                <div class="text-xs text-gray-500">Oleh: Ibu Siti (Toolman)</div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium">3</span> dari
                            <span class="font-medium">45</span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-green-50 text-green-700 text-sm font-medium">
                                1
                            </a>
                            <a href="#"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50">
                                2
                            </a>
                            <a href="#"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Next
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
