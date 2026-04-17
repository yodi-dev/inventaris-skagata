<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Inventaris SMKN 3</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 dark:bg-gray-900 dark:text-white">

    <div class="min-h-screen flex">

        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1580582932707-520aed937b7b?q=80&w=1000&auto=format&fit=crop');">
            <div class="h-full w-full bg-green-900/60 dark:bg-gray-900/80 flex items-center justify-center">
                <div class="text-white text-center px-10">
                    <h1 class="text-5xl font-extrabold mb-4 tracking-tight">Sistem Inventaris</h1>
                    <p class="text-xl font-medium text-green-100">SMK Negeri 3 Yogyakarta</p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white dark:bg-gray-900 p-8 sm:p-12">
            <div class="w-full max-w-md">

                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Selamat Datang! 👋</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Silakan masuk dengan akun yang terdaftar.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat
                            Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autofocus autocomplete="username"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm transition duration-150">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                    </div>

                    <div class="mt-5">
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm transition duration-150">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500 dark:bg-gray-800 dark:border-gray-700 dark:focus:ring-green-600 dark:focus:ring-offset-gray-900">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat Saya') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium"
                                href="{{ route('password.request') }}">
                                {{ __('Lupa Password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-900 transition duration-150 ease-in-out">
                            Log In
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</body>

</html>
