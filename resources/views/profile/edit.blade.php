@php
    // Role ditentukan dari variable yang dikirim controller/route
    $currentRole = $role ?? 'peminjam';
    if (!in_array($currentRole, ['peminjam', 'toolman', 'superadmin'])) {
        $currentRole = 'peminjam';
    }
    $layout = $currentRole === 'peminjam' ? 'layouts.peminjam' : 'layouts.admin';

    // Inisial Nama
    $words = explode(' ', trim($user->name ?? 'User'));
    $initials = '';
    foreach (array_slice($words, 0, 2) as $w) {
        $initials .= strtoupper(substr($w, 0, 1));
    }
    $initials = $initials ?: 'SK';

    // Status label
    if (($user->status ?? 'aktif') === 'aktif') {
        $statusAkun = match ($currentRole) {
            'superadmin' => 'Super Administrator',
            'toolman' => 'Petugas Aktif',
            default => 'Aktif Terverifikasi',
        };
    } else {
        $statusAkun = ucfirst($user->status ?? 'Aktif');
    }

    // Color theme & labels per role
    if ($currentRole === 'superadmin') {
        $roleBadge = 'Waka Sarpras · Super Admin';
        $subinfo = 'Wakil Kepala Sekolah Bidang Sarana & Prasarana';
        $identifierLabel = 'NIP Pegawai Negeri';
        $bengkelLabel = 'Unit Kerja';
        $bengkelValue = 'Seluruh Bengkel SMKN 3 Yogyakarta';
        $bannerBg = 'bg-gradient-to-r from-slate-900 via-emerald-950 to-green-900';
        $avatarBg = 'bg-green-700 text-white';
        $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200/80';
    } elseif ($currentRole === 'toolman') {
        $roleBadge = 'Toolman · ' . ($user->bengkel->nama ?? 'Bengkel');
        $subinfo = 'Penanggung Jawab Bengkel ' . ($user->bengkel->nama ?? '-');
        $identifierLabel = 'NIP / NUPTK Petugas';
        $bengkelLabel = 'Bengkel Tanggung Jawab';
        $bengkelValue = $user->bengkel->nama ?? 'Belum Ditentukan';
        $bannerBg = 'bg-gradient-to-r from-slate-900 via-blue-950 to-blue-900';
        $avatarBg = 'bg-blue-700 text-white';
        $badgeClass = 'bg-blue-50 text-blue-800 border-blue-200/80';
    } else {
        $isGuru = method_exists($user, 'isGuru') ? $user->isGuru() : ($user->jenis_peminjam ?? '') === 'guru';
        $roleBadge = $isGuru ? 'Guru / Pendidik' : 'Siswa · ' . ($user->bengkel->kode ?? 'SMKN 3');
        $subinfo = $isGuru
            ? 'Guru Kejuruan · SMKN 3 Yogyakarta'
            : 'Siswa ' . ($user->bengkel->nama ?? 'Kejuruan') . ' · SMKN 3 Yogyakarta';
        $identifierLabel = $isGuru ? 'NIP / NUPTK Guru' : 'NISN / NIS Siswa';
        $bengkelLabel = 'Bengkel / Jurusan';
        $bengkelValue = $user->bengkel->nama ?? ($isGuru ? 'Lintas Jurusan' : 'Belum Ditentukan');
        $bannerBg = 'bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800';
        $avatarBg = 'bg-emerald-600 text-white';
        $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200/80';
    }
@endphp

@extends($layout)

@section('title', 'Pengaturan Akun & Profil')
@section('header_title', 'Pengaturan Akun')

@section('content')
    <div x-data="{
        activeTab: 'profile',
        pwdNew: '',
        pwdConfirm: '',
        showOld: false,
        showNew: false
    }" x-cloak class="max-w-4xl mx-auto space-y-5 sm:space-y-6 pb-10">

        {{-- Flash Messages --}}
        @if (session('status') === 'profile-updated')
            <div
                class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Profil Berhasil Disimpan</p>
                    <p class="text-xs text-emerald-700 mt-0.5">Informasi akun Anda telah berhasil diperbarui pada sistem.
                    </p>
                </div>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div
                class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Kata Sandi Berhasil Diperbarui</p>
                    <p class="text-xs text-emerald-700 mt-0.5">Gunakan kata sandi baru Anda untuk sesi login berikutnya.</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm shadow-xs">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Terdapat kesalahan pada formulir:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ====================================================== --}}
        {{-- HERO PROFILE CARD                                        --}}
        {{-- ====================================================== --}}
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
            {{-- Dekoratif banner atas --}}
            <div class="h-20 sm:h-24 {{ $bannerBg }}"></div>

            <div class="px-5 sm:px-7 pb-6 -mt-10 sm:-mt-12">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-5">
                    {{-- Avatar & Identitas Singkat --}}
                    <div class="flex items-end gap-3 sm:gap-4">
                        <div class="relative">
                            <div
                                class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl border-4 border-white shadow-md font-black text-2xl sm:text-3xl flex items-center justify-center {{ $avatarBg }}">
                                {{ $initials }}
                            </div>
                        </div>

                        <div class="mb-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-lg sm:text-2xl font-black text-gray-900 leading-tight">
                                    {{ $user->name }}
                                </h1>
                                <span
                                    class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>{{ $statusAkun }}</span>
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $subinfo }}</p>
                        </div>
                    </div>

                    {{-- Role Badge Modern (Bersih tanpa emoji) --}}
                    <span
                        class="self-start sm:self-auto inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide border shadow-2xs mt-1 sm:mt-0 {{ $badgeClass }}">
                        @if ($currentRole === 'superadmin')
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        @elseif ($currentRole === 'toolman')
                            <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        @endif
                        <span>{{ $roleBadge }}</span>
                    </span>
                </div>

                {{-- Statistik Cepat --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-4 border-t border-gray-100">
                    @foreach ($stats as $stat)
                        <div class="bg-slate-50 border border-gray-200/70 rounded-2xl p-3.5 text-center sm:text-left">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide truncate">
                                {{ $stat['label'] }}</p>
                            <p class="text-base sm:text-xl font-black text-gray-900 mt-0.5 leading-tight">
                                {{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ====================================================== --}}
        {{-- TAB NAVIGATION                                           --}}
        {{-- ====================================================== --}}
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
            <button type="button" @click="activeTab = 'profile'"
                class="px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all shrink-0 flex items-center gap-2 border"
                :class="activeTab === 'profile'
                    ?
                    'bg-green-700 text-white border-green-700 shadow-sm' :
                    'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Informasi Akun</span>
            </button>
            <button type="button" @click="activeTab = 'security'"
                class="px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all shrink-0 flex items-center gap-2 border"
                :class="activeTab === 'security'
                    ?
                    'bg-green-700 text-white border-green-700 shadow-sm' :
                    'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Keamanan & Password</span>
            </button>
        </div>

        {{-- ====================================================== --}}
        {{-- TAB: INFORMASI AKUN                                      --}}
        {{-- ====================================================== --}}
        <div x-show="activeTab === 'profile'" x-transition>
            <form method="POST" action="{{ route('profile.update') }}"
                class="bg-white rounded-3xl border border-gray-200/80 shadow-xs p-5 sm:p-7 space-y-6">
                @csrf
                @method('patch')

                <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Biodata Pengguna</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi nama lengkap dan kontak yang terhubung
                        dengan akun ini.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">

                    {{-- Nama Lengkap --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Nama
                            Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            placeholder="Nama lengkap sesuai data sekolah"
                            class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium transition-all outline-none">
                    </div>

                    {{-- Identifier (NIP/NIS) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">
                                {{ $identifierLabel }}
                            </label>
                            <span class="text-[10px] text-gray-400 flex items-center gap-1 font-medium">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Terkunci
                            </span>
                        </div>
                        <input type="text" value="{{ $user->nomor_identitas ?? '-' }}" readonly
                            class="w-full text-xs sm:text-sm font-mono border border-gray-200 rounded-xl py-3 px-3.5 bg-gray-100 text-gray-500 cursor-not-allowed select-all outline-none">
                        <p class="text-[10px] text-gray-400 mt-1">Identitas resmi dikelola oleh pihak sekolah.</p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Alamat
                            Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            placeholder="email@smkn3yk.sch.id"
                            class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium transition-all outline-none">
                    </div>

                    {{-- No. HP --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">No. WhatsApp /
                            HP</label>
                        <input type="text" name="nomor_wa" value="{{ old('nomor_wa', $user->nomor_wa) }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium transition-all outline-none">
                        <p class="text-[10px] text-gray-400 mt-1">Digunakan untuk notifikasi dan konfirmasi peminjaman.</p>
                    </div>

                    {{-- Bengkel / Unit Kerja --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                            <span>{{ $bengkelLabel }}</span>
                        </label>
                        <input type="text" value="{{ $bengkelValue }}" readonly
                            class="w-full text-xs sm:text-sm font-medium border border-gray-200 rounded-xl py-3 px-3.5 bg-gray-100 text-gray-700 cursor-not-allowed select-all outline-none">
                    </div>

                </div>

                {{-- Info Tambahan Berbasis Role --}}
                @if ($currentRole === 'peminjam')
                    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4.5 text-xs space-y-2.5">
                        <div class="flex items-center gap-2 font-bold text-emerald-900">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                            <span>Detail Keanggotaan Peminjam</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700">
                            <div>
                                <span class="text-gray-400 block text-[11px] mb-0.5 font-medium">Penempatan /
                                    Jurusan</span>
                                <span
                                    class="font-bold text-gray-900">{{ $user->bengkel->nama ?? 'Siswa Kejuruan' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px] mb-0.5 font-medium">Kategori Peminjam</span>
                                <span class="font-bold text-gray-900">{{ ucfirst($user->jenis_peminjam ?? 'Siswa') }}
                                    Terdaftar</span>
                            </div>
                        </div>
                    </div>
                @elseif ($currentRole === 'toolman')
                    <div class="bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4.5 text-xs space-y-2.5">
                        <div class="flex items-center gap-2 font-bold text-blue-900">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Lokasi & Jadwal Jaga Bengkel</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700">
                            <div>
                                <span class="text-gray-400 block text-[11px] mb-0.5 font-medium">Ruang Bengkel
                                    Dikelola</span>
                                <span class="font-bold text-gray-900">{{ $user->bengkel->nama ?? 'Bengkel' }}
                                    ({{ $user->bengkel->kode ?? '-' }})</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px] mb-0.5 font-medium">Jam Pelayanan
                                    Sirkulasi</span>
                                <span class="font-bold text-gray-900">Senin – Jumat: 07:00 – 15:30 WIB</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4.5 text-xs space-y-2.5">
                        <div class="flex items-center gap-2 font-bold text-gray-900">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Jabatan Struktural & Wewenang</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700">
                            <div>
                                <span class="text-gray-400 block text-[11px] mb-0.5 font-medium">Jabatan Kedinasan</span>
                                <span class="font-bold text-gray-900">Wakil Kepala Sekolah Bidang Sarana & Prasarana</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px] mb-0.5 font-medium">Cakupan Wewenang</span>
                                <span class="font-bold text-gray-900">Verifikasi & Persetujuan RAB Seluruh Bengkel</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs sm:text-sm font-bold text-white bg-green-700 hover:bg-green-800 active:scale-[0.98] shadow-xs transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </form>
        </div>

        {{-- ====================================================== --}}
        {{-- TAB: KEAMANAN & PASSWORD                                 --}}
        {{-- ====================================================== --}}
        <div x-show="activeTab === 'security'" x-transition>
            <form method="POST" action="{{ route('password.update') }}"
                class="bg-white rounded-3xl border border-gray-200/80 shadow-xs p-5 sm:p-7 space-y-6">
                @csrf
                @method('put')

                <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Keamanan & Kata Sandi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui password akun secara berkala dengan kombinasi huruf,
                        angka, dan simbol.</p>
                </div>

                <div class="max-w-xl space-y-4">

                    {{-- Password Lama --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Kata Sandi Saat
                            Ini</label>
                        <div class="relative">
                            <input :type="showOld ? 'text' : 'password'" name="current_password" required
                                placeholder="Masukkan kata sandi lama..."
                                class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 pl-3.5 pr-11 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium transition-all outline-none">
                            <button type="button" @click="showOld = !showOld"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                :title="showOld ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                                <svg x-show="!showOld" class="w-4.5 h-4.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showOld" x-cloak class="w-4.5 h-4.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Kata Sandi
                            Baru</label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" name="password" x-model="pwdNew" required
                                minlength="8" placeholder="Minimal 8 karakter..."
                                class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 pl-3.5 pr-11 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium transition-all outline-none">
                            <button type="button" @click="showNew = !showNew"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                :title="showNew ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                                <svg x-show="!showNew" class="w-4.5 h-4.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showNew" x-cloak class="w-4.5 h-4.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        {{-- Strength Bar --}}
                        <div class="mt-2 space-y-1">
                            <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                    :style="`width: ${Math.min(pwdNew.length / 12 * 100, 100)}%`"
                                    :class="{
                                        'bg-rose-500': pwdNew.length > 0 && pwdNew.length < 6,
                                        'bg-amber-500': pwdNew.length >= 6 && pwdNew.length < 8,
                                        'bg-emerald-500': pwdNew.length >= 8
                                    }">
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400">
                                <span x-show="pwdNew.length === 0">Minimal 8 karakter.</span>
                                <span x-show="pwdNew.length > 0 && pwdNew.length < 6" class="text-rose-500">Terlalu
                                    pendek</span>
                                <span x-show="pwdNew.length >= 6 && pwdNew.length < 8" class="text-amber-600">Hampir
                                    cukup…</span>
                                <span x-show="pwdNew.length >= 8" class="text-emerald-600 font-medium">Sangat baik &
                                    kuat</span>
                            </p>
                        </div>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Konfirmasi Kata
                            Sandi Baru</label>
                        <input :type="showNew ? 'text' : 'password'" name="password_confirmation" x-model="pwdConfirm"
                            required placeholder="Ketik ulang kata sandi baru..."
                            class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 font-medium transition-all outline-none"
                            :class="pwdConfirm && pwdNew !== pwdConfirm ?
                                'border-rose-400 focus:ring-rose-400 focus:border-rose-400' : ''">
                        <p x-show="pwdConfirm && pwdNew !== pwdConfirm"
                            class="text-[10px] text-rose-500 mt-1 font-medium">Kata sandi tidak cocok.</p>
                    </div>

                </div>

                {{-- Info Sesi Aktif --}}
                <div class="max-w-xl bg-slate-50 border border-gray-200 rounded-2xl p-4 text-xs space-y-1">
                    <p class="font-bold text-gray-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Sesi Login Terverifikasi
                    </p>
                    <p class="text-gray-500">Sistem Informasi Manajemen Inventaris & Bengkel SMKN 3 Yogyakarta (Sibenka)
                    </p>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs sm:text-sm font-bold text-white bg-gray-900 hover:bg-black active:scale-[0.98] shadow-xs transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection
