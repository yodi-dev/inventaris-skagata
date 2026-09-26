@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <!-- Mobile Header Branding -->
    <div class="lg:hidden flex flex-col items-center mb-6 text-center">
        <img src="{{ asset('logo.png') }}" alt="Logo SMKN 3 Yogyakarta" class="w-16 h-16 mb-2.5 object-contain drop-shadow-xs">
        <div class="flex items-center gap-1.5">
            <span class="text-xl font-black text-gray-900 tracking-tight">SIBENKA</span>
            <span
                class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 border border-primary-200 shadow-2xs">SKAGATA</span>
        </div>
        <p class="text-xs text-gray-500 font-medium mt-1">Sistem Inventaris &amp; Sirkulasi Bengkel</p>
        <p class="text-[11px] text-gray-400 font-normal">SMK Negeri 3 Yogyakarta</p>
    </div>

    <!-- Header Form -->
    <div class="mb-6 text-center lg:text-left">
        <div
            class="hidden lg:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary-50 text-primary-800 border border-primary-200 text-xs font-semibold mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
            <span>Portal Masuk Akun</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Selamat Datang!</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Silakan masuk menggunakan akun SIBENKA yang terdaftar.</p>
    </div>

    <!-- Session Status (Sukses) -->
    @if (session('status'))
        <div
            class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-start gap-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    <!-- Session Error (Gagal/Peringatan) -->
    @if (session('error'))
        <div
            class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium flex items-start gap-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false, isSubmitting: false }"
        @submit="isSubmitting = true">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="email" list="email-history" placeholder="nama@email.com"
                class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 px-3.5 placeholder:text-gray-400">
            <datalist id="email-history"></datalist>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                    autocomplete="current-password" placeholder="Masukkan password Anda"
                    class="block w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-2xs transition text-sm py-2.5 pl-3.5 pr-10 placeholder:text-gray-400">
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                    tabindex="-1" title="Lihat/Sembunyikan Password">
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

        <!-- Remember Me & Lupa Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-gray-300 text-primary-600 shadow-2xs focus:ring-primary-500 cursor-pointer">
                <span class="ms-2 text-xs sm:text-sm text-gray-600 font-medium">{{ __('Ingat Saya') }}</span>
            </label>

            {{-- @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif --}}
        </div>

        <!-- Tombol Submit -->
        <div class="pt-2">
            <button type="submit" :disabled="isSubmitting"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out active:scale-[0.99] disabled:opacity-75 disabled:cursor-not-allowed">
                <template x-if="isSubmitting">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </template>
                <span x-text="isSubmitting ? 'Memproses Masuk...' : 'Masuk ke Akun'">Masuk ke Akun</span>
                <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>

    <!-- Link Pendaftaran untuk Peminjam -->
    @if (Route::has('register'))
        <div class="mt-6 text-center text-xs sm:text-sm text-gray-500">
            Belum punya akun peminjam?
            <a href="{{ route('register') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors">
                Daftar akun di sini
            </a>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const datalist = document.getElementById('email-history');
            const form = emailInput ? emailInput.closest('form') : null;
            const STORAGE_KEY = 'sibenka_email_history';

            if (!emailInput || !datalist) return;

            // 1. Ambil riwayat email tersimpan dari localStorage
            function getStoredEmails() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (e) {
                    return [];
                }
            }

            // 2. Render riwayat ke elemen <datalist>
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

            // 3. Simpan email saat formulir disubmit
            function saveEmail(email) {
                if (!email) return;
                const trimmed = email.trim();
                if (!trimmed || !trimmed.includes('@')) return;

                let emails = getStoredEmails().filter(function(item) {
                    return item.toLowerCase() !== trimmed.toLowerCase();
                });

                emails.unshift(trimmed);

                // Simpan hingga 10 riwayat alamat email terakhir
                if (emails.length > 10) {
                    emails = emails.slice(0, 10);
                }

                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(emails));
                } catch (e) {
                    // Penanganan jika storage dibatasi oleh browser
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
