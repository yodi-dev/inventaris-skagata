@extends('layouts.admin')

@section('title', 'Edit Akun Toolman')
@section('header_title', 'Master Data Akun Toolman')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">

        <!-- Breadcrumb & Top Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('superadmin.master.toolman') }}" class="hover:text-primary-600 transition-colors">Akun Toolman</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">Edit Akun</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Akun Staf Toolman</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Perbarui profil staf, penempatan bengkel, kontak, dan status akun toolman.</p>
            </div>

            <a href="{{ route('superadmin.master.toolman') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium border border-gray-300 shadow-sm transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Quick Summary Header Card -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-green-100 border border-green-200 flex items-center justify-center text-green-700 font-bold text-xl shadow-xs">
                        AR
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900">Ahmad Riyadi, S.Kom.</h2>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                ASN Guru / Toolman
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <span class="font-mono">NIP. 19800512 200501 1 003</span>
                            <span class="text-gray-300">•</span>
                            <span>ahmad.riyadi@smkn3yk.sch.id</span>
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 text-slate-700 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Bengkel TKJ
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Last Active Info -->
                <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                    <div class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Aktivitas Terakhir</div>
                    <div class="text-xs font-semibold text-gray-700 mt-0.5">Hari ini, 07:45 WIB</div>
                    <div class="text-[11px] text-emerald-600 font-medium flex items-center sm:justify-end gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sedang Bertugas
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <form id="formEditToolman" action="{{ route('superadmin.master.toolman') }}" method="GET"
            class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden"
            x-data="{
                statusAkun: 'aktif'
            }">

            <!-- SECTION 1: Profil Staf & Penempatan Bengkel -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Profil Staf & Penempatan Bengkel</h3>
                        <p class="text-xs text-gray-500">Informasi identitas staf, kontak, dan area bengkel operasional yang dikelola.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Lengkap beserta Gelar <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                            value="Ahmad Riyadi, S.Kom."
                            placeholder="Contoh: Ahmad Riyadi, S.Kom."
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm placeholder:text-gray-400">
                        <p class="text-xs text-gray-400 mt-1">Nama ini tercatat sebagai penanggung jawab dalam transaksi peminjaman & mutasi alat.</p>
                    </div>

                    <!-- NIP / NUPTK -->
                    <div>
                        <label for="nip" class="block text-sm font-semibold text-gray-700 mb-1">
                            NIP / NUPTK <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <input type="text" id="nip" name="nip"
                            value="19800512 200501 1 003"
                            placeholder="Contoh: 19800512 200501 1 003"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika staf berstatus honorer / belum memiliki NIP.</p>
                    </div>

                    <!-- Nomor WhatsApp Aktif -->
                    <div>
                        <label for="no_telepon" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-xs font-bold">
                                +62
                            </div>
                            <input type="tel" id="no_telepon" name="no_telepon" required
                                value="812-3456-7890"
                                placeholder="812-3456-7890"
                                class="w-full pl-12 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Digunakan untuk konfirmasi peminjaman alat darurat & notifikasi pengadaan.</p>
                    </div>

                    <!-- Alamat Email (Username Login) -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                            Alamat Email (Username Login) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" required
                                value="ahmad.riyadi@smkn3yk.sch.id"
                                placeholder="nama.toolman@smkn3yk.sch.id"
                                class="w-full pl-10 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-medium">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Digunakan oleh staf toolman untuk masuk ke portal bengkel.</p>
                    </div>

                    <!-- Penugasan Bengkel Utama -->
                    <div>
                        <label for="bengkel_penempatan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Bengkel / Jurusan Penugasan <span class="text-red-500">*</span>
                        </label>
                        <select id="bengkel_penempatan" name="bengkel_penempatan" required
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm">
                            <option value="TKJ" selected>Teknik Komputer Jaringan (BGK-TKJ)</option>
                            <option value="TKR">Teknik Kendaraan Ringan (BGK-TKR)</option>
                            <option value="AV">Teknik Audio Video (BGK-AV)</option>
                            <option value="TITL">Teknik Instalasi Tenaga Listrik (BGK-TITL)</option>
                            <option value="BOGA">Tata Boga / Kuliner (BGK-BGA)</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Menentukan ruang lingkup inventaris dan data sirkulasi yang dikelola toolman.</p>
                    </div>

                    <!-- Status Akun -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Status Akun <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <label class="flex items-center gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-all"
                                :class="statusAkun === 'aktif' ? 'border-green-500 bg-green-50/50 ring-1 ring-green-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="status_akun" value="aktif" x-model="statusAkun" class="text-green-600 focus:ring-green-500">
                                <div>
                                    <div class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </div>
                                    <div class="text-[11px] text-gray-500">Bisa login & kelola data inventaris</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-all"
                                :class="statusAkun === 'nonaktif' ? 'border-red-500 bg-red-50/50 ring-1 ring-red-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="status_akun" value="nonaktif" x-model="statusAkun" class="text-red-600 focus:ring-red-500">
                                <div>
                                    <div class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Nonaktif
                                    </div>
                                    <div class="text-[11px] text-gray-500">Akses login ditangguhkan sementara</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Info Box Keterangan Pemindahan Fitur Password -->
                <div class="p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-amber-800">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                        <span>
                            <strong>Perubahan Kata Sandi:</strong> Untuk mereset kata sandi akun staf ini, gunakan tombol aksi <strong>Reset Password</strong> langsung pada tabel Manajemen Akun Toolman.
                        </span>
                    </div>
                    <a href="{{ route('superadmin.master.toolman') }}" class="inline-flex items-center gap-1 text-amber-900 font-semibold hover:underline whitespace-nowrap">
                        Ke Tabel Akun
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- SECTION 2: Metadata Akun (Read-only System Info) -->
            <div class="px-6 sm:px-8 py-4 bg-slate-50/70 border-t border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Dibuat Pada:</span>
                        <span class="font-medium text-gray-700">10 Januari 2024, 08:30 WIB</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Terakhir Login:</span>
                        <span class="font-medium text-gray-700">Hari ini, 07:45 WIB</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Lokasi Jaringan Terakhir:</span>
                        <span class="font-medium text-gray-700">192.168.10.45 (Lab Jaringan TKJ)</span>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS FOOTER -->
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 flex items-center gap-1.5 order-2 sm:order-1">
                    <span class="text-red-500 font-bold">*</span> Menandakan bidang wajib diisi sebelum menyimpan perubahan.
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end order-1 sm:order-2">
                    <a href="{{ route('superadmin.master.toolman') }}"
                        class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 text-sm font-medium rounded-lg transition-colors shadow-sm text-center">
                        Batal
                    </a>
                    <button type="reset"
                        class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Reset
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-semibold shadow transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>

        <!-- Live Interaction Script (Frontend Simulation) -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formEditToolman');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const nama = document.getElementById('nama_lengkap').value || 'Staf Toolman';
                    const bengkel = document.getElementById('bengkel_penempatan').value || 'Bengkel Terpilih';
                    const email = document.getElementById('email').value || '-';

                    alert(`✅ Sukses (Prototipe UI):\nPerubahan data akun Toolman "${nama}" (${bengkel})\nEmail: ${email}\n\nBerhasil disimpan! Mengalihkan kembali ke daftar akun toolman...`);

                    window.location.href = "{{ route('superadmin.master.toolman') }}";
                });
            });
        </script>

    </div>
@endsection
