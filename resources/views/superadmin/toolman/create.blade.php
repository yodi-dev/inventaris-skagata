@extends('layouts.admin')

@section('title', 'Tambah Akun Toolman Baru')
@section('header_title', 'Master Data Akun Toolman')

@section('content')
    <div class="space-y-6 max-w-5xl">

        <!-- Breadcrumb & Top Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('superadmin.toolman.index') }}" class="hover:text-primary-600 transition-colors">Akun Toolman</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">Tambah Akun Baru</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Akun Staf Toolman Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi formulir berikut untuk mendaftarkan staf penanggung jawab bengkel.</p>
            </div>

            <a href="{{ route('superadmin.toolman.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium border border-gray-300 shadow-sm transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Alert Kesalahan Validasi -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm shadow-xs">
                <div class="font-bold flex items-center gap-2 mb-1.5">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Mohon periksa kembali formulir Anda:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs pl-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <form id="formTambahToolman" action="{{ route('superadmin.toolman.store') }}" method="POST"
            class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden"
            x-data="{
                showPassword: false,
                autoGeneratePass() {
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
                    let pass = '';
                    for (let i = 0; i < 10; i++) {
                        pass += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    const p = document.getElementById('password');
                    const pc = document.getElementById('password_confirmation');
                    if (p) p.value = pass;
                    if (pc) pc.value = pass;
                    this.showPassword = true;
                }
            }">
            @csrf

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
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Lengkap beserta Gelar <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                            placeholder="Contoh: Ahmad Riyadi, S.Kom."
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm placeholder:text-gray-400 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Nama ini akan tampil sebagai penanggung jawab sirkulasi alat dan inventaris.</p>
                        @enderror
                    </div>

                    <!-- NIP / NUPTK -->
                    <div>
                        <label for="nomor_identitas" class="block text-sm font-semibold text-gray-700 mb-1">
                            NIP / NUPTK <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <input type="text" id="nomor_identitas" name="nomor_identitas" value="{{ old('nomor_identitas') }}"
                            placeholder="Contoh: 19800512 200501 1 003"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono @error('nomor_identitas') border-red-500 @enderror">
                        @error('nomor_identitas')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Kosongkan jika staf berstatus honorer atau belum memiliki NIP.</p>
                        @enderror
                    </div>

                    <!-- Nomor WhatsApp / Kontak -->
                    <div>
                        <label for="nomor_wa" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="nomor_wa" name="nomor_wa" required value="{{ old('nomor_wa') }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm @error('nomor_wa') border-red-500 @enderror">
                        @error('nomor_wa')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Digunakan untuk kontak koordinasi pengajuan barang dan darurat.</p>
                        @enderror
                    </div>

                    <!-- Penugasan Bengkel Utama -->
                    <div class="md:col-span-2">
                        <label for="bengkel_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Bengkel Penugasan <span class="text-red-500">*</span>
                        </label>
                        <select id="bengkel_id" name="bengkel_id" required
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm @error('bengkel_id') border-red-500 @enderror">
                            <option value="" disabled {{ old('bengkel_id') ? '' : 'selected' }}>-- Pilih Bengkel Tempat Bertugas --</option>
                            @foreach ($bengkels as $bengkel)
                                <option value="{{ $bengkel->id }}" {{ old('bengkel_id') == $bengkel->id ? 'selected' : '' }}>
                                    {{ $bengkel->nama }} {{ ($bengkel->kode ?? $bengkel->kode_bengkel) ? '('.($bengkel->kode ?? $bengkel->kode_bengkel).')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('bengkel_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Akun ini memiliki hak akses inventaris dan sirkulasi pada bengkel terpilih.</p>
                        @enderror
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
                            <p class="text-xs text-gray-500">Email dan kata sandi yang digunakan staf toolman untuk masuk ke sistem.</p>
                        </div>
                    </div>

                    <!-- Tombol Acak Password -->
                    <button type="button" @click="autoGeneratePass()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
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
                            <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                placeholder="nama.toolman@smkn3yk.sch.id"
                                class="w-full pl-10 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-medium @error('email') border-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Gunakan alamat email aktif yang belum pernah terdaftar di sistem.</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                Password Login <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="showPassword = !showPassword"
                                class="text-xs text-gray-500 hover:text-gray-700 font-medium">
                                <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></span>
                            </button>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                            minlength="8" placeholder="Minimal 8 karakter"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter kombinasi huruf, angka, atau simbol.</p>
                        @enderror
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
                    <a href="{{ route('superadmin.toolman.index') }}"
                        class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 text-sm font-medium rounded-lg transition-colors shadow-sm text-center">
                        Batal
                    </a>
                    <button type="reset"
                        class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Reset
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-semibold shadow transition-all hover:shadow-md cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Akun Toolman
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection

