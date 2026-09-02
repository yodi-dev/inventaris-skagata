<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Inventaris SMKN 3 Yk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-gray-800 font-sans antialiased flex min-h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex-col justify-between hidden md:flex z-20 shadow-sm">

        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-gray-200 px-4">
            <span class="text-lg font-bold text-primary-600 tracking-wide uppercase">
                Inventaris SMK
            </span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            @if (request()->is('superadmin*'))
                <!-- MENU SUPER ADMIN (WAKA SARPRAS) -->
                <a href="/superadmin/dashboard"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('superadmin/dashboard') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>

                <a href="/superadmin/pengadaan"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('superadmin/pengadaan*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Persetujuan Pengadaan
                </a>

                <!-- Sub-menu Laporan -->
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Laporan</p>
                </div>
                <a href="/superadmin/laporan/mutasi"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('superadmin/laporan/mutasi') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Mutasi Aset
                </a>
                <a href="/superadmin/laporan/konsumsi"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('superadmin/laporan/konsumsi') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Konsumsi Bahan
                </a>

                <!-- Sub-menu Master Data -->
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</p>
                </div>
                <a href="/superadmin/master/bengkel"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('superadmin/master/bengkel') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Data Bengkel
                </a>
                <a href="/superadmin/master/toolman"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('superadmin/master/toolman') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Akun Toolman
                </a>
            @else
                <!-- MENU ADMIN BENGKEL (TOOLMAN) -->
                <a href="/toolman/dashboard"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('toolman/dashboard') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>

                <a href="/toolman/barang"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('toolman/barang*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Manajemen Barang
                </a>

                <!-- Sub-menu Sirkulasi -->
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sirkulasi</p>
                </div>
                <a href="/toolman/sirkulasi/peminjaman"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('toolman/sirkulasi/peminjaman') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Persetujuan Pinjam
                </a>
                <a href="/toolman/sirkulasi/pengembalian"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('toolman/sirkulasi/pengembalian') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    Pengembalian
                </a>

                <!-- Menu Lainnya Toolman -->
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lainnya</p>
                </div>
                <a href="/toolman/pengadaan/create"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('toolman/pengadaan*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ajukan Pengadaan
                </a>
                <a href="/toolman/users"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->is('toolman/users*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Manajemen Peminjam
                </a>
            @endif

        </nav>

        <!-- Pengaturan Profil & Logout di bawah Sidebar -->
        <div class="p-4 border-t border-gray-200 space-y-2">
            <a href="/profile/edit"
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Pengaturan
            </a>
            <form method="POST" action="#">
                @csrf
                <button type="submit"
                    class="w-full flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Top Header -->
        <header
            class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-10 shadow-sm">

            <div class="flex items-center">
                <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <h2 class="text-xl font-semibold text-gray-800 truncate">
                    @yield('header_title', 'Beranda')
                </h2>
            </div>

            <!-- Header Profile Section Dynamically Changing -->
            <div class="flex items-center space-x-4 cursor-pointer hover:opacity-80 transition-opacity">
                <div class="hidden sm:block text-right">
                    @if (request()->is('superadmin*'))
                        <p class="text-sm font-medium text-gray-900">Waka Sarpras</p>
                        <p class="text-xs text-gray-500">Super Administrator</p>
                    @else
                        <p class="text-sm font-medium text-gray-900">Admin Toolman</p>
                        <p class="text-xs text-gray-500">Bengkel TKJ</p>
                    @endif
                </div>
                <div
                    class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200">
                    {{ request()->is('superadmin*') ? 'W' : 'A' }}
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6">
            @yield('content')
        </main>

    </div>

</body>

</html>
