@extends('layouts.admin')

@section('title', 'Persetujuan Pengadaan')
@section('header_title', 'Persetujuan Pengadaan (RAB)')

@section('content')
    <div x-data="{
        filterStatus: '',
        
        // Modal States
        showDetailModal: false,
        showApproveModal: false,
        showRevisiModal: false,
        showRejectModal: false,

        // Active Selected Item Data
        activeItem: {
            id: '',
            kode: '',
            tanggal: '',
            bengkel: '',
            pemohon: '',
            judul: '',
            kategori: '',
            barang: '',
            spesifikasi: '',
            qty: '',
            hargaSatuan: '',
            totalBiaya: '',
            status: '',
            statusLabel: '',
            keterangan: '',
            urgensi: 'Normal'
        },

        // Form Inputs
        catatanApprove: 'Disetujui untuk realisasi pengadaan semester berjalan.',
        sumberDana: 'BOS Reguler',
        catatanRevisi: '',
        rekomendasiBiaya: '',
        alasanTolak: '',

        // Open Modal Handlers
        openDetail(data) {
            this.activeItem = Object.assign({}, data);
            this.showDetailModal = true;
        },
        openApprove(data) {
            this.activeItem = Object.assign({}, data);
            this.catatanApprove = 'Disetujui untuk pengadaan ' + this.activeItem.bengkel + '.';
            this.sumberDana = 'BOS Reguler';
            this.showApproveModal = true;
        },
        openRevisi(data) {
            this.activeItem = Object.assign({}, data);
            this.catatanRevisi = '';
            this.rekomendasiBiaya = '';
            this.showRevisiModal = true;
        },
        openReject(data) {
            this.activeItem = Object.assign({}, data);
            this.alasanTolak = '';
            this.showRejectModal = true;
        },

        // Confirm Action Handlers
        confirmApprove() {
            alert('✅ Sukses (Prototipe UI):\nPengajuan ' + this.activeItem.kode + ' (' + this.activeItem.barang + ')\nSumber Dana: ' + this.sumberDana + '\n\nBERHASIL DISETUJUI oleh Waka Sarpras!');
            this.activeItem.status = 'approved';
            this.activeItem.statusLabel = 'Disetujui';
            this.showApproveModal = false;
            this.showDetailModal = false;
        },
        confirmRevisi() {
            if (!this.catatanRevisi.trim()) {
                alert('⚠️ Perhatian: Harap masukkan catatan revisi untuk staf toolman pengaju!');
                return;
            }
            alert('⚠️ Sukses (Prototipe UI):\nCatatan revisi untuk ' + this.activeItem.kode + ' berhasil dikirim ke ' + this.activeItem.bengkel + ':\n\n&quot;' + this.catatanRevisi + '&quot;');
            this.activeItem.status = 'revisi';
            this.activeItem.statusLabel = 'Direvisi';
            this.showRevisiModal = false;
            this.showDetailModal = false;
        },
        confirmReject() {
            if (!this.alasanTolak.trim()) {
                alert('⚠️ Perhatian: Harap masukkan alasan penolakan pengadaan!');
                return;
            }
            alert('❌ Sukses (Prototipe UI):\nPengajuan ' + this.activeItem.kode + ' (' + this.activeItem.barang + ') TELAH DITOLAK.\n\nAlasan: &quot;' + this.alasanTolak + '&quot;');
            this.activeItem.status = 'rejected';
            this.activeItem.statusLabel = 'Ditolak';
            this.showRejectModal = false;
            this.showDetailModal = false;
        }
    }" class="space-y-6">

        <!-- Header & Filter Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Pengajuan RAB Masuk</h2>
                <p class="text-sm text-gray-500 mt-0.5">Tinjau, setujui, berikan catatan revisi, atau tolak pengadaan barang dari tiap bengkel.</p>
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-3">
                <select x-model="filterStatus" class="text-sm border-gray-300 rounded-lg focus:border-green-600 focus:ring-green-600 shadow-sm bg-white">
                    <option value="">Semua Status Pengajuan</option>
                    <option value="pending">Menunggu Persetujuan (Pending)</option>
                    <option value="revisi">Perlu Revisi</option>
                    <option value="approved">Sudah Disetujui</option>
                </select>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-semibold">Tanggal / ID</th>
                            <th class="px-6 py-4 font-semibold">Bengkel / Pemohon</th>
                            <th class="px-6 py-4 font-semibold">Nama Barang & Spesifikasi</th>
                            <th class="px-6 py-4 font-semibold">Qty & Est. Biaya</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">

                        <!-- Row 1: Status Pending (TKJ) -->
                        <tr x-show="!filterStatus || filterStatus === 'pending'" class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900">02 Jun 2026</span>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">#RAB-2026-089</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">Teknik Komputer Jaringan</span>
                                <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Ahmad Riyadi, S.Kom. (Toolman TKJ)
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Router Mikrotik RB951</p>
                                <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">Port Gigabit 5x, Wireless 2.4GHz, untuk praktikum jaringan siswa kelas XI</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-bold text-gray-900">5 Unit</p>
                                <p class="text-xs text-emerald-700 font-semibold mt-0.5">Rp 4.500.000</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Detail Modal Button -->
                                    <button type="button"
                                        @click="openDetail({
                                            id: 1,
                                            kode: '#RAB-2026-089',
                                            tanggal: '02 Jun 2026',
                                            bengkel: 'Teknik Komputer Jaringan',
                                            pemohon: 'Ahmad Riyadi, S.Kom. (Toolman TKJ)',
                                            judul: 'Pengadaan Perangkat Jaringan Semester Genap',
                                            kategori: 'Fasilitas Praktik Siswa',
                                            barang: 'Router Mikrotik RB951',
                                            spesifikasi: '5x Port Ethernet 10/100/1000, Wireless 2.4GHz 802.11b/g/n, CPU 600MHz, RAM 128MB, Lisensi RouterOS Level 4, Adaptor Original 24V.',
                                            qty: '5 Unit',
                                            hargaSatuan: 'Rp 900.000',
                                            totalBiaya: 'Rp 4.500.000',
                                            status: 'pending',
                                            statusLabel: 'Pending',
                                            keterangan: 'Dibutuhkan untuk penambahan modul praktikum Routing Dinamis dan VLAN siswa kelas XI TKJ semester genap.',
                                            urgensi: 'Penting / Prioritas'
                                        })"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Lihat Rincian RAB">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>

                                    <!-- Approve Button -->
                                    <button type="button"
                                        @click="openApprove({
                                            kode: '#RAB-2026-089',
                                            bengkel: 'Teknik Komputer Jaringan',
                                            pemohon: 'Ahmad Riyadi, S.Kom.',
                                            barang: 'Router Mikrotik RB951 (5 Unit)',
                                            totalBiaya: 'Rp 4.500.000'
                                        })"
                                        class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 border border-emerald-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Setujui Pengadaan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>

                                    <!-- Revisi Button -->
                                    <button type="button"
                                        @click="openRevisi({
                                            kode: '#RAB-2026-089',
                                            bengkel: 'Teknik Komputer Jaringan',
                                            pemohon: 'Ahmad Riyadi, S.Kom.',
                                            barang: 'Router Mikrotik RB951 (5 Unit)',
                                            totalBiaya: 'Rp 4.500.000'
                                        })"
                                        class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 border border-amber-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Minta Revisi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <!-- Reject Button -->
                                    <button type="button"
                                        @click="openReject({
                                            kode: '#RAB-2026-089',
                                            bengkel: 'Teknik Komputer Jaringan',
                                            pemohon: 'Ahmad Riyadi, S.Kom.',
                                            barang: 'Router Mikrotik RB951 (5 Unit)',
                                            totalBiaya: 'Rp 4.500.000'
                                        })"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Tolak Pengadaan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2: Status Pending (TAV) -->
                        <tr x-show="!filterStatus || filterStatus === 'pending'" class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900">01 Jun 2026</span>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">#RAB-2026-088</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">Teknik Audio Video</span>
                                <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Dwi Darmawan (Toolman TAV)
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Osiloskop Digital Storage</p>
                                <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">Pengganti alat ukur lab elektronik lama yang rusak berat & tidak presisi</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-bold text-gray-900">2 Unit</p>
                                <p class="text-xs text-emerald-700 font-semibold mt-0.5">Rp 8.000.000</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Detail Button -->
                                    <button type="button"
                                        @click="openDetail({
                                            id: 2,
                                            kode: '#RAB-2026-088',
                                            tanggal: '01 Jun 2026',
                                            bengkel: 'Teknik Audio Video',
                                            pemohon: 'Dwi Darmawan (Toolman TAV)',
                                            judul: 'Penggantian Alat Ukur Elektronik Rusak',
                                            kategori: 'Penggantian Alat Rusak',
                                            barang: 'Osiloskop Digital Storage',
                                            spesifikasi: 'Bandwidth 100MHz, Dual Channel, Real-Time Sampling 1 GSa/s, Layar TFT LCD 7 Inch Berwarna, Support USB Host/Device, Termasuk 2 Probe BNC.',
                                            qty: '2 Unit',
                                            hargaSatuan: 'Rp 4.000.000',
                                            totalBiaya: 'Rp 8.000.000',
                                            status: 'pending',
                                            statusLabel: 'Pending',
                                            keterangan: '2 unit osiloskop analog lama di lab sistem audio mengalami kerusakan tabung CRT dan sparepart sudah diskontinyu.',
                                            urgensi: 'Sangat Mendesak'
                                        })"
                                        class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 border border-blue-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Lihat Rincian RAB">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>

                                    <!-- Approve Button -->
                                    <button type="button"
                                        @click="openApprove({
                                            kode: '#RAB-2026-088',
                                            bengkel: 'Teknik Audio Video',
                                            pemohon: 'Dwi Darmawan',
                                            barang: 'Osiloskop Digital Storage (2 Unit)',
                                            totalBiaya: 'Rp 8.000.000'
                                        })"
                                        class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 border border-emerald-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Setujui Pengadaan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>

                                    <!-- Revisi Button -->
                                    <button type="button"
                                        @click="openRevisi({
                                            kode: '#RAB-2026-088',
                                            bengkel: 'Teknik Audio Video',
                                            pemohon: 'Dwi Darmawan',
                                            barang: 'Osiloskop Digital Storage (2 Unit)',
                                            totalBiaya: 'Rp 8.000.000'
                                        })"
                                        class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 border border-amber-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Minta Revisi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <!-- Reject Button -->
                                    <button type="button"
                                        @click="openReject({
                                            kode: '#RAB-2026-088',
                                            bengkel: 'Teknik Audio Video',
                                            pemohon: 'Dwi Darmawan',
                                            barang: 'Osiloskop Digital Storage (2 Unit)',
                                            totalBiaya: 'Rp 8.000.000'
                                        })"
                                        class="p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200/60 rounded-lg transition-all shadow-2xs"
                                        title="Tolak Pengadaan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 3: Status Approved (Tata Boga) -->
                        <tr x-show="!filterStatus || filterStatus === 'approved'" class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900">28 May 2026</span>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">#RAB-2026-085</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">Tata Boga / Kuliner</span>
                                <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Siti Aminah (Toolman Boga)
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">Oven Listrik Heavy Duty</p>
                                <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">Penambahan kapasitas fasilitas praktik pembuatan roti & pastry</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-bold text-gray-900">1 Unit</p>
                                <p class="text-xs text-emerald-700 font-semibold mt-0.5">Rp 5.200.000</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Disetujui
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                        @click="openDetail({
                                            id: 3,
                                            kode: '#RAB-2026-085',
                                            tanggal: '28 May 2026',
                                            bengkel: 'Tata Boga / Kuliner',
                                            pemohon: 'Siti Aminah (Toolman Boga)',
                                            judul: 'Penambahan Alat Praktik Baking Siswa',
                                            kategori: 'Fasilitas Praktik Siswa',
                                            barang: 'Oven Listrik Heavy Duty',
                                            spesifikasi: 'Kapasitas 60L, Daya 1200 Watt, Temperature Control up to 250C, Rotisserie, 2 Tray Baking Alumunium.',
                                            qty: '1 Unit',
                                            hargaSatuan: 'Rp 5.200.000',
                                            totalBiaya: 'Rp 5.200.000',
                                            status: 'approved',
                                            statusLabel: 'Disetujui',
                                            keterangan: 'Telah disetujui Waka Sarpras menggunakan alokasi dana BOS Reguler 2026.',
                                            urgensi: 'Selesai'
                                        })"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg transition-colors"
                                        title="Lihat Rincian">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detail
                                    </button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500">Menampilkan 3 dari 12 total pengajuan RAB masuk</p>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-400 cursor-not-allowed" disabled>Sebelumnya</button>
                    <button class="px-3 py-1.5 bg-green-700 text-white rounded-lg text-xs font-semibold">1</button>
                    <button class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50">2</button>
                    <button class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50">Berikutnya</button>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 1. MODAL DETAIL PENGADAAN (RAB REVIEW) -->
        <!-- ==================================================== -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showDetailModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showDetailModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showDetailModal"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    @keydown.escape.window="showDetailModal = false"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">

                    <!-- Close Button -->
                    <button type="button" @click="showDetailModal = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition-colors z-20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Modal Header -->
                    <div class="p-6 sm:p-7 border-b border-gray-100 pr-14 bg-slate-50/50">
                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200/80 shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-bold text-gray-900 leading-snug">Rincian Pengajuan RAB</h3>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold font-mono"
                                        :class="{
                                            'bg-amber-100 text-amber-800 border border-amber-200': activeItem.status === 'pending',
                                            'bg-emerald-100 text-emerald-800 border border-emerald-200': activeItem.status === 'approved',
                                            'bg-red-100 text-red-800 border border-red-200': activeItem.status === 'rejected' || activeItem.status === 'revisi'
                                        }"
                                        x-text="activeItem.statusLabel || 'Pending'">
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                    <span class="font-mono font-bold text-blue-700" x-text="activeItem.kode"></span>
                                    <span>•</span>
                                    <span x-text="activeItem.tanggal"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body Details -->
                    <div class="p-6 sm:p-7 space-y-5 max-h-[70vh] overflow-y-auto">

                        <!-- Information Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/70 text-xs">
                            <div>
                                <span class="text-gray-400 block mb-0.5">Bengkel Pemohon:</span>
                                <span class="font-bold text-gray-900 text-sm" x-text="activeItem.bengkel"></span>
                                <span class="text-gray-500 block mt-0.5" x-text="activeItem.pemohon"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Kategori & Prioritas:</span>
                                <span class="font-semibold text-gray-800" x-text="activeItem.kategori"></span>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-medium" x-text="activeItem.urgensi"></span>
                            </div>
                        </div>

                        <!-- Item Detail Box -->
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200 font-semibold text-xs text-gray-700 uppercase tracking-wider">
                                Rincian Barang & Estimasi Anggaran
                            </div>
                            <div class="p-4 space-y-3 text-sm">
                                <div>
                                    <div class="font-bold text-gray-900 text-base" x-text="activeItem.barang"></div>
                                    <div class="text-xs text-gray-500 mt-1 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="font-semibold text-gray-700 block mb-1">Spesifikasi Teknis:</span>
                                        <span x-text="activeItem.spesifikasi"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3 pt-2 text-center">
                                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200/60">
                                        <span class="text-[11px] text-gray-400 block uppercase font-medium">Jumlah Unit</span>
                                        <span class="font-bold text-gray-800 text-base" x-text="activeItem.qty"></span>
                                    </div>
                                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200/60">
                                        <span class="text-[11px] text-gray-400 block uppercase font-medium">Estimasi Satuan</span>
                                        <span class="font-semibold text-gray-700" x-text="activeItem.hargaSatuan"></span>
                                    </div>
                                    <div class="p-2.5 bg-emerald-50 rounded-lg border border-emerald-200/80">
                                        <span class="text-[11px] text-emerald-700 block uppercase font-bold">Total Anggaran</span>
                                        <span class="font-bold text-emerald-800 text-base" x-text="activeItem.totalBiaya"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan & Alasan Pengajuan -->
                        <div class="text-xs text-gray-600 bg-amber-50/60 border border-amber-200/70 p-3.5 rounded-xl">
                            <span class="font-bold text-amber-900 block mb-1">Catatan / Justifikasi Pemohon:</span>
                            <p class="leading-relaxed" x-text="activeItem.keterangan || 'Tidak ada catatan tambahan.'"></p>
                        </div>
                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="p-5 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" @click="showDetailModal = false"
                            class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs w-full sm:w-auto">
                            Tutup
                        </button>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end" x-show="activeItem.status === 'pending'">
                            <!-- Tombol Tolak -->
                            <button type="button" @click="openReject(activeItem)"
                                class="px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold rounded-lg transition-colors">
                                Tolak Pengadaan
                            </button>
                            <!-- Tombol Minta Revisi -->
                            <button type="button" @click="openRevisi(activeItem)"
                                class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-lg transition-colors">
                                Minta Revisi
                            </button>
                            <!-- Tombol Setujui -->
                            <button type="button" @click="openApprove(activeItem)"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui Pengadaan
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 2. MODAL SETUJUI PENGADAAN (APPROVE MODAL) -->
        <!-- ==================================================== -->
        <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showApproveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showApproveModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showApproveModal"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 pr-12">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200/80 ring-4 ring-emerald-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">Setujui Pengajuan RAB?</h3>
                                <p class="text-xs text-gray-500 mt-1">Pengadaan akan disetujui dan dialokasikan ke anggaran sekolah.</p>
                            </div>
                        </div>

                        <!-- Card Preview -->
                        <div class="mt-4 p-3 bg-emerald-50/60 border border-emerald-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold text-emerald-900">
                                <span x-text="activeItem.kode"></span>
                                <span class="font-bold text-sm text-emerald-800" x-text="activeItem.totalBiaya"></span>
                            </div>
                            <div class="text-gray-600 font-medium" x-text="activeItem.barang"></div>
                            <div class="text-gray-500" x-text="activeItem.bengkel"></div>
                        </div>
                    </div>

                    <!-- Form Persetujuan -->
                    <form @submit.prevent="confirmApprove()" class="p-6 space-y-4">
                        <!-- Sumber Dana -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Sumber Alokasi Dana <span class="text-red-500">*</span>
                            </label>
                            <select x-model="sumberDana" required
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                                <option value="BOS Reguler">BOS Reguler (Belanja Praktik Siswa)</option>
                                <option value="BOP Provinsi">BOP Provinsi DIY (Sarana Prasarana)</option>
                                <option value="Dana Komite">Dana Komite Sekolah (Pengembangan Jurusan)</option>
                                <option value="Unit Produksi Bengkel">Kas Unit Produksi (UP) Bengkel</option>
                            </select>
                        </div>

                        <!-- Catatan Disetujui -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Catatan Persetujuan Waka Sarpras <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea x-model="catatanApprove" rows="2"
                                placeholder="Contoh: Disetujui untuk pengadaan termin 1 tahun ajaran 2026..."
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showApproveModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Ya, Setujui Pengadaan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 3. MODAL MINTA REVISI PENGADAAN -->
        <!-- ==================================================== -->
        <div x-show="showRevisiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showRevisiModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showRevisiModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showRevisiModal"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 pr-12">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200/80 ring-4 ring-amber-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">Kirim Catatan Revisi RAB</h3>
                                <p class="text-xs text-gray-500 mt-1">Minta pengurus bengkel untuk menyesuaikan kuantitas atau spesifikasi.</p>
                            </div>
                        </div>

                        <!-- Card Preview -->
                        <div class="mt-4 p-3 bg-amber-50/60 border border-amber-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold text-amber-900">
                                <span x-text="activeItem.kode"></span>
                                <span class="font-bold text-sm text-amber-800" x-text="activeItem.totalBiaya"></span>
                            </div>
                            <div class="text-gray-700 font-medium" x-text="activeItem.barang"></div>
                            <div class="text-gray-500" x-text="activeItem.bengkel"></div>
                        </div>
                    </div>

                    <!-- Form Revisi -->
                    <form @submit.prevent="confirmRevisi()" class="p-6 space-y-4">
                        <!-- Poin Catatan Revisi -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Poin / Catatan yang Perlu Diperbaiki <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="catatanRevisi" rows="3" required
                                placeholder="Contoh: Mohon kurangi jumlah pesanan dari 5 unit menjadi 3 unit terlebih dahulu, atau ganti spesifikasi ke merk alternatif yang lebih hemat biaya..."
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-amber-600 focus:ring-amber-600 shadow-sm"></textarea>
                        </div>

                        <!-- Batas Pagu Rekomendasi (Opsional) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Rekomendasi Maksimal Anggaran <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-bold text-gray-400">
                                    Rp
                                </div>
                                <input type="text" x-model="rekomendasiBiaya" placeholder="Contoh: 3.000.000"
                                    class="w-full pl-9 text-sm border-gray-300 rounded-lg focus:border-amber-600 focus:ring-amber-600 shadow-sm font-mono">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showRevisiModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kirim Catatan Revisi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 4. MODAL TOLAK PENGADAAN (REJECT MODAL) -->
        <!-- ==================================================== -->
        <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showRejectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showRejectModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showRejectModal"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 pr-12">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 border border-red-200/80 ring-4 ring-red-50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">Tolak Pengajuan RAB?</h3>
                                <p class="text-xs text-gray-500 mt-1">Pengadaan barang ini tidak akan disetujui untuk direalisasikan.</p>
                            </div>
                        </div>

                        <!-- Card Preview -->
                        <div class="mt-4 p-3 bg-red-50/60 border border-red-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold text-red-900">
                                <span x-text="activeItem.kode"></span>
                                <span class="font-bold text-sm text-red-800" x-text="activeItem.totalBiaya"></span>
                            </div>
                            <div class="text-gray-700 font-medium" x-text="activeItem.barang"></div>
                            <div class="text-gray-500" x-text="activeItem.bengkel"></div>
                        </div>
                    </div>

                    <!-- Form Penolakan -->
                    <form @submit.prevent="confirmReject()" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Alasan Penolakan Pengadaan <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="alasanTolak" rows="3" required
                                placeholder="Contoh: Stok alat di lab utama masih mencukupi, atau alokasi pagu anggaran belanja modal tahun ini sudah terpenuhi..."
                                class="w-full text-sm border-gray-300 rounded-lg focus:border-red-600 focus:ring-red-600 shadow-sm"></textarea>
                        </div>

                        <div class="p-3 bg-red-50/80 border border-red-200/80 rounded-xl text-xs text-red-800 flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Status pengajuan akan ditandai sebagai ditolak dan toolman akan menerima notifikasi alasan ini.</span>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showRejectModal = false"
                                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-2xs">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Ya, Tolak Pengadaan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection
