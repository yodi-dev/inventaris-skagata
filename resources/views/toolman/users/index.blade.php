@extends('layouts.admin')

@section('title', 'Manajemen Peminjam')
@section('header_title', 'Manajemen Akun Peminjam')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{
        activeTab: window.location.hash ? window.location.hash.replace('#', '') : '{{ request('tab', 'pending') }}',
        searchQuery: '',
        roleFilter: '',
        
        // State Modal Suspend
        showSuspendModal: false,
        selectedUser: null,
        suspendReason: 'merusak',
        suspendNotes: '',
        
        // State Modal Pulihkan
        showRestoreModal: false,
        restoreConfirmed: false,
        restoreNotes: '',

        // Toast Feedback
        toastMessage: '',
        showToast: false,
        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
        },

        openSuspend(user) {
            this.selectedUser = user;
            this.suspendReason = 'merusak';
            this.suspendNotes = '';
            this.showSuspendModal = true;
        },

        submitSuspend() {
            this.showSuspendModal = false;
            this.triggerToast(`Akun ${this.selectedUser.name} berhasil ditangguhkan (Suspend). Akses peminjaman dinonaktifkan.`);
        },

        openRestore(user) {
            this.selectedUser = user;
            this.restoreConfirmed = true;
            this.restoreNotes = '';
            this.showRestoreModal = true;
        },

        submitRestore() {
            this.showRestoreModal = false;
            this.triggerToast(`Akun ${this.selectedUser.name} berhasil dipulihkan! Peminjam dapat mengajukan pinjaman kembali.`);
        },

        approveUser(name) {
            this.triggerToast(`Pendaftaran ${name} disetujui! Akun kini aktif.`);
        },

        rejectUser(name) {
            this.triggerToast(`Pendaftaran ${name} ditolak.`);
        }
    }">

        <!-- Toast Notification Floating -->
        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 border border-gray-700 text-sm max-w-md"
            style="display: none;">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="toastMessage"></span>
        </div>

        <!-- Top Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center text-xs font-medium text-gray-500 mb-2 space-x-2">
                    <a href="/toolman/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Manajemen Peminjam</span>
                </nav>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Akun Peminjam</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola permohonan registrasi, status akun aktif, dan penegakan sanksi siswa & guru bengkel.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Bengkel TKJ Aktif
                </span>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Peminjam -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Peminjam</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">142</p>
                    <p class="text-xs text-gray-400 mt-1">Siswa & Guru TKJ</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 2: Menunggu Acc -->
            <div @click="activeTab = 'pending'"
                :class="activeTab === 'pending' ? 'ring-2 ring-amber-500 border-amber-300' : 'border-gray-200'"
                class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between cursor-pointer hover:border-amber-300 transition-all">
                <div>
                    <p class="text-xs font-medium text-amber-700 uppercase tracking-wider">Menunggu Approval</p>
                    <p class="text-2xl font-bold text-amber-900 mt-1">2</p>
                    <p class="text-xs text-amber-600 mt-1">Perlu diverifikasi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 3: Akun Aktif -->
            <div @click="activeTab = 'active'"
                :class="activeTab === 'active' ? 'ring-2 ring-primary-500 border-primary-300' : 'border-gray-200'"
                class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between cursor-pointer hover:border-primary-300 transition-all">
                <div>
                    <p class="text-xs font-medium text-primary-700 uppercase tracking-wider">Akun Aktif</p>
                    <p class="text-2xl font-bold text-primary-900 mt-1">137</p>
                    <p class="text-xs text-primary-600 mt-1">Bisa meminjam</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center text-primary-600 border border-primary-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 4: Ditangguhkan (Suspend) -->
            <div @click="activeTab = 'suspended'"
                :class="activeTab === 'suspended' ? 'ring-2 ring-red-500 border-red-300' : 'border-gray-200'"
                class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between cursor-pointer hover:border-red-300 transition-all">
                <div>
                    <p class="text-xs font-medium text-red-700 uppercase tracking-wider">Ditangguhkan (Suspend)</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">3</p>
                    <p class="text-xs text-red-600 mt-1">Pelanggaran / Sanksi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600 border border-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row gap-4 items-center justify-between">
            <!-- Search -->
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" x-model="searchQuery"
                    class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari nama, NISN/NIP, kelas...">
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select x-model="roleFilter"
                    class="block w-full sm:w-auto pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Peran</option>
                    <option value="Siswa">Siswa</option>
                    <option value="Guru">Guru / Instruktur</option>
                    <option value="Magang">Mahasiswa Magang / PPL</option>
                </select>

                <select
                    class="block w-full sm:w-auto pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Tingkat</option>
                    <option value="X">Kelas X TKJ</option>
                    <option value="XI">Kelas XI TKJ</option>
                    <option value="XII">Kelas XII TKJ</option>
                </select>
            </div>
        </div>

        <!-- Tabbed Container -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            
            <!-- Tabs Header -->
            <div class="border-b border-gray-200 px-6 pt-4 bg-gray-50/50">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <!-- Tab 1: Menunggu Acc -->
                    <button type="button" @click="activeTab = 'pending'; window.location.hash = 'pending'"
                        :class="activeTab === 'pending'
                            ? 'border-primary-600 text-primary-700 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                        class="whitespace-nowrap py-3.5 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                        <span>Menunggu Approval</span>
                        <span :class="activeTab === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600'"
                            class="px-2 py-0.5 rounded-full text-xs font-semibold">2</span>
                    </button>

                    <!-- Tab 2: Akun Aktif -->
                    <button type="button" @click="activeTab = 'active'; window.location.hash = 'active'"
                        :class="activeTab === 'active'
                            ? 'border-primary-600 text-primary-700 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                        class="whitespace-nowrap py-3.5 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                        <span>Akun Aktif</span>
                        <span :class="activeTab === 'active' ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-600'"
                            class="px-2 py-0.5 rounded-full text-xs font-semibold">137</span>
                    </button>

                    <!-- Tab 3: Ditangguhkan (Suspend) -->
                    <button type="button" @click="activeTab = 'suspended'; window.location.hash = 'suspended'"
                        :class="activeTab === 'suspended'
                            ? 'border-red-600 text-red-700 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                        class="whitespace-nowrap py-3.5 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                        <span>Ditangguhkan (Suspend)</span>
                        <span :class="activeTab === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600'"
                            class="px-2 py-0.5 rounded-full text-xs font-semibold">3</span>
                    </button>
                </nav>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 1: MENUNGGU ACC (APPROVAL REGISTRASI)                      -->
            <!-- ============================================================== -->
            <div x-show="activeTab === 'pending'" class="divide-y divide-gray-100">
                
                <!-- Info Banner -->
                <div class="p-4 bg-amber-50/70 border-b border-amber-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        <strong>Aturan PRD Sibenka:</strong> Siswa dan guru yang baru mendaftar memiliki status <span class="font-semibold text-amber-800">"Menunggu Acc"</span>. Mereka belum dapat login atau membuat tiket peminjaman sampai disetujui oleh Toolman bengkel.
                    </p>
                </div>

                <!-- Table Menunggu Acc -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-xs uppercase text-gray-500 tracking-wider">
                                <th class="px-6 py-4 font-semibold">Calon Peminjam</th>
                                <th class="px-6 py-4 font-semibold">Peran & Identitas</th>
                                <th class="px-6 py-4 font-semibold">Waktu Daftar</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Tindakan Approval</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <!-- Pending Row 1 -->
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-700 font-bold text-sm flex items-center justify-center border border-blue-200">
                                            D
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Dimas Aditya</p>
                                            <p class="text-xs text-gray-500 mt-0.5">dimas.aditya@siswa.smkn3.sch.id</p>
                                            <p class="text-[11px] text-gray-400">WA: 0812-3456-7890</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 mb-1">
                                        Siswa
                                    </span>
                                    <p class="text-sm font-medium text-gray-900">Kelas X TKJ 2</p>
                                    <p class="text-xs text-gray-500 font-mono">NISN: 0078912345</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <p class="font-medium text-gray-900">Hari ini</p>
                                    <p class="text-xs text-gray-500">09:15 WIB</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-ping"></span>
                                        Menunggu Acc
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" @click="rejectUser('Dimas Aditya')"
                                        class="px-3.5 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold rounded-lg transition-colors">
                                        Tolak
                                    </button>
                                    <button type="button" @click="approveUser('Dimas Aditya')"
                                        class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                        Setujui Akun
                                    </button>
                                </td>
                            </tr>

                            <!-- Pending Row 2 -->
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm flex items-center justify-center border border-emerald-200">
                                            R
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Reza Pratama, S.Kom.</p>
                                            <p class="text-xs text-gray-500 mt-0.5">reza.ppl@univ.edu</p>
                                            <p class="text-[11px] text-gray-400">WA: 0857-9988-1122</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 mb-1">
                                        Guru PPL / Magang
                                    </span>
                                    <p class="text-sm font-medium text-gray-900">Mapel Informatika</p>
                                    <p class="text-xs text-gray-500 font-mono">NIM: 2201019940</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <p class="font-medium text-gray-900">Kemarin</p>
                                    <p class="text-xs text-gray-500">14:30 WIB</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Menunggu Acc
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" @click="rejectUser('Reza Pratama')"
                                        class="px-3.5 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold rounded-lg transition-colors">
                                        Tolak
                                    </button>
                                    <button type="button" @click="approveUser('Reza Pratama')"
                                        class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                        Setujui Akun
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Counter -->
                <div class="px-6 py-3.5 bg-gray-50 flex items-center justify-between text-xs text-gray-500">
                    <span>Menampilkan 2 calon peminjam menunggu persetujuan</span>
                    <span>Approval otomatis memberikan hak akses login & katalog</span>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: AKUN AKTIF (PENGGUNA NORMAL DENGAN FITUR SUSPEND)       -->
            <!-- ============================================================== -->
            <div x-show="activeTab === 'active'" style="display: none;" class="divide-y divide-gray-100">
                
                <!-- Info Banner -->
                <div class="p-4 bg-emerald-50/60 border-b border-emerald-100 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <div class="text-xs text-emerald-900 leading-relaxed">
                            <strong>Status Aktif:</strong> Peminjam dalam daftar ini berhak meminjam alat dan mengajukan bahan praktik. Jika siswa melakukan pelanggaran (merusak/menghilangkan alat atau sering terlambat), Toolman dapat <strong>menangguhkan (Suspend)</strong> akun melalui tombol aksi.
                        </div>
                    </div>
                </div>

                <!-- Table Akun Aktif -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-xs uppercase text-gray-500 tracking-wider">
                                <th class="px-6 py-4 font-semibold">Profil Peminjam</th>
                                <th class="px-6 py-4 font-semibold">Peran & Kelas</th>
                                <th class="px-6 py-4 font-semibold">Tanggungan Peminjaman</th>
                                <th class="px-6 py-4 font-semibold">Terdaftar Sejak</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Aksi Toolman</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">

                            <!-- Active Row 1 (Sedang Pinjam Alat) -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-700 font-bold text-sm flex items-center justify-center border border-blue-200">
                                            B
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Budi Santoso</p>
                                            <p class="text-xs text-gray-500 mt-0.5">budi.santoso@siswa.smkn3.sch.id</p>
                                            <p class="text-[11px] text-gray-400">NISN: 0061234567</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 mb-1">
                                        Siswa
                                    </span>
                                    <p class="text-sm font-medium text-gray-900">XI TKJ 1</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Pinjam 1 Unit (Router RB951)
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    10 Jan 2026
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" @click="openSuspend({ name: 'Budi Santoso', role: 'Siswa XI TKJ 1', email: 'budi.santoso@siswa.smkn3.sch.id' })"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 hover:bg-red-50 text-red-700 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                                        title="Tangguhkan Akun Siswa">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Suspend
                                    </button>
                                </td>
                            </tr>

                            <!-- Active Row 2 (Bebas Tanggungan) -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 text-purple-700 font-bold text-sm flex items-center justify-center border border-purple-200">
                                            S
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Siti Nurhaliza</p>
                                            <p class="text-xs text-gray-500 mt-0.5">siti.nurhaliza@siswa.smkn3.sch.id</p>
                                            <p class="text-[11px] text-gray-400">NISN: 0058912344</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 mb-1">
                                        Siswa
                                    </span>
                                    <p class="text-sm font-medium text-gray-900">XII TKJ 2</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                        Bebas Tanggungan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    15 Agu 2025
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" @click="openSuspend({ name: 'Siti Nurhaliza', role: 'Siswa XII TKJ 2', email: 'siti.nurhaliza@siswa.smkn3.sch.id' })"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 hover:bg-red-50 text-red-700 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                                        title="Tangguhkan Akun Siswa">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Suspend
                                    </button>
                                </td>
                            </tr>

                            <!-- Active Row 3 (Guru) -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center border border-indigo-200">
                                            Y
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Pak Yono, S.T.</p>
                                            <p class="text-xs text-gray-500 mt-0.5">yono@smkn3.sch.id</p>
                                            <p class="text-[11px] text-gray-400">NIP: 198205142010011003</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800 mb-1">
                                        Guru
                                    </span>
                                    <p class="text-sm font-medium text-gray-900">Produktif TKJ</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                        Bebas Tanggungan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    12 Jan 2026
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" @click="openSuspend({ name: 'Pak Yono, S.T.', role: 'Guru Produktif TKJ', email: 'yono@smkn3.sch.id' })"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 hover:bg-red-50 text-red-700 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                                        title="Tangguhkan Akun">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Suspend
                                    </button>
                                </td>
                            </tr>

                            <!-- Active Row 4 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-amber-100 text-amber-700 font-bold text-sm flex items-center justify-center border border-amber-200">
                                            F
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Fajar Ramadhan</p>
                                            <p class="text-xs text-gray-500 mt-0.5">fajar.ramadhan@siswa.smkn3.sch.id</p>
                                            <p class="text-[11px] text-gray-400">NISN: 0071122334</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 mb-1">
                                        Siswa
                                    </span>
                                    <p class="text-sm font-medium text-gray-900">X TKJ 1</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                        Bebas Tanggungan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    01 Feb 2026
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" @click="openSuspend({ name: 'Fajar Ramadhan', role: 'Siswa X TKJ 1', email: 'fajar.ramadhan@siswa.smkn3.sch.id' })"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 hover:bg-red-50 text-red-700 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                                        title="Tangguhkan Akun Siswa">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Suspend
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination Mockup -->
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Menampilkan 4 dari 137 akun aktif</span>
                    <div class="flex space-x-1">
                        <button class="px-2.5 py-1 text-xs border border-gray-300 rounded bg-white text-gray-600">Prev</button>
                        <button class="px-2.5 py-1 text-xs border border-primary-600 rounded bg-primary-50 text-primary-700 font-bold">1</button>
                        <button class="px-2.5 py-1 text-xs border border-gray-300 rounded bg-white text-gray-600">2</button>
                        <button class="px-2.5 py-1 text-xs border border-gray-300 rounded bg-white text-gray-600">Next</button>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: AKUN DITANGGUHKAN / SUSPEND (SANKS & GANTI RUGI)       -->
            <!-- ============================================================== -->
            <div x-show="activeTab === 'suspended'" style="display: none;" class="divide-y divide-gray-100">
                
                <!-- Rule Sanksi Warning Box (PRD Bab 3) -->
                <div class="p-4 bg-red-50 border-b border-red-200 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-xs text-red-900 leading-relaxed">
                        <p class="font-bold text-red-950">Aturan Sanksi & Penalti Sibenka:</p>
                        <p class="mt-0.5">
                            Jika siswa merusak atau menghilangkan alat bengkel, atau sering terlambat, Toolman menangguhkan (Suspend) akun. Urusan ganti rugi diselesaikan secara langsung di luar sistem aplikasi. Siswa yang disuspend <strong>tidak dapat mengajukan tiket peminjaman baru</strong> sampai akun dipulihkan oleh Toolman.
                        </p>
                    </div>
                </div>

                <!-- Table Akun Suspend -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-xs uppercase text-gray-500 tracking-wider">
                                <th class="px-6 py-4 font-semibold">Profil Pelanggar</th>
                                <th class="px-6 py-4 font-semibold">Tgl Disuspend</th>
                                <th class="px-6 py-4 font-semibold">Alasan Sanksi / Pelanggaran</th>
                                <th class="px-6 py-4 font-semibold">Status Ganti Rugi Fisik</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Pemulihan Akun</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">

                            <!-- Suspended Row 1: Merusak Alat -->
                            <tr class="bg-red-50/20 hover:bg-red-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-red-100 text-red-700 font-bold text-sm flex items-center justify-center border border-red-200">
                                            A
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Andi Saputra</p>
                                            <p class="text-xs text-gray-500 mt-0.5">andi.spt@siswa.smkn3.sch.id</p>
                                            <p class="text-xs font-semibold text-gray-700 mt-0.5">Kelas XII TKJ 1 &bull; NISN: 0051122998</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    <p class="font-semibold text-gray-900">28 Feb 2026</p>
                                    <p class="text-gray-400">4 hari lalu</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                            Merusak Alat Praktik
                                        </span>
                                        <p class="text-xs text-gray-700">Port LAN Router Mikrotik patah / terbakar akibat korsleting tegangan tinggi.</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="bg-white p-2.5 rounded-lg border border-red-200 text-xs text-gray-700 space-y-1">
                                        <p class="font-medium text-red-800">&bull; Menunggu ganti biaya servis adaptor</p>
                                        <p class="text-[11px] text-gray-500">Koordinasi dengan Wali Kelas & Instruktur</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                        Ditangguhkan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button type="button" @click="openRestore({ name: 'Andi Saputra', reason: 'Merusak Alat Praktik', item: 'Router Mikrotik RB951' })"
                                        class="inline-flex items-center px-3.5 py-1.5 bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Pulihkan Akun
                                    </button>
                                </td>
                            </tr>

                            <!-- Suspended Row 2: Menghilangkan Alat -->
                            <tr class="bg-red-50/20 hover:bg-red-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-red-100 text-red-700 font-bold text-sm flex items-center justify-center border border-red-200">
                                            R
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Rian Pratama</p>
                                            <p class="text-xs text-gray-500 mt-0.5">rian.pratama@siswa.smkn3.sch.id</p>
                                            <p class="text-xs font-semibold text-gray-700 mt-0.5">Kelas XI TKJ 2 &bull; NISN: 0064433221</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    <p class="font-semibold text-gray-900">20 Feb 2026</p>
                                    <p class="text-gray-400">12 hari lalu</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                            Menghilangkan Alat
                                        </span>
                                        <p class="text-xs text-gray-700">Tang Crimping RJ45 hilang setelah praktik instalasi kabel jaringan LAN.</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="bg-white p-2.5 rounded-lg border border-red-200 text-xs text-gray-700 space-y-1">
                                        <p class="font-medium text-red-800">&bull; Wajib mengganti 1 unit Crimper baru</p>
                                        <p class="text-[11px] text-gray-500">Batas penggantian s/d 10 Maret 2026</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                        Ditangguhkan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button type="button" @click="openRestore({ name: 'Rian Pratama', reason: 'Menghilangkan Alat', item: 'Tang Crimping RJ45' })"
                                        class="inline-flex items-center px-3.5 py-1.5 bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Pulihkan Akun
                                    </button>
                                </td>
                            </tr>

                            <!-- Suspended Row 3: Terlambat Berulang -->
                            <tr class="bg-red-50/20 hover:bg-red-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-red-100 text-red-700 font-bold text-sm flex items-center justify-center border border-red-200">
                                            D
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Doni Kusuma</p>
                                            <p class="text-xs text-gray-500 mt-0.5">doni.kusuma@siswa.smkn3.sch.id</p>
                                            <p class="text-xs font-semibold text-gray-700 mt-0.5">Kelas X TKJ 2 &bull; NISN: 0079988776</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    <p class="font-semibold text-gray-900">15 Feb 2026</p>
                                    <p class="text-gray-400">17 hari lalu</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Keterlambatan > 3 Kali
                                        </span>
                                        <p class="text-xs text-gray-700">Tidak mengembalikan alat di hari yang sama selama 3 tiket berturut-turut.</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="bg-white p-2.5 rounded-lg border border-red-200 text-xs text-gray-700 space-y-1">
                                        <p class="font-medium text-amber-800">&bull; Skorsing peminjaman alat selama 2 minggu</p>
                                        <p class="text-[11px] text-gray-500">Masa skorsing telah terlewati</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                        Ditangguhkan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button type="button" @click="openRestore({ name: 'Doni Kusuma', reason: 'Keterlambatan Berulang', item: 'Kabel Tester' })"
                                        class="inline-flex items-center px-3.5 py-1.5 bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Pulihkan Akun
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Footer Counter -->
                <div class="px-6 py-3.5 bg-gray-50 flex items-center justify-between text-xs text-gray-500">
                    <span>3 peminjam dalam status sanksi penangguhan</span>
                    <span>Pemulihan akun mengaktifkan kembali akses peminjaman di katalog</span>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- MODAL: TANGGUHKAN AKUN (SUSPEND MODAL)                         -->
        <!-- ============================================================== -->
        <div x-show="showSuspendModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/60" @click="showSuspendModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <!-- Modal Panel -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-red-100">
                    
                    <div class="p-6">
                        <!-- Icon Header -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Tangguhkan Akun Peminjam (Suspend)</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Akun akan dibekukan dan peminjam tidak dapat membuat pengajuan alat/bahan baru sampai sanksi dicabut.
                                </p>
                            </div>
                        </div>

                        <!-- Target User Card -->
                        <div class="mt-4 p-3.5 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Target Akun</p>
                                <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="selectedUser ? selectedUser.name : ''"></p>
                                <p class="text-xs text-gray-500" x-text="selectedUser ? selectedUser.role : ''"></p>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded bg-red-100 text-red-700">
                                Status &rarr; Suspend
                            </span>
                        </div>

                        <!-- Form Options -->
                        <div class="mt-5 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                                    Alasan Pelanggaran <span class="text-red-500">*</span>
                                </label>
                                <select x-model="suspendReason"
                                    class="block w-full text-sm rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 shadow-sm">
                                    <option value="merusak">Merusak Alat Praktik Bengkel</option>
                                    <option value="hilang">Menghilangkan Alat / Komponen</option>
                                    <option value="terlambat">Keterlambatan Pengembalian Berulang (> 3 Kali)</option>
                                    <option value="tatatertib">Melanggar Tata Tertib / Keamanan Bengkel</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                                    Catatan Sanksi & Kesepakatan Ganti Rugi
                                </label>
                                <textarea x-model="suspendNotes" rows="3"
                                    class="block w-full text-sm rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 shadow-sm"
                                    placeholder="Contoh: Siswa wajib mengganti adaptor router yang terbakar ke ruang teknisi sebelum akun dipulihkan..."></textarea>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Sesuai PRD: Urusan ganti rugi diselesaikan secara langsung di luar sistem aplikasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                        <button type="button" @click="showSuspendModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            Batal
                        </button>
                        <button type="button" @click="submitSuspend()"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                            Tangguhkan Akun
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL: PULIHKAN AKUN (RESTORE / UNSUSPEND MODAL)               -->
        <!-- ============================================================== -->
        <div x-show="showRestoreModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60" @click="showRestoreModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-emerald-100">
                    
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Pulihkan Akun Peminjam</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Aktifkan kembali akses peminjaman setelah siswa menyelesaikan urusan sanksi/ganti rugi.
                                </p>
                            </div>
                        </div>

                        <!-- Target User Card -->
                        <div class="mt-4 p-3.5 bg-emerald-50/50 rounded-xl border border-emerald-200">
                            <p class="text-xs font-semibold text-emerald-800 uppercase">Peminjam yang Dipulihkan</p>
                            <p class="text-base font-bold text-gray-900 mt-0.5" x-text="selectedUser ? selectedUser.name : ''"></p>
                            <p class="text-xs text-gray-600 mt-1">
                                Pelanggaran sebelumnya: <span class="font-medium text-red-600" x-text="selectedUser ? selectedUser.reason : ''"></span>
                            </p>
                        </div>

                        <!-- Konfirmasi Checklist -->
                        <div class="mt-5 space-y-3">
                            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100/60 transition-colors">
                                <input type="checkbox" x-model="restoreConfirmed"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mt-0.5">
                                <span class="text-xs text-gray-700 leading-relaxed">
                                    Saya mengonfirmasi bahwa urusan ganti rugi fisik, penggantian alat, atau masa skorsing telah <strong>selesai diselesaikan</strong> di luar sistem.
                                </span>
                            </label>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Pemulihan (Opsional)</label>
                                <input type="text" x-model="restoreNotes"
                                    class="block w-full text-xs rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Contoh: Unit pengganti sudah diserahkan ke lemari alat.">
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                        <button type="button" @click="showRestoreModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            Batal
                        </button>
                        <button type="button" @click="submitRestore()" :disabled="!restoreConfirmed"
                            :class="restoreConfirmed ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="px-5 py-2 text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Aktifkan Akun Kembali
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
