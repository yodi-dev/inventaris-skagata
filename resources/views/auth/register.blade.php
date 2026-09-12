@extends('layouts.auth')

@section('title', 'Daftar Akun Peminjam')

@section('content')
    <!-- Header Form -->
    <div class="mb-6 text-center lg:text-left">
        <div
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary-50 text-primary-800 border border-primary-200 text-xs font-semibold mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
            <span>Registrasi Peminjam</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Buat Akun Peminjam</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Pendaftaran akun khusus Siswa & Guru SMKN 3 Yogyakarta.</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{
        jenis: '{{ old('jenis_peminjam', 'siswa') }}',
        showPassword: false,
        showConfirm: false
    }">
        @csrf

        <!-- Pilihan Jenis Peminjam (Siswa / Guru) -->
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jenis Peminjam <span
                    class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label
                    class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all text-sm font-semibold shadow-2xs"
                    :class="jenis === 'siswa'
                        ?
                        'bg-primary-50 border-primary-500 text-primary-800 ring-2 ring-primary-500/20 shadow-xs' :
                        'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300'">
                    <input type="radio" name="jenis_peminjam" value="siswa" x-model="jenis" class="sr-only">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 transition-colors"
                            :class="jenis === 'siswa' ? 'text-primary-700' : 'text-gray-400'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        <span>Siswa</span>
                    </div>
                </label>
                <label
                    class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all text-sm font-semibold shadow-2xs"
                    :class="jenis === 'guru'
                        ?
                        'bg-primary-50 border-primary-500 text-primary-800 ring-2 ring-primary-500/20 shadow-xs' :
                        'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300'">
                    <input type="radio" name="jenis_peminjam" value="guru" x-model="jenis" class="sr-only">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 transition-colors"
                            :class="jenis === 'guru' ? 'text-primary-700' : 'text-gray-400'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Guru / Pengajar</span>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('jenis_peminjam')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Lengkap
                <span class="text-red-500">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name" placeholder="Nama lengkap sesuai identitas resmi"
                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 px-3.5 placeholder:text-gray-400">
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Alamat Email
                <span class="text-red-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                list="email-history" placeholder="nama@email.com"
                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 px-3.5 placeholder:text-gray-400">
            <datalist id="email-history"></datalist>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Nomor Identitas Dinamis (NIS / NIP) -->
        <div>
            <label for="nomor_identitas" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                <span x-text="jenis === 'siswa' ? 'Nomor Induk Siswa (NIS)' : 'Nomor Induk Pegawai (NIP)'"></span>
                <span class="text-red-500">*</span>
            </label>
            <input id="nomor_identitas" type="text" name="nomor_identitas" value="{{ old('nomor_identitas') }}" required
                :placeholder="jenis === 'siswa' ? 'Contoh: 12345' : 'Contoh: 198001012010011001'"
                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 px-3.5 font-mono placeholder:text-gray-400">
            <x-input-error :messages="$errors->get('nomor_identitas')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Pilihan Bengkel (Hanya jika Siswa) -->
        <div x-show="jenis === 'siswa'" x-transition class="space-y-1.5">
            <label for="bengkel_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                Bengkel Kejuruan <span class="text-red-500">*</span>
            </label>
            <select id="bengkel_id" name="bengkel_id" :required="jenis === 'siswa'"
                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 px-3.5 font-medium text-gray-800">
                <option value="">-- Pilih Bengkel Kejuruan --</option>
                @foreach ($bengkels as $bengkel)
                    <option value="{{ $bengkel->id }}" {{ old('bengkel_id') == $bengkel->id ? 'selected' : '' }}>
                        {{ $bengkel->nama }} ({{ $bengkel->kode }})
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-gray-500 flex items-center gap-1.5 pt-0.5">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Akun siswa akan terikat secara otomatis pada bengkel yang dipilih.</span>
            </p>
            <x-input-error :messages="$errors->get('bengkel_id')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- No. WhatsApp / HP -->
        <div>
            <label for="nomor_wa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                No. WhatsApp / HP <span class="text-[11px] text-gray-400 font-normal lowercase">(opsional)</span>
            </label>
            <input id="nomor_wa" type="text" name="nomor_wa" value="{{ old('nomor_wa') }}"
                placeholder="Contoh: 081234567890"
                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 px-3.5 placeholder:text-gray-400">
            <x-input-error :messages="$errors->get('nomor_wa')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Password Fields with Visibility Toggle -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Password
                    <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                        autocomplete="new-password" placeholder="Min. 8 karakter"
                        class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 pl-3.5 pr-10 placeholder:text-gray-400">
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                        tabindex="-1">
                        <template x-if="!showPassword">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <template x-if="showPassword">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </template>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation"
                    class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Konfirmasi <span
                        class="text-red-500">*</span></label>
                <div class="relative">
                    <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                        name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password"
                        class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 pl-3.5 pr-10 placeholder:text-gray-400">
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                        tabindex="-1">
                        <template x-if="!showConfirm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <template x-if="showConfirm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </template>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-xs" />
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="pt-2">
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out active:scale-[0.99]">
                <span>Daftar Sebagai Peminjam</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>

    <!-- Link kembali ke Login -->
    <div class="mt-6 text-center text-xs sm:text-sm text-gray-500">
        Sudah memiliki akun terdaftar?
        <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors">
            Masuk di sini
        </a>
    </div>

    <!-- Script Riwayat Input Email (Sinkron dengan Login) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const datalist = document.getElementById('email-history');
            const form = emailInput ? emailInput.closest('form') : null;
            const STORAGE_KEY = 'sibenka_email_history';

            if (!emailInput || !datalist) return;

            function getStoredEmails() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (e) {
                    return [];
                }
            }

            function populateDatalist() {
                const emails = getStoredEmails();
                datalist.innerHTML = '';
                emails.forEach(function(email) {
                    if (email && typeof email === 'string') {
                        const opt = document.createElement('option');
                        opt.value = email;
                        datalist.appendChild(opt);
                    }
                });
            }

            function saveEmail(email) {
                if (!email) return;
                const trimmed = email.trim();
                if (!trimmed || !trimmed.includes('@')) return;

                let emails = getStoredEmails().filter(function(item) {
                    return item.toLowerCase() !== trimmed.toLowerCase();
                });

                emails.unshift(trimmed);

                if (emails.length > 10) {
                    emails = emails.slice(0, 10);
                }

                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(emails));
                } catch (e) {
                    // Penanganan batas browser storage
                }
            }

            populateDatalist();

            if (form) {
                form.addEventListener('submit', function() {
                    saveEmail(emailInput.value);
                });
            }
        });
    </script>
@endsection
