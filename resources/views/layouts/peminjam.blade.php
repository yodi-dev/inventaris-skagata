<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Peminjam') - Inventaris SMKN 3 Yk</title>

    <!-- Load Tailwind via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- TOP NAVBAR -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Brand / Logo -->
            <div class="flex items-center space-x-3">
                <span class="text-base sm:text-lg font-bold text-primary-600 tracking-wide uppercase">
                    Inventaris SMKN 3 Yk
                </span>
            </div>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('peminjam.katalog.index') }}"
                    class="text-sm font-medium text-primary-600 border-b-2 border-primary-600 py-5">
                    Katalog Barang
                </a>
                <a href="{{ route('peminjam.tiket.index') }}"
                    class="text-sm font-medium text-gray-500 hover:text-gray-900 py-5 transition-colors">
                    Tiket Saya (Riwayat)
                </a>
            </nav>

            <!-- User Profile & Logout -->
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-gray-900">Budi Santoso</p>
                    <p class="text-xs text-gray-500">XI TKJ 1</p>
                </div>
                <!-- Tombol Logout Sederhana -->
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        title="Keluar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Sub-Navbar untuk HP (Mobile Nav Links) -->
        <div class="flex md:hidden border-t border-gray-200 px-4 bg-gray-50">
            <a href="{{ route('peminjam.katalog.index') }}"
                class="flex-1 text-center py-2.5 text-xs font-semibold text-primary-600 border-b-2 border-primary-600">
                Katalog
            </a>
            <a href="{{ route('peminjam.tiket.index') }}"
                class="flex-1 text-center py-2.5 text-xs font-medium text-gray-600">
                Tiket Saya
            </a>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        &copy; 2026 SMK Negeri 3 Yogyakarta. Sistem Inventaris Bengkel.
        </div>

</body>

</html>
