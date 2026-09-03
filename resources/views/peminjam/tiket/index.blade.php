@extends('layouts.peminjam')

@section('title', 'Tiket Peminjaman Saya')

@section('content')
<div x-data="tiketPeminjamApp()" x-cloak class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Status Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Tiket Peminjaman Saya 🎟️</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau status persetujuan, barang aktif yang sedang dibawa, dan riwayat peminjaman.</p>
        </div>

        <!-- Filter Status Dropdown -->
        <div class="w-full sm:w-auto flex items-center gap-2">
            <select x-model="filterStatus"
                class="block w-full sm:w-auto text-xs bg-white border-gray-300 rounded-xl shadow-2xs focus:ring-primary-500 focus:border-primary-500 py-2 font-medium">
                <option value="all">Semua Status Tiket</option>
                <option value="pending">⏳ Menunggu Acc (Pending)</option>
                <option value="active">⚡ Sedang Dipinjam (Active)</option>
                <option value="selesai">✅ Selesai</option>
            </select>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition
         class="fixed top-20 right-4 left-4 sm:left-auto sm:right-6 z-50 max-w-sm bg-white border border-emerald-200 bg-emerald-50/90 text-emerald-900 shadow-xl rounded-2xl p-4 flex items-center gap-3">
        <span class="text-emerald-600 font-bold">✓</span>
        <div class="flex-1 text-xs" x-text="toast.message"></div>
        <button @click="toast.show = false" class="text-gray-400">&times;</button>
    </div>

    <!-- List of Tickets -->
    <div class="space-y-4 sm:space-y-5">
        <template x-for="tiket in filteredTickets" :key="tiket.id">
            <div class="bg-white border rounded-2xl shadow-xs overflow-hidden transition-all"
                 :class="{
                     'border-blue-200': tiket.status === 'active',
                     'border-amber-200': tiket.status === 'pending',
                     'border-gray-200 opacity-90': tiket.status === 'selesai'
                 }">
                <!-- Header Tiket -->
                <div class="px-4 sm:px-5 py-3 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2"
                     :class="{
                         'border-blue-100 bg-blue-50/50': tiket.status === 'active',
                         'border-amber-100 bg-amber-50/50': tiket.status === 'pending',
                         'border-gray-100 bg-gray-50/70': tiket.status === 'selesai'
                     }">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono font-bold"
                              :class="{
                                  'text-blue-700': tiket.status === 'active',
                                  'text-amber-800': tiket.status === 'pending',
                                  'text-gray-600': tiket.status === 'selesai'
                              }"
                              x-text="'#' + tiket.id"></span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-xs text-gray-500" x-text="tiket.tanggal"></span>
                    </div>
                    <div>
                        <template x-if="tiket.status === 'active'">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                                Aktif (Sedang Dipinjam)
                            </span>
                        </template>
                        <template x-if="tiket.status === 'pending'">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600 mr-1.5 animate-pulse"></span>
                                Menunggu Persetujuan Toolman
                            </span>
                        </template>
                        <template x-if="tiket.status === 'selesai'">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Selesai & Dikembalikan
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Body Tiket -->
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <!-- Rincian Barang -->
                        <div class="space-y-2 flex-1">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Daftar Barang yang Diminta:</h4>
                            <div class="bg-slate-50 border border-gray-200 rounded-xl p-3 space-y-2">
                                <template x-for="(b, idx) in tiket.items" :key="idx">
                                    <div class="flex justify-between items-center text-xs sm:text-sm"
                                         :class="idx > 0 ? 'border-t border-gray-200 pt-2' : ''">
                                        <div>
                                            <span class="font-bold text-gray-900" x-text="b.nama"></span>
                                            <span class="text-xs text-gray-500" x-text="' (' + b.qty + ' ' + (b.satuan || 'Unit') + ')'"></span>
                                            <template x-if="b.tipe === 'bahan'">
                                                <span class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200 ml-1">Habis Pakai</span>
                                            </template>
                                        </div>
                                        <span class="text-xs font-mono text-gray-400" x-text="b.kode || ''"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Batas Waktu & Keperluan -->
                        <div class="w-full md:w-72 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col justify-between space-y-2.5">
                            <div>
                                <p class="text-[11px] text-gray-400 font-semibold uppercase">Batas Pengembalian</p>
                                <p class="text-xs sm:text-sm font-bold" 
                                   :class="tiket.status === 'active' ? 'text-rose-600' : 'text-gray-700'"
                                   x-text="tiket.batasKembali || 'Hari Ini, 15:30 WIB'"></p>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 font-semibold uppercase">Tujuan Praktik</p>
                                <p class="text-xs font-medium text-gray-800 line-clamp-2" x-text="tiket.tujuan"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tiket: Tombol Aksi -->
                <div class="px-4 sm:px-5 py-3 border-t border-gray-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500">
                        <template x-if="tiket.status === 'active'">
                            <span>💡 Bawa alat kembali ke meja Toolman sebelum jam 15:30 WIB untuk cek fisik.</span>
                        </template>
                        <template x-if="tiket.status === 'pending'">
                            <span>⏳ Menunggu konfirmasi dari Toolman bengkel TKJ.</span>
                        </template>
                        <template x-if="tiket.status === 'selesai'">
                            <span>✅ Transaksi peminjaman telah selesai dan diverifikasi.</span>
                        </template>
                    </p>

                    <template x-if="tiket.status === 'active'">
                        <button @click="ajukanPengembalian(tiket)"
                                class="w-full sm:w-auto px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span>Ajukan Pengembalian Alat</span>
                        </button>
                    </template>

                    <template x-if="tiket.status === 'pending'">
                        <button @click="batalkanRequest(tiket.id)"
                                class="text-xs text-rose-600 hover:text-rose-700 font-semibold border border-rose-200 px-3 py-1.5 rounded-xl hover:bg-rose-50 transition-colors">
                            Batalkan Request
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="filteredTickets.length === 0">
            <div class="bg-white rounded-3xl p-8 sm:p-12 text-center border border-gray-200 shadow-xs max-w-md mx-auto">
                <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    🎟️
                </div>
                <h3 class="text-sm font-bold text-gray-800">Tidak ada tiket ditemukan</h3>
                <p class="text-xs text-gray-500 mt-1">Coba sesuaikan filter status tiket.</p>
                <a href="{{ route('peminjam.katalog.index') }}" class="mt-4 inline-block px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 font-semibold rounded-xl text-xs transition-colors">
                    Buka Katalog Barang
                </a>
            </div>
        </template>
    </div>
</div>

<script>
    function tiketPeminjamApp() {
        const DEFAULT_TICKETS = [
            {
                id: "TRX-2026-089",
                tanggal: "Dipinjam hari ini, 08:30 WIB",
                status: "active",
                batasKembali: "Besok, 14:00 WIB",
                tujuan: "Praktik Jaringan Dasar Pak Yono",
                items: [
                    { nama: "Crimping Tool RJ45", kode: "INV-TKJ-012", qty: 1, satuan: "Unit", tipe: "inventaris" },
                    { nama: "LAN Tester Digital", kode: "INV-TKJ-008", qty: 1, satuan: "Unit", tipe: "inventaris" }
                ]
            },
            {
                id: "TRX-2026-092",
                tanggal: "Diajukan 15 menit yang lalu",
                status: "pending",
                batasKembali: "Tidak Perlu Pengembalian (BHP)",
                tujuan: "Merakit kabel patch cord untuk lab komputer",
                items: [
                    { nama: "Kabel UTP Cat6 (Belden)", kode: "BHP-TKJ-001", qty: 10, satuan: "Meter", tipe: "bahan" }
                ]
            },
            {
                id: "TRX-2026-070",
                tanggal: "Selesai pada 28 Februari 2026",
                status: "selesai",
                batasKembali: "Tepat Waktu",
                tujuan: "Simulasi Uji Router Gateway",
                items: [
                    { nama: "Router Mikrotik RB951", kode: "INV-TKJ-045", qty: 1, satuan: "Unit", tipe: "inventaris" }
                ]
            }
        ];

        return {
            tickets: [],
            filterStatus: 'all',
            toast: {
                show: false,
                message: ''
            },

            init() {
                const stored = localStorage.getItem('sibenka_tickets');
                if (stored) {
                    try {
                        const parsed = JSON.parse(stored);
                        // Combine stored with default if stored doesn't have default
                        const combined = [...parsed];
                        DEFAULT_TICKETS.forEach(d => {
                            if (!combined.some(c => c.id === d.id)) {
                                combined.push(d);
                            }
                        });
                        this.tickets = combined;
                    } catch (e) {
                        this.tickets = JSON.parse(JSON.stringify(DEFAULT_TICKETS));
                    }
                } else {
                    this.tickets = JSON.parse(JSON.stringify(DEFAULT_TICKETS));
                }
            },

            get filteredTickets() {
                if (this.filterStatus === 'all') return this.tickets;
                return this.tickets.filter(t => t.status === this.filterStatus);
            },

            ajukanPengembalian(tiket) {
                tiket.status = 'selesai';
                tiket.batasKembali = 'Selesai & Dikembalikan';
                localStorage.setItem('sibenka_tickets', JSON.stringify(this.tickets));
                this.toast.message = `Permohonan pengembalian #${tiket.id} diajukan! Silakan temui Toolman untuk pengecekan fisik.`;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            },

            batalkanRequest(id) {
                this.tickets = this.tickets.filter(t => t.id !== id);
                localStorage.setItem('sibenka_tickets', JSON.stringify(this.tickets));
                this.toast.message = `Permohonan #${id} berhasil dibatalkan.`;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3500);
            }
        };
    }

    window.tiketPeminjamApp = tiketPeminjamApp;
    document.addEventListener('alpine:init', () => {
        if (window.Alpine) {
            window.Alpine.data('tiketPeminjamApp', tiketPeminjamApp);
        }
    });
</script>
@endsection
