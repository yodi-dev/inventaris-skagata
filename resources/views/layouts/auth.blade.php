<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title dinamis per halaman -->
    <title>@yield('title', 'Autentikasi') - Inventaris SMKN 3 Yk</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-slate-50">

    <div class="min-h-screen flex">

        <!-- Bagian Kiri: Gambar & Branding (Tetap/Statis) -->
        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center relative"
            style="background-image: url('https://images.unsplash.com/photo-1580582932707-520aed937b7b?q=80&w=1000&auto=format&fit=crop');">

            <div class="absolute inset-0 bg-primary-900/80 flex items-center justify-center">
                <div class="text-white text-center px-10">
                    <h1 class="text-5xl font-extrabold mb-4 tracking-tight">Sistem Inventaris</h1>
                    <p class="text-xl font-medium text-primary-100">SMK Negeri 3 Yogyakarta</p>
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
