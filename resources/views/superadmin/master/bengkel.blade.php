@extends('layouts.admin')

@section('content')
    <div x-data="{
        showDeleteModal: false,
        deleteNama: '',
        deleteKode: '',
        openDelete(nama, kode) {
            this.deleteNama = nama;
            this.deleteKode = kode;
            this.showDeleteModal = true;
        },
        confirmDelete() {
            alert('✅ Sukses (Prototipe UI):\nData bengkel &quot;' + this.deleteNama + '&quot; (' + this.deleteKode + ') berhasil disimulasikan dihapus!');
            this.showDeleteModal = false;
        }
    }" class="min-h-screen bg-slate-50 p-6">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Data Bengkel & Jurusan</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola daftar bengkel/jurusan beserta penanggung jawabnya.</p>
            </div>

            <!-- Action Button -->
            <a href="{{ route('superadmin.master.bengkel.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Bengkel
            </a>
        </div>

        <!-- Toolbar / Search Card -->
        <div
            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <!-- Search -->
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" placeholder="Cari nama bengkel atau kode..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm outline-none transition-all shadow-sm">
            </div>

            <!-- Info Text -->
            <div class="text-sm text-gray-500">
                Total: <span class="font-bold text-gray-800">3</span> Bengkel Terdaftar
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold w-16 text-center">No</th>
                            <th class="py-3 px-4 font-semibold">Info Bengkel</th>
                            <th class="py-3 px-4 font-semibold">Kepala Bengkel</th>
                            <th class="py-3 px-4 font-semibold text-center">Total Inventaris</th>
                            <th class="py-3 px-4 font-semibold text-center">Total BHP</th>
                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">

                        <!-- Row 1: TKJ -->
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-4 text-center font-medium text-gray-500">1</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-900">Teknik Komputer Jaringan</div>
                                <div class="inline-flex items-center gap-1 mt-1">
                                    <span
                                        class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-mono border border-gray-200">BGK-TKJ</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-gray-900">Ahmad Riyadi, S.Kom.</div>
                                <div class="text-xs text-gray-500">NIP. 19800512 200501 1 003</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-blue-600/20">
                                    450 Item
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-amber-600/20">
                                    1.205 Item
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('superadmin.master.bengkel.edit') }}"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all inline-flex items-center justify-center shadow-xs"
                                        title="Edit Bengkel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button type="button" @click="openDelete('Teknik Komputer Jaringan', 'BGK-TKJ')"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all inline-flex items-center justify-center shadow-xs"
                                        title="Hapus Bengkel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2: TKR -->
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-4 text-center font-medium text-gray-500">2</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-900">Teknik Kendaraan Ringan</div>
                                <div class="inline-flex items-center gap-1 mt-1">
                                    <span
                                        class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-mono border border-gray-200">BGK-TKR</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-gray-900">Budi Santoso, S.Pd., M.T.</div>
                                <div class="text-xs text-gray-500">NIP. 19781123 200312 1 002</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-blue-600/20">
                                    320 Item
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-amber-600/20">
                                    850 Item
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('superadmin.master.bengkel.edit') }}"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all inline-flex items-center justify-center shadow-xs"
                                        title="Edit Bengkel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button type="button" @click="openDelete('Teknik Kendaraan Ringan', 'BGK-TKR')"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all inline-flex items-center justify-center shadow-xs"
                                        title="Hapus Bengkel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 3: Audio Video -->
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-4 text-center font-medium text-gray-500">3</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-900">Audio Video</div>
                                <div class="inline-flex items-center gap-1 mt-1">
                                    <span
                                        class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-mono border border-gray-200">BGK-AV</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-gray-900">Siti Aminah, S.T.</div>
                                <div class="text-xs text-gray-500">NIP. 19850314 201001 2 005</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-blue-600/20">
                                    210 Item
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-inset ring-amber-600/20">
                                    430 Item
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('superadmin.master.bengkel.edit') }}"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all inline-flex items-center justify-center shadow-xs"
                                        title="Edit Bengkel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button type="button" @click="openDelete('Audio Video', 'BGK-AV')"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all inline-flex items-center justify-center shadow-xs"
                                        title="Hapus Bengkel">
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

        <!-- MODAL KONFIRMASI HAPUS BENGKEL -->
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
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Hapus Data Bengkel?</h3>
                                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus data bengkel berikut dari sistem?
                                </p>

                                <!-- Card Item Preview -->
                                <div class="mt-3.5 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                                    <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Nama Bengkel:</div>
                                    <div class="font-bold text-gray-900 text-sm mt-0.5" x-text="deleteNama"></div>
                                    <div class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-0.5 rounded bg-white border border-gray-200 text-xs font-mono font-medium text-gray-700 shadow-xs">
                                        <span>Kode:</span>
                                        <span class="text-red-600 font-semibold" x-text="deleteKode"></span>
                                    </div>
                                </div>

                                <!-- Alert Box Danger -->
                                <div class="mt-3.5 flex items-center gap-2 p-2.5 bg-red-50/70 border border-red-200/80 rounded-lg text-xs text-red-700">
                                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>Seluruh data inventaris dan hak akses toolman terkait akan terpengaruh.</span>
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
                            Ya, Hapus Bengkel
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
