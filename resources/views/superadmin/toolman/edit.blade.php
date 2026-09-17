@extends('layouts.admin')

@section('title', 'Edit Akun Toolman')
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
                    <span class="text-gray-800 font-semibold">Edit Akun</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Akun Staf Toolman</h1>
                    @if ($toolman->status === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            Suspend
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Perbarui profil staf, penempatan bengkel, kontak, dan status akun toolman.</p>
            </div>

            <a href="{{ route('superadmin.toolman.index') }}"
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
                        {{ strtoupper(substr($toolman->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900">{{ $toolman->name }}</h2>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                Toolman Bengkel
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <span class="font-mono">{{ $toolman->nomor_identitas ? 'NIP: ' . $toolman->nomor_identitas : 'Non-NIP' }}</span>
                            <span class="text-gray-300">•</span>
                            <span>{{ $toolman->email }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 text-slate-700 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $toolman->bengkel->nama ?? 'Belum ada bengkel' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info Status -->
                <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                    <div class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Status Akun</div>
                    <div class="text-xs font-semibold {{ $toolman->status === 'aktif' ? 'text-emerald-700' : 'text-red-700' }} mt-0.5">
                        {{ $toolman->status === 'aktif' ? 'Aktif Beroperasi' : 'Suspend / Ditangguhkan' }}
                    </div>
                    <div class="text-[11px] text-gray-500 flex items-center sm:justify-end gap-1 mt-0.5">
                        <span>WA: {{ $toolman->nomor_wa }}</span>
                    </div>
                </div>
            </div>
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
        <form id="formEditToolman" action="{{ route('superadmin.toolman.update', $toolman->id) }}" method="POST"
            class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden"
            x-data="{
                statusAkun: '{{ old('status', $toolman->status === 'suspend' ? 'suspend' : 'aktif') }}'
            }">
            @csrf
            @method('PUT')

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
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Lengkap beserta Gelar <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            value="{{ old('name', $toolman->name) }}"
                            placeholder="Contoh: Ahmad Riyadi, S.Kom."
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm placeholder:text-gray-400 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Nama ini tercatat sebagai penanggung jawab dalam transaksi peminjaman & mutasi alat.</p>
                        @enderror
                    </div>

                    <!-- NIP / NUPTK -->
                    <div>
                        <label for="nomor_identitas" class="block text-sm font-semibold text-gray-700 mb-1">
                            NIP / NUPTK <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <input type="text" id="nomor_identitas" name="nomor_identitas"
                            value="{{ old('nomor_identitas', $toolman->nomor_identitas) }}"
                            placeholder="Contoh: 19800512 200501 1 003"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono @error('nomor_identitas') border-red-500 @enderror">
                        @error('nomor_identitas')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Kosongkan jika staf berstatus honorer / belum memiliki NIP.</p>
                        @enderror
                    </div>

                    <!-- Nomor WhatsApp Aktif -->
                    <div>
                        <label for="nomor_wa" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="nomor_wa" name="nomor_wa" required
                            value="{{ old('nomor_wa', $toolman->nomor_wa) }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm @error('nomor_wa') border-red-500 @enderror">
                        @error('nomor_wa')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Digunakan untuk konfirmasi peminjaman alat darurat & notifikasi pengadaan.</p>
                        @enderror
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
                                value="{{ old('email', $toolman->email) }}"
                                placeholder="nama.toolman@smkn3yk.sch.id"
                                class="w-full pl-10 rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-medium @error('email') border-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Digunakan oleh staf toolman untuk masuk ke portal bengkel.</p>
                        @enderror
                    </div>

                    <!-- Penugasan Bengkel Utama -->
                    <div>
                        <label for="bengkel_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Bengkel Penugasan <span class="text-red-500">*</span>
                        </label>
                        <select id="bengkel_id" name="bengkel_id" required
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm @error('bengkel_id') border-red-500 @enderror">
                            @foreach ($bengkels as $bengkel)
                                <option value="{{ $bengkel->id }}" {{ old('bengkel_id', $toolman->bengkel_id) == $bengkel->id ? 'selected' : '' }}>
                                    {{ $bengkel->nama }} {{ ($bengkel->kode ?? $bengkel->kode_bengkel) ? '('.($bengkel->kode ?? $bengkel->kode_bengkel).')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('bengkel_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Menentukan ruang lingkup inventaris dan data sirkulasi yang dikelola toolman.</p>
                        @enderror
                    </div>

                    <!-- Status Akun -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Status Akun <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                :class="statusAkun === 'aktif' ? 'border-green-500 bg-green-50/50 ring-1 ring-green-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="status" value="aktif" x-model="statusAkun" class="text-green-600 focus:ring-green-500">
                                <div>
                                    <div class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </div>
                                    <div class="text-[11px] text-gray-500">Bisa login & kelola data inventaris bengkel</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                :class="statusAkun === 'suspend' ? 'border-red-500 bg-red-50/50 ring-1 ring-red-500' : 'border-gray-200 hover:bg-gray-50'">
                                <input type="radio" name="status" value="suspend" x-model="statusAkun" class="text-red-600 focus:ring-red-500">
                                <div>
                                    <div class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Suspend / Nonaktif
                                    </div>
                                    <div class="text-[11px] text-gray-500">Akses login ditangguhkan sementara</div>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
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
                            <strong>Perubahan Kata Sandi:</strong> Untuk mereset kata sandi akun staf ini, Anda dapat menggunakan tombol aksi <strong>Reset Password</strong> langsung pada tabel Manajemen Akun Toolman.
                        </span>
                    </div>
                    <a href="{{ route('superadmin.toolman.index') }}" class="inline-flex items-center gap-1 text-amber-900 font-semibold hover:underline whitespace-nowrap">
                        Ke Tabel Akun
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- SECTION 2: Metadata Akun (Read-only System Info) -->
            <div class="px-6 sm:px-8 py-4 bg-slate-50/70 border-t border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-500">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Didaftarkan Pada:</span>
                        <span class="font-medium text-gray-700">{{ $toolman->created_at ? $toolman->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Pembaruan Terakhir:</span>
                        <span class="font-medium text-gray-700">{{ $toolman->updated_at ? $toolman->updated_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS FOOTER -->
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 flex items-center gap-1.5 order-2 sm:order-1">
                    <span class="text-red-500 font-bold">*</span> Menandakan bidang wajib diisi sebelum menyimpan perubahan.
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
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection
