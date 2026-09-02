<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Inventaris SMKN 3 Yk</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-gray-800 font-sans antialiased flex min-h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 flex-col justify-between hidden md:flex z-20 shadow-sm">

        <div class="h-16 flex items-center justify-center border-b border-gray-200 px-4">
            <span class="text-lg font-bold text-primary-600 tracking-wide uppercase">
                Inventaris SMK
            </span>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            <a href="#"
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-primary-50 text-primary-600">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Dashboard
            </a>

            <a href="#"
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                Sirkulasi Barang
            </a>

            <a href="#"
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Manajemen Barang
            </a>
        </nav>

        <div class="p-4 border-t border-gray-200">
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

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

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

            <div class="flex items-center space-x-4 cursor-pointer hover:opacity-80 transition-opacity">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium text-gray-900">Admin Toolman</p>
                    <p class="text-xs text-gray-500">Bengkel TKJ</p>
                </div>
                <div
                    class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200">
                    A
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6">
            @yield('content')
        </main>

    </div>

</body>

</html>
