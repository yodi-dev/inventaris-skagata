@extends('layouts.admin')

@section('title', 'Tambah Akun Toolman Baru')
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
                    <span class="text-gray-800 font-semibold">Tambah Akun Baru</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Akun Staf Toolman Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi formulir ringkas berikut untuk mendaftarkan akun pengelola bengkel.</p>
            </div>

            <a href="{{ route('superadmin.master.toolman') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium border border-gray-300 shadow-sm transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Form Card -->
        <form id="formTambahToolman" action="{{ route('superadmin.master.toolman') }}" method="GET"
            class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden"
            x-data="{
                showPassword: false,
                autoGeneratePass() {
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
                    let pass = '';
                    for(let i=0; i<10; i++) {
                        pass += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    document.getElementById('password').value = pass;
                    document.getElementById('password_confirmation').value = pass;
                    this.showPassword = true;
                }
            }">

            <!-- SECTION 1: Data Pribadi & Penempatan Bengkel -->
            <div class="p-6 sm:p-8 border-b border-gray-200 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Profil Staf & Penempatan Bengkel</h3>
                        <p class="text-xs text-gray-500">Informasi identitas staf dan area bengkel kerja yang dikelola.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Lengkap beserta Gelar <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                            placeholder="Contoh: Ahmad Riyadi, S.Kom."
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm placeholder:text-gray-400">
                        <p class="text-xs text-gray-400 mt-1">Nama ini akan tampil sebagai penanggung jawab sirkulasi alat dan inventaris.</p>
                    </div>

                    <!-- NIP / NUPTK -->
                    <div>
                        <label for="nip" class="block text-sm font-semibold text-gray-700 mb-1">
                            NIP / NUPTK <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <input type="text" id="nip" name="nip"
                            placeholder="Contoh: 19800512 200501 1 003"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika staf honorer / belum memiliki NIP.</p>
                    </div>

                    <!-- Nomor WhatsApp / Kontak -->
                    <div>
                        <label for="no_telepon" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-xs font-bold">
                                +62
                            </div>
                            <input type="tel" id="no_telepon" name="no_telepon" required
                                placeholder="812-3456-7890"
                                class="w-full pl-12 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Digunakan untuk kontak koordinasi pengajuan barang dan darurat.</p>
                    </div>

                    <!-- Penugasan Bengkel Utama -->
                    <div class="md:col-span-2">
                        <label for="bengkel_penempatan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Bengkel / Jurusan Penugasan <span class="text-red-500">*</span>
                        </label>
                        <select id="bengkel_penempatan" name="bengkel_penempatan" required
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm">
                            <option value="" disabled selected>-- Pilih Bengkel / Jurusan Tempat Bertugas --</option>
                            <option value="TKJ">Teknik Komputer Jaringan (BGK-TKJ)</option>
                            <option value="TKR">Teknik Kendaraan Ringan (BGK-TKR)</option>
                            <option value="AV">Teknik Audio Video (BGK-AV)</option>
                            <option value="TITL">Teknik Instalasi Tenaga Listrik (BGK-TITL)</option>
                            <option value="BOGA">Tata Boga / Kuliner (BGK-BGA)</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Akun ini memiliki hak akses inventaris dan sirkulasi pada bengkel terpilih.</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Kredensial Akun Login -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Kredensial Login Aplikasi</h3>
                            <p class="text-xs text-gray-500">Username dan password yang digunakan staf toolman untuk masuk ke sistem.</p>
                        </div>
                    </div>

                    <!-- Tombol Acak Password -->
                    <button type="button" @click="autoGeneratePass()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Acak Password
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Email Login -->
                    <div class="md:col-span-2">
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
                                placeholder="nama.toolman@smkn3yk.sch.id"
                                class="w-full pl-10 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-medium">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Disarankan menggunakan email institusi sekolah resmi (@smkn3yk.sch.id).</p>
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                Password Akun <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="showPassword = !showPassword"
                                class="text-xs text-gray-500 hover:text-green-700 transition-colors">
                                <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></span>
                            </button>
                        </div>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                minlength="8" placeholder="Minimal 8 karakter"
                                class="w-full pr-10 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600"
                                @click="showPassword = !showPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showPassword">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showPassword" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                            Ulangi Password <span class="text-red-500">*</span>
                        </label>
                        <input :type="showPassword ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                            minlength="8" placeholder="Ketik ulang password"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono">
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS FOOTER -->
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 flex items-center gap-1.5 order-2 sm:order-1">
                    <span class="text-red-500 font-bold">*</span> Menandakan bidang wajib diisi sebelum menyimpan data.
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
                        Simpan Akun Toolman
                    </button>
                </div>
            </div>

        </form>

        <!-- Script Simulasi Form -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formTambahToolman');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const pass = document.getElementById('password').value;
                    const passConf = document.getElementById('password_confirmation').value;

                    if (pass !== passConf) {
                        alert('⚠️ Perhatian: Konfirmasi password tidak cocok dengan password yang dimasukkan.');
                        return;
                    }

                    const nama = document.getElementById('nama_lengkap').value || 'Staf Toolman';
                    const bengkel = document.getElementById('bengkel_penempatan').value || 'Bengkel Terpilih';
                    const email = document.getElementById('email').value || '-';

                    alert(`✅ Sukses (Prototipe UI):\nAkun Toolman "${nama}" (${bengkel})\nEmail: ${email}\n\nBerhasil disimulasikan dibuat! Mengalihkan kembali ke data akun toolman...`);

                    window.location.href = "{{ route('superadmin.master.toolman') }}";
                });
            });
        </script>

    </div>
@endsection
