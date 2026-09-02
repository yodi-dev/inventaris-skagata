@extends('layouts.admin')

@section('title', 'Manajemen Peminjam')
@section('header_title', 'Manajemen Akun Peminjam')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Daftar Pengguna Bengkel</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola akses siswa, guru, dan mahasiswa magang ke sistem inventaris.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari nama atau kelas...">
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white border-b border-gray-200 px-4 pt-4 rounded-t-xl">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                <a href="#"
                    class="border-primary-500 text-primary-600 whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-sm">
                    Menunggu Acc (2)
                </a>
                <a href="#"
                    class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                    Akun Aktif
                </a>
                <a href="#"
                    class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                    Ditangguhkan / Suspend (1)
                </a>
            </nav>
        </div>

        <!-- User Table -->
        <div class="bg-white border border-gray-200 rounded-b-xl shadow-sm overflow-hidden -mt-6 border-t-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-6 py-4 font-semibold">Profil Pengguna</th>
                            <th class="px-6 py-4 font-semibold">Peran & Kelas</th>
                            <th class="px-6 py-4 font-semibold">Tanggal Daftar</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">

                        <!-- Dummy Row 1: Menunggu Acc (Siswa Baru) -->
                        <tr class="hover:bg-yellow-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                        D
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Dimas Aditya</p>
                                        <p class="text-xs text-gray-500 mt-0.5">dimas.aditya@siswa.smkn3.sch.id</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 font-medium">Siswa</p>
                                <p class="text-xs text-gray-500 mt-0.5">X TKJ 2</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Hari ini, 09:15 WIB</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    Menunggu Acc
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button
                                    class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium rounded-md transition-colors"
                                    title="Tolak">Tolak</button>
                                <button
                                    class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors"
                                    title="Setujui">Approve</button>
                            </td>
                        </tr>

                        <!-- Dummy Row 2: Menunggu Acc (Guru PPL) -->
                        <tr class="hover:bg-yellow-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                        R
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Reza Pratama</p>
                                        <p class="text-xs text-gray-500 mt-0.5">reza.ppl@univ.edu</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 font-medium">Guru PPL</p>
                                <p class="text-xs text-gray-500 mt-0.5">Mapel Informatika</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Kemarin, 14:30 WIB</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    Menunggu Acc
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button
                                    class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium rounded-md transition-colors">Tolak</button>
                                <button
                                    class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors">Approve</button>
                            </td>
                        </tr>

                        <!-- Dummy Row 3: Akun Aktif -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-xs">
                                        Y
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Pak Yono</p>
                                        <p class="text-xs text-gray-500 mt-0.5">yono@smkn3.sch.id</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 font-medium">Guru</p>
                                <p class="text-xs text-gray-500 mt-0.5">Produktif TKJ</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">12 Jan 2026</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <!-- Dropdown action (simulated) -->
                                <button
                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-md hover:bg-gray-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <!-- Dummy Row 4: Akun Suspend -->
                        <tr class="bg-red-50/20 hover:bg-red-50/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold text-xs">
                                        A
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Andi Saputra</p>
                                        <p class="text-xs text-gray-500 mt-0.5">andi.spt@siswa.smkn3.sch.id</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 font-medium">Siswa</p>
                                <p class="text-xs text-gray-500 mt-0.5">XII RPL 2</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">05 Feb 2026</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Disuspend
                                </span>
                                <p class="text-[10px] text-red-500 mt-1 text-center">Alasan: Sering merusak alat</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium rounded-md transition-colors shadow-sm">
                                    Pulihkan Akun
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
