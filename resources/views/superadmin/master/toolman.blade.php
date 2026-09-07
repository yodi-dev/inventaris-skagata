@extends('layouts.admin')

@section('content')
    <div x-data="{
        showDeleteModal: false,
        deleteNama: '',
        deleteBengkel: '',
        deleteEmail: '',
        openDelete(nama, bengkel, email) {
            this.deleteNama = nama;
            this.deleteBengkel = bengkel;
            this.deleteEmail = email;
            this.showDeleteModal = true;
        },
        confirmDelete() {
            alert('✅ Sukses (Prototipe UI):\nAkun toolman &quot;' + this.deleteNama + '&quot; (' + this.deleteBengkel + ') berhasil disimulasikan dihapus!');
            this.showDeleteModal = false;
        },

        // State Modal Reset Password
        showResetModal: false,
        resetNama: '',
        resetBengkel: '',
        resetEmail: '',
        newPassword: '',
        newPasswordConfirmation: '',
        showNewPassword: false,
        openReset(nama, bengkel, email) {
            this.resetNama = nama;
            this.resetBengkel = bengkel;
            this.resetEmail = email;
            this.newPassword = '';
            this.newPasswordConfirmation = '';
            this.showNewPassword = false;
            this.showResetModal = true;
        },
        autoGeneratePass() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
            let pass = '';
            for(let i = 0; i < 10; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            this.newPassword = pass;
            this.newPasswordConfirmation = pass;
            this.showNewPassword = true;
        },
        confirmReset() {
            if (!this.newPassword) {
                alert('⚠️ Perhatian: Silakan isi password baru atau gunakan tombol Acak Password!');
                return;
            }
            if (this.newPassword !== this.newPasswordConfirmation) {
                alert('⚠️ Perhatian: Konfirmasi password tidak cocok dengan password baru yang dimasukkan.');
                return;
            }
            alert('✅ Sukses (Prototipe UI):\nPassword akun toolman &quot;' + this.resetNama + '&quot; (' + this.resetEmail + ') berhasil direset!\n\nPassword baru: ' + this.newPassword + '\n\nSilakan berikan password baru ini kepada staf bersangkutan.');
            this.showResetModal = false;
        }
    }" class="min-h-screen bg-slate-50 p-6">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Akun Toolman</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola hak akses pengguna untuk pengurus / admin masing-masing bengkel.
                </p>
            </div>

            <!-- Action Button -->
            <a href="{{ route('superadmin.master.toolman.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Tambah Toolman Baru
            </a>
        </div>

        <!-- Toolbar / Search & Filter Card -->
        <div
            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex flex-col sm:flex-row w-full gap-4">
                <!-- Search -->
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Cari nama atau NIP..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm outline-none transition-all shadow-sm">
                </div>

                <!-- Filter Bengkel -->
                <select
                    class="w-full sm:w-48 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all bg-white">
                    <option value="">Semua Penempatan</option>
                    <option value="tkj">TKJ</option>
                    <option value="tkr">TKR</option>
                    <option value="av">Audio Video</option>
                </select>
            </div>

            <!-- Info Text -->
            <div class="text-sm text-gray-500 whitespace-nowrap">
                Total: <span class="font-bold text-gray-800">3</span> Akun
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold w-16 text-center">No</th>
                            <th class="py-3 px-4 font-semibold">Profil Toolman</th>
                            <th class="py-3 px-4 font-semibold">Penempatan Bengkel</th>
                            <th class="py-3 px-4 font-semibold text-center">Status Akun</th>
                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">

                        <!-- Row 1: Active Toolman -->
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-4 text-center font-medium text-gray-500">1</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar Initial -->
                                    <div
                                        class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold border border-green-200">
                                        AR
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">Ahmad Riyadi, S.Kom.</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span>NIP. 198005122005011003</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-gray-500">ahmad.riyadi@smkn3yk.sch.id</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    TKJ
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-green-600/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openReset('Ahmad Riyadi, S.Kom.', 'TKJ', 'ahmad.riyadi@smkn3yk.sch.id')"
                                        class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 border border-amber-200/60 rounded-lg transition-all shadow-xs"
                                        title="Reset Password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                            </path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('superadmin.master.toolman.edit') }}"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all shadow-xs"
                                        title="Edit Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button type="button" @click="openDelete('Ahmad Riyadi, S.Kom.', 'TKJ', 'ahmad.riyadi@smkn3yk.sch.id')"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all shadow-xs"
                                        title="Hapus Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2: Inactive/Suspended Toolman -->
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-4 text-center font-medium text-gray-500">2</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar Initial with gray tones -->
                                    <div
                                        class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                                        DD
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">Dwi Darmawan</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span>NIP. - (Honorer)</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-gray-500">dwi.toolman@smkn3yk.sch.id</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    Audio Video
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-red-50 text-red-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-red-600/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Nonaktif
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openReset('Dwi Darmawan', 'Audio Video', 'dwi.toolman@smkn3yk.sch.id')"
                                        class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 border border-amber-200/60 rounded-lg transition-all shadow-xs"
                                        title="Reset Password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                            </path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('superadmin.master.toolman.edit') }}"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all shadow-xs"
                                        title="Edit Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button type="button" @click="openDelete('Dwi Darmawan', 'Audio Video', 'dwi.toolman@smkn3yk.sch.id')"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all shadow-xs"
                                        title="Hapus Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS TOOLMAN -->
        <div x-show="showDeleteModal" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <!-- Backdrop Overlay -->
            <div x-show="showDeleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="showDeleteModal = false"></div>

            <!-- Modal Panel Centered -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showDeleteModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    @keydown.escape.window="showDeleteModal = false"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                    
                    <!-- Close button (top right) -->
                    <button type="button" @click="showDeleteModal = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <!-- Warning Icon -->
                            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 ring-8 ring-red-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>

                            <div class="flex-1 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Hapus Akun Toolman?</h3>
                                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus akses akun staf toolman berikut?
                                </p>

                                <!-- Card Item Preview -->
                                <div class="mt-3.5 p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                                    <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Nama Akun:</div>
                                    <div class="font-bold text-gray-900 text-sm" x-text="deleteNama"></div>
                                    <div class="flex items-center gap-2 pt-1 text-xs text-gray-600">
                                        <span class="px-2 py-0.5 rounded bg-white border border-gray-200 font-medium" x-text="deleteBengkel"></span>
                                        <span class="text-gray-400">•</span>
                                        <span class="font-mono text-gray-500 truncate" x-text="deleteEmail"></span>
                                    </div>
                                </div>

                                <!-- Alert Box Danger -->
                                <div class="mt-3.5 flex items-center gap-2 p-2.5 bg-red-50/70 border border-red-200/80 rounded-lg text-xs text-red-700">
                                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>Pengguna tidak akan dapat lagi masuk ke dashboard toolman.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-xs transition-colors">
                            Batal
                        </button>
                        <button type="button" @click="confirmDelete()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Ya, Hapus Akun
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- MODAL RESET PASSWORD TOOLMAN -->
        <div x-show="showResetModal" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-reset-title" role="dialog" aria-modal="true">
            
            <!-- Backdrop Overlay -->
            <div x-show="showResetModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="showResetModal = false"></div>

            <!-- Modal Panel Centered -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showResetModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    @keydown.escape.window="showResetModal = false"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    
                    <!-- Close button (top right dengan z-index dan posisi aman) -->
                    <button type="button" @click="showResetModal = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition-colors z-20 focus:outline-none"
                        title="Tutup Modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Modal Header -->
                    <div class="p-6 sm:p-7 border-b border-gray-100">
                        <div class="flex items-start gap-4 pr-10">
                            <!-- Icon Kunci Amber -->
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200/80 ring-4 ring-amber-50/80 shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                            </div>
                            <!-- Judul & Subtitle Header -->
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug" id="modal-reset-title">
                                    Kredensial Login & Ganti Password
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    Reset dan tetapkan password baru untuk staf toolman.
                                </p>
                            </div>
                        </div>

                        <!-- Target Account Card Preview -->
                        <div class="mt-4 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-green-100 text-green-700 font-bold text-xs flex items-center justify-center shrink-0 border border-green-200">
                                    <span x-text="resetNama ? resetNama.substring(0,2).toUpperCase() : 'TM'"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Akun Sasaran:</div>
                                    <div class="font-bold text-gray-900 text-sm truncate" x-text="resetNama"></div>
                                    <div class="text-xs text-gray-500 font-mono truncate" x-text="resetEmail"></div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-md bg-white border border-gray-200 text-xs font-semibold text-slate-700 shadow-2xs shrink-0" x-text="resetBengkel"></span>
                        </div>
                    </div>

                    <!-- Modal Body / Form Fields -->
                    <form @submit.prevent="confirmReset()" class="p-6 sm:p-7 space-y-4">
                        <!-- Password Baru with Acak Password button rapi di baris label -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="modal_new_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Password Baru <span class="text-red-500">*</span>
                                </label>
                                <!-- Tombol Acak Password rapi berdampingan dengan label -->
                                <button type="button" @click="autoGeneratePass()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Acak Password
                                </button>
                            </div>
                            <div class="relative">
                                <input :type="showNewPassword ? 'text' : 'password'" id="modal_new_password" x-model="newPassword" required
                                    minlength="8" placeholder="Minimal 8 karakter"
                                    class="w-full pl-3.5 pr-11 py-2.5 rounded-lg border border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 text-sm shadow-sm font-mono tracking-wide">
                                <button type="button" 
                                    @click="showNewPassword = !showNewPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-700 transition-colors cursor-pointer"
                                    :title="showNewPassword ? 'Sembunyikan Password' : 'Lihat Password'">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showNewPassword">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showNewPassword" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label for="modal_confirm_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Ulangi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input :type="showNewPassword ? 'text' : 'password'" id="modal_confirm_password" x-model="newPasswordConfirmation" required
                                    minlength="8" placeholder="Ketik ulang password baru"
                                    class="w-full pl-3.5 pr-11 py-2.5 rounded-lg border border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 text-sm shadow-sm font-mono tracking-wide">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                    <span x-show="newPassword && newPasswordConfirmation && newPassword === newPasswordConfirmation" class="text-emerald-500 text-xs font-bold flex items-center gap-1">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice Alert -->
                        <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-start gap-2.5 text-xs text-amber-900 leading-relaxed">
                            <svg class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                Password baru akan langsung aktif setelah disimpan. Pastikan untuk mencatat dan memberikan password ini kepada staf toolman terkait.
                            </span>
                        </div>

                        <!-- Footer Modal Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showResetModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-xs transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all hover:shadow-md cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection
