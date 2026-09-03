@php
    // Role ditentukan dari variable yang dikirim controller/route (berbasis URL prefix)
    $currentRole = $role ?? 'peminjam';
    if (!in_array($currentRole, ['peminjam', 'toolman', 'superadmin'])) {
        $currentRole = 'peminjam';
    }
    $layout = ($currentRole === 'peminjam') ? 'layouts.peminjam' : 'layouts.admin';
@endphp

@extends($layout)

@section('title', 'Pengaturan Akun & Profil')
@section('header_title', 'Pengaturan Akun')

@section('content')
<div x-data="profileApp('{{ $currentRole }}')" x-cloak class="max-w-4xl mx-auto space-y-5 sm:space-y-6 pb-10">

    {{-- ====================================================== --}}
    {{-- TOAST NOTIFICATION                                       --}}
    {{-- ====================================================== --}}
    <div x-show="toast.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-24 sm:bottom-auto sm:top-20 right-4 left-4 sm:left-auto sm:right-6 z-50 max-w-sm border shadow-xl rounded-2xl p-4 flex items-start gap-3"
         :class="{
             'bg-emerald-50 border-emerald-200 text-emerald-900': toast.type === 'success',
             'bg-blue-50 border-blue-200 text-blue-900': toast.type === 'info',
             'bg-rose-50 border-rose-200 text-rose-900': toast.type === 'error'
         }">
        <div class="shrink-0 mt-0.5">
            <template x-if="toast.type === 'success'">
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </template>
            <template x-if="toast.type === 'info'">
                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </template>
            <template x-if="toast.type === 'error'">
                <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </template>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs sm:text-sm font-bold" x-text="toast.title"></p>
            <p class="text-xs text-current opacity-80 mt-0.5" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- ====================================================== --}}
    {{-- HERO PROFILE CARD                                        --}}
    {{-- ====================================================== --}}
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
        {{-- Dekoratif banner atas --}}
        <div class="h-20 sm:h-24"
             :class="{
                 'bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700': activeRole === 'peminjam',
                 'bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700': activeRole === 'toolman',
                 'bg-gradient-to-r from-purple-700 via-indigo-700 to-slate-800': activeRole === 'superadmin'
             }"></div>

        <div class="px-5 sm:px-7 pb-6 -mt-10 sm:-mt-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-5">
                {{-- Avatar --}}
                <div class="flex items-end gap-3 sm:gap-4">
                    <div class="relative">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl border-4 border-white shadow-md text-white font-black text-2xl sm:text-3xl flex items-center justify-center"
                             :class="{
                                 'bg-emerald-600': activeRole === 'peminjam',
                                 'bg-blue-600': activeRole === 'toolman',
                                 'bg-purple-700': activeRole === 'superadmin'
                             }"
                             x-text="profile.initials"></div>
                        <button type="button"
                                @click="showToast('Ubah Foto Profil', 'Silakan pilih foto JPG/PNG maksimal 2MB.', 'info')"
                                class="absolute -bottom-1.5 -right-1.5 w-7 h-7 rounded-xl bg-white border border-gray-200 text-gray-600 shadow-sm hover:bg-gray-50 transition-colors flex items-center justify-center"
                                title="Ubah Foto Profil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>

                    <div class="mb-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-2xl font-black text-gray-900 leading-tight" x-text="profile.nama"></h1>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span x-text="profile.statusAkun"></span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5" x-text="profile.subinfo"></p>
                    </div>
                </div>

                {{-- Role Badge --}}
                <span class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold tracking-wide border shadow-2xs mt-1 sm:mt-0"
                      :class="{
                          'bg-emerald-50 text-emerald-800 border-emerald-200': activeRole === 'peminjam',
                          'bg-blue-50 text-blue-800 border-blue-200': activeRole === 'toolman',
                          'bg-purple-50 text-purple-800 border-purple-200': activeRole === 'superadmin'
                      }">
                    <span x-text="profile.roleEmoji"></span>
                    <span x-text="profile.roleBadge"></span>
                </span>
            </div>

            {{-- Statistik Cepat --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-4 border-t border-gray-100">
                <template x-for="(stat, i) in profile.stats" :key="i">
                    <div class="bg-slate-50 border border-gray-200/70 rounded-2xl p-3 text-center sm:text-left">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide truncate" x-text="stat.label"></p>
                        <p class="text-base sm:text-xl font-black text-gray-900 mt-0.5 leading-tight" x-text="stat.value"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- TAB NAVIGATION                                           --}}
    {{-- ====================================================== --}}
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        <button @click="activeTab = 'profile'"
                class="px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all shrink-0 flex items-center gap-1.5 border"
                :class="activeTab === 'profile'
                    ? 'bg-primary-600 text-white border-primary-600 shadow-sm'
                    : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Informasi Akun</span>
        </button>
        <button @click="activeTab = 'security'"
                class="px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all shrink-0 flex items-center gap-1.5 border"
                :class="activeTab === 'security'
                    ? 'bg-primary-600 text-white border-primary-600 shadow-sm'
                    : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Keamanan & Password</span>
        </button>
    </div>

    {{-- ====================================================== --}}
    {{-- TAB: INFORMASI AKUN                                      --}}
    {{-- ====================================================== --}}
    <div x-show="activeTab === 'profile'" x-transition>
        <form @submit.prevent="saveProfile()" class="bg-white rounded-3xl border border-gray-200/80 shadow-xs p-5 sm:p-7 space-y-6">

            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Biodata Pengguna</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi nama lengkap dan kontak yang terhubung dengan akun ini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">

                {{-- Nama Lengkap --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Nama Lengkap</label>
                    <input type="text"
                           x-model="profile.nama"
                           required
                           placeholder="Nama lengkap sesuai data sekolah"
                           class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium transition-all outline-none">
                </div>

                {{-- Identifier (NIP/NIS) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wide" x-text="profile.identifierLabel"></label>
                        <span class="text-[10px] text-gray-400 flex items-center gap-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Terkunci
                        </span>
                    </div>
                    <input type="text"
                           x-model="profile.identifier"
                           readonly
                           class="w-full text-xs sm:text-sm font-mono border border-gray-200 rounded-xl py-3 px-3.5 bg-gray-100 text-gray-500 cursor-not-allowed select-none outline-none">
                    <p class="text-[10px] text-gray-400 mt-1">Identitas resmi dikelola oleh pihak sekolah.</p>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Alamat Email</label>
                    <input type="email"
                           x-model="profile.email"
                           placeholder="email@smkn3yk.sch.id"
                           class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium transition-all outline-none">
                </div>

                {{-- No. HP --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">No. WhatsApp / HP</label>
                    <input type="text"
                           x-model="profile.phone"
                           placeholder="08xxxxxxxxxx"
                           class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium transition-all outline-none">
                    <p class="text-[10px] text-gray-400 mt-1">Digunakan untuk konfirmasi peminjaman di bengkel.</p>
                </div>

                {{-- Bengkel / Unit Kerja --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                        <span x-text="activeRole === 'peminjam' ? 'Bengkel / Jurusan' : (activeRole === 'toolman' ? 'Bengkel Tanggung Jawab' : 'Unit Kerja')"></span>
                    </label>
                    <input type="text"
                           x-model="profile.bengkel"
                           readonly
                           class="w-full text-xs sm:text-sm font-medium border border-gray-200 rounded-xl py-3 px-3.5 bg-gray-100 text-gray-700 cursor-not-allowed select-none outline-none">
                </div>

            </div>

            {{-- Info Tambahan Berbasis Role --}}
            <template x-if="activeRole === 'peminjam'">
                <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-4 text-xs space-y-2">
                    <p class="font-bold text-emerald-900 flex items-center gap-1.5">🎓 Kelas & Pembimbing Praktik</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                        <div>
                            <span class="text-gray-400 block text-[11px] mb-0.5">Kelas & Konsentrasi</span>
                            <span class="font-bold text-gray-900">XI TKJ 1 (Teknik Komputer Jaringan)</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[11px] mb-0.5">Guru Pembimbing Kejuruan</span>
                            <span class="font-bold text-gray-900">Pak Yono, S.Pd.T.</span>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="activeRole === 'toolman'">
                <div class="bg-blue-50/80 border border-blue-200 rounded-2xl p-4 text-xs space-y-2">
                    <p class="font-bold text-blue-900 flex items-center gap-1.5">🔧 Lokasi & Jadwal Jaga Bengkel</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                        <div>
                            <span class="text-gray-400 block text-[11px] mb-0.5">Pos / Ruang Kerja</span>
                            <span class="font-bold text-gray-900">Ruang Toolman Lab Jaringan Lt. 2</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[11px] mb-0.5">Jam Pelayanan Sirkulasi</span>
                            <span class="font-bold text-gray-900">Senin – Jumat: 07:00 – 15:30 WIB</span>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="activeRole === 'superadmin'">
                <div class="bg-purple-50/80 border border-purple-200 rounded-2xl p-4 text-xs space-y-2">
                    <p class="font-bold text-purple-900 flex items-center gap-1.5">🏛️ Jabatan Struktural</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                        <div>
                            <span class="text-gray-400 block text-[11px] mb-0.5">Surat Keputusan (SK)</span>
                            <span class="font-bold text-gray-900">SK No. 821/2025/SARPRAS</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[11px] mb-0.5">Cakupan Wewenang</span>
                            <span class="font-bold text-gray-900">RAB Seluruh Bengkel Skagata</span>
                        </div>
                    </div>
                </div>
            </template>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs sm:text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 active:scale-[0.98] shadow-xs transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>
    </div>

    {{-- ====================================================== --}}
    {{-- TAB: KEAMANAN & PASSWORD                                 --}}
    {{-- ====================================================== --}}
    <div x-show="activeTab === 'security'" x-transition>
        <form @submit.prevent="updatePassword()" class="bg-white rounded-3xl border border-gray-200/80 shadow-xs p-5 sm:p-7 space-y-6">

            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Keamanan & Kata Sandi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui password akun secara berkala dengan kombinasi huruf, angka, dan simbol.</p>
            </div>

            <div class="max-w-xl space-y-4">

                {{-- Password Lama --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input :type="showOld ? 'text' : 'password'"
                               x-model="pwd.old"
                               required
                               placeholder="Masukkan kata sandi lama..."
                               class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 pl-3.5 pr-11 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium transition-all outline-none">
                        <button type="button" @click="showOld = !showOld"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 text-base">
                            <span x-text="showOld ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'"
                               x-model="pwd.new"
                               required
                               minlength="8"
                               placeholder="Minimal 8 karakter..."
                               class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 pl-3.5 pr-11 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium transition-all outline-none">
                        <button type="button" @click="showNew = !showNew"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 text-base">
                            <span x-text="showNew ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    {{-- Strength Bar --}}
                    <div class="mt-2 space-y-1">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300"
                                 :style="`width: ${Math.min(pwd.new.length / 12 * 100, 100)}%`"
                                 :class="{
                                     'bg-rose-500': pwd.new.length > 0 && pwd.new.length < 6,
                                     'bg-amber-500': pwd.new.length >= 6 && pwd.new.length < 8,
                                     'bg-emerald-500': pwd.new.length >= 8
                                 }"></div>
                        </div>
                        <p class="text-[10px] text-gray-400">
                            <span x-show="pwd.new.length === 0">Minimal 8 karakter.</span>
                            <span x-show="pwd.new.length > 0 && pwd.new.length < 6" class="text-rose-500">Terlalu pendek</span>
                            <span x-show="pwd.new.length >= 6 && pwd.new.length < 8" class="text-amber-600">Hampir cukup…</span>
                            <span x-show="pwd.new.length >= 8" class="text-emerald-600">✓ Kuat & cukup</span>
                        </p>
                    </div>
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <input type="password"
                           x-model="pwd.confirm"
                           required
                           placeholder="Ketik ulang kata sandi baru..."
                           class="w-full text-xs sm:text-sm border border-gray-300 rounded-xl py-3 px-3.5 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium transition-all outline-none"
                           :class="pwd.confirm && pwd.new !== pwd.confirm ? 'border-rose-400 focus:ring-rose-400 focus:border-rose-400' : ''">
                    <p x-show="pwd.confirm && pwd.new !== pwd.confirm" class="text-[10px] text-rose-500 mt-1">Kata sandi tidak cocok.</p>
                </div>

            </div>

            {{-- Info Sesi Aktif --}}
            <div class="max-w-xl bg-slate-50 border border-gray-200 rounded-2xl p-4 text-xs space-y-1">
                <p class="font-bold text-gray-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Sesi Login Aktif
                </p>
                <p class="text-gray-500">Browser Chrome &bull; Jaringan Intranet SMKN 3 Yogyakarta (192.168.10.x)</p>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs sm:text-sm font-bold text-white bg-gray-900 hover:bg-black active:scale-[0.98] shadow-xs transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>

        </form>
    </div>

</div>

<script>
    function profileApp(initialRole) {
        const DATA = {
            peminjam: {
                nama: 'Budi Santoso',
                initials: 'BS',
                roleBadge: 'Siswa · XI TKJ 1',
                roleEmoji: '🎓',
                identifier: '0089-2122-1045',
                identifierLabel: 'NISN / NIS Siswa',
                email: 'budi.santoso@siswa.smkn3yk.sch.id',
                phone: '0857-1122-3344',
                bengkel: 'Teknik Komputer & Jaringan (TKJ)',
                subinfo: 'Siswa Kelas XI TKJ 1 · SMKN 3 Yogyakarta',
                statusAkun: 'Aktif Terverifikasi',
                stats: [
                    { label: 'Total Pinjam', value: '14 Kali' },
                    { label: 'Sedang Dipinjam', value: '1 Alat' },
                    { label: 'Tepat Waktu', value: '100%' },
                    { label: 'Pelanggaran', value: '0 (Bersih)' },
                ],
            },
            toolman: {
                nama: 'Bambang Wijaya, S.T.',
                initials: 'BW',
                roleBadge: 'Toolman · Bengkel TKJ',
                roleEmoji: '🔧',
                identifier: '19880315 201201 1 004',
                identifierLabel: 'NIP / NUPTK Petugas',
                email: 'bambang.wijaya@smkn3yk.sch.id',
                phone: '0813-9876-5432',
                bengkel: 'Teknik Komputer & Jaringan (TKJ)',
                subinfo: 'Penanggung Jawab Lab Jaringan Gedung B Lt. 2',
                statusAkun: 'Petugas Aktif',
                stats: [
                    { label: 'Barang Dikelola', value: '124 Jenis' },
                    { label: 'Antrean Pinjam', value: '8 Tiket' },
                    { label: 'Usulan RAB', value: '4 Draf' },
                    { label: 'Bengkel Binaan', value: 'TKJ' },
                ],
            },
            superadmin: {
                nama: 'Drs. H. Ahmad Riyadi, M.Pd.',
                initials: 'AR',
                roleBadge: 'Waka Sarpras · Super Admin',
                roleEmoji: '🏛️',
                identifier: '19750814 200003 1 002',
                identifierLabel: 'NIP Pegawai Negeri',
                email: 'ahmad.riyadi@smkn3yk.sch.id',
                phone: '0812-3456-7890',
                bengkel: 'Seluruh Bengkel Kejuruan SMKN 3 Yk',
                subinfo: 'Wakil Kepala Sekolah Bidang Sarana & Prasarana',
                statusAkun: 'Super Administrator',
                stats: [
                    { label: 'Bengkel Binaan', value: '6 Jurusan' },
                    { label: 'RAB Diverifikasi', value: '8 Usulan' },
                    { label: 'Anggaran ACC', value: 'Rp 26.2 Jt' },
                    { label: 'Wewenang', value: 'Penuh' },
                ],
            },
        };

        return {
            activeRole: initialRole,
            activeTab: 'profile',
            profile: structuredClone ? structuredClone(DATA[initialRole] ?? DATA.peminjam) : JSON.parse(JSON.stringify(DATA[initialRole] ?? DATA.peminjam)),

            pwd: { old: '', new: '', confirm: '' },
            showOld: false,
            showNew: false,

            toast: { show: false, title: '', message: '', type: 'success', timer: null },

            init() {
                const saved = localStorage.getItem('sibenka_profile_' + this.activeRole);
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        // Only restore editable fields
                        ['nama', 'email', 'phone'].forEach(k => {
                            if (parsed[k]) this.profile[k] = parsed[k];
                        });
                    } catch {}
                }
            },

            saveProfile() {
                localStorage.setItem('sibenka_profile_' + this.activeRole, JSON.stringify({
                    nama: this.profile.nama,
                    email: this.profile.email,
                    phone: this.profile.phone,
                }));
                this.showToast('Perubahan Disimpan!', 'Informasi profil akun berhasil diperbarui.', 'success');
            },

            updatePassword() {
                if (!this.pwd.old) {
                    this.showToast('Gagal', 'Masukkan kata sandi saat ini terlebih dahulu.', 'error');
                    return;
                }
                if (this.pwd.new.length < 8) {
                    this.showToast('Gagal', 'Kata sandi baru minimal 8 karakter.', 'error');
                    return;
                }
                if (this.pwd.new !== this.pwd.confirm) {
                    this.showToast('Gagal', 'Konfirmasi kata sandi tidak cocok.', 'error');
                    return;
                }
                this.pwd = { old: '', new: '', confirm: '' };
                this.showToast('Kata Sandi Diperbarui!', 'Gunakan kata sandi baru untuk login berikutnya.', 'success');
            },

            showToast(title, message, type = 'success') {
                clearTimeout(this.toast.timer);
                Object.assign(this.toast, { title, message, type, show: true });
                this.toast.timer = setTimeout(() => { this.toast.show = false; }, 3500);
            },
        };
    }

    window.profileApp = profileApp;
    document.addEventListener('alpine:init', () => {
        if (window.Alpine) window.Alpine.data('profileApp', profileApp);
    });
</script>
@endsection
