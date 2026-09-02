@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <!-- Header Form -->
    <div class="mb-8 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang! 👋</h2>
        <p class="text-sm text-gray-500">Silakan masuk dengan akun yang terdaftar.</p>
    </div>

    <!-- Session Status (Bawaan Laravel Breeze) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
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

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif
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
@endsection
