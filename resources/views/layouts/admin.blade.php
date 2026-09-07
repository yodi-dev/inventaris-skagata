<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Inventaris SMKN 3 Yk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-gray-800 font-sans antialiased flex min-h-screen overflow-hidden"
    x-data="{
        sidebarOpen: true,
        mobileSidebarOpen: false,
        toggleSidebar() {
            if (window.innerWidth < 768) {
                this.mobileSidebarOpen = !this.mobileSidebarOpen;
            } else {
                this.sidebarOpen = !this.sidebarOpen;
            }
        }
    }">

    <!-- Mobile Sidebar Drawer (Only on screens < md) -->
    <div class="md:hidden" x-cloak>
        <!-- Backdrop Overlay -->
        <div x-show="mobileSidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40"
            @click="mobileSidebarOpen = false"
            style="display: none;">
        </div>

        <!-- Offcanvas Mobile Drawer -->
        <div x-show="mobileSidebarOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white flex flex-col justify-between shadow-2xl border-r border-gray-200"
            style="display: none;">

            <!-- Header & Close Button -->
            <div class="h-16 flex items-center justify-between border-b border-gray-200 px-4 shrink-0">
                <span class="text-lg font-bold text-primary-600 tracking-wide uppercase">
                    Inventaris SMK
                </span>
                <button @click="mobileSidebarOpen = false"
                    class="text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                    aria-label="Tutup Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            @include('layouts.partials.admin-sidebar-menu')

            <!-- Footer & Logout -->
            @include('layouts.partials.admin-sidebar-footer')
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside
        class="w-64 bg-white border-r border-gray-200 flex-col justify-between hidden md:flex z-20 shadow-sm shrink-0"
        :class="sidebarOpen ? 'ml-0' : '-ml-64'"
        :style="sidebarOpen ? 'margin-left: 0;' : 'margin-left: -16rem;'"
        style="transition: margin-left 300ms cubic-bezier(0.4, 0, 0.2, 1);">

        <div class="w-64 flex flex-col justify-between h-full shrink-0">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-center border-b border-gray-200 px-4 shrink-0">
                <span class="text-lg font-bold text-primary-600 tracking-wide uppercase">
                    Inventaris SMK
                </span>
            </div>

            <!-- Navigation Links -->
            @include('layouts.partials.admin-sidebar-menu')

            <!-- Footer & Logout -->
            @include('layouts.partials.admin-sidebar-footer')
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0 w-full">

        <!-- Top Header -->
        <header
            class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-3 sm:px-6 z-10 shadow-sm w-full min-w-0 shrink-0">

            <div class="flex items-center min-w-0 flex-1 mr-2 sm:mr-4">
                <button @click="toggleSidebar()"
                    class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 p-2 -ml-1.5 rounded-lg transition-colors shrink-0 focus:outline-none focus:ring-2 focus:ring-primary-500/20 mr-2 sm:mr-3"
                    :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
                    aria-label="Tutup / Buka Sidebar">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <h2 class="text-base sm:text-xl font-semibold text-gray-800 truncate min-w-0">
                    @yield('header_title', 'Beranda')
                </h2>
            </div>

            <!-- Header Profile Section Dynamically Changing -->
            <div class="flex items-center space-x-3 sm:space-x-4 cursor-pointer hover:opacity-80 transition-opacity shrink-0">
                <div class="hidden sm:block text-right">
                    @if (request()->is('superadmin*') || request('role') === 'superadmin' || (isset($role) && $role === 'superadmin'))
                        <p class="text-sm font-medium text-gray-900">Waka Sarpras</p>
                        <p class="text-xs text-gray-500">Super Administrator</p>
                    @else
                        <p class="text-sm font-medium text-gray-900">Admin Toolman</p>
                        <p class="text-xs text-gray-500">Bengkel TKJ</p>
                    @endif
                </div>
                <div
                    class="h-9 w-9 sm:h-10 sm:w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 text-sm sm:text-base shrink-0 shadow-xs">
                    {{ request()->is('superadmin*') || request('role') === 'superadmin' || (isset($role) && $role === 'superadmin') ? 'W' : 'A' }}
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 min-w-0 w-full">
            @yield('content')
        </main>

    </div>

</body>

</html>
