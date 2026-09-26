<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title dinamis per halaman -->
    <title>@yield('title', 'Autentikasi') - SIBENKA SMKN 3 Yogyakarta</title>

    <!-- Favicon / Logo Tab Browser -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-slate-50">

    <div class="min-h-screen flex">

        <!-- Bagian Kiri: Gambar & Branding (Tetap/Statis) -->
        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center relative"
            style="background-image: url('https://images.unsplash.com/photo-1580582932707-520aed937b7b?q=80&w=1000&auto=format&fit=crop');">

            <div class="absolute inset-0 bg-primary-900/85 backdrop-blur-[2px] flex items-center justify-center">
                <div class="text-white text-center px-10 max-w-lg">
                    <img src="{{ asset('logo.png') }}" alt="Logo SMKN 3 Yogyakarta"
                        class="w-24 h-24 mx-auto mb-6 object-contain drop-shadow-lg">
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <span class="text-4xl lg:text-5xl font-black tracking-tight text-white">SIBENKA</span>
                        <span
                            class="text-xs font-black px-2.5 py-1 rounded-md bg-white/20 text-white backdrop-blur-xs border border-white/30 tracking-wider uppercase">
                            SKAGATA
                        </span>
                    </div>
                    <p class="text-lg font-semibold text-primary-100 leading-snug">
                        Sistem Inventaris &amp; Sirkulasi Bengkel
                    </p>
                    <p class="text-sm text-primary-200/80 mt-1 font-medium">
                        SMK Negeri 3 Yogyakarta
                    </p>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan: Area Dinamis untuk Form Login/Register -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8 sm:p-12 shadow-xl z-10">
            <div class="w-full max-w-md">

                <!-- Konten dari file login.blade.php atau register.blade.php akan masuk ke sini -->
                @yield('content')

            </div>
        </div>

    </div>

</body>

</html>
