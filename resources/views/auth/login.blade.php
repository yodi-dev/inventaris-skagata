@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <!-- Header Form -->
    <div class="mb-8 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang!</h2>
        <p class="text-sm text-gray-500">Silakan masuk dengan akun yang terdaftar.</p>
    </div>

    <!-- Session Status (Bawaan Laravel Breeze) -->
    @if (session('status'))
        <div
            class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-start gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="email" list="email-history"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
            <datalist id="email-history"></datalist>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Remember Me & Lupa Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 cursor-pointer">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
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
            <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                Log In
            </button>
        </div>
    </form>

    <!-- Link Pendaftaran untuk Peminjam -->
    @if (Route::has('register'))
        <div class="mt-8 text-center text-sm text-gray-600">
            Belum punya akun peminjam?
            <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                Daftar di sini
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
