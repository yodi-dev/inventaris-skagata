<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Katalog Peminjam') - SIBENKA SMKN 3 Yogyakarta</title>

    <!-- Load Tailwind & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-slate-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-primary-500 selection:text-white"
    x-data="{
        cartCount: 0,
        init() {
            const updateCount = () => {
                try {
                    const cart = JSON.parse(localStorage.getItem('sibenka_cart') || '[]');
                    this.cartCount = cart.reduce((acc, item) => acc + (item.qty || 1), 0);
                } catch (e) {
                    this.cartCount = 0;
                }
            };
            updateCount();
            window.addEventListener('cart-updated', updateCount);
            window.addEventListener('storage', updateCount);
        }
    }">

    <!-- TOP NAVBAR (Clean & Responsive) -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">

            <!-- Brand / Logo -->
            <a href="{{ route('peminjam.katalog.index') }}" class="flex items-center space-x-2.5 group">
                <div
                    class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary-700 to-primary-500 text-white flex items-center justify-center font-black shadow-xs group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-base font-bold text-gray-900 tracking-tight">SIBENKA</span>
                        <span
                            class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-primary-50 text-primary-700 border border-primary-200">SKAGATA</span>
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium leading-none hidden sm:block">Sistem Inventaris
                        Bengkel</p>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center space-x-1">
                <a href="{{ route('peminjam.katalog.index') }}"
                    class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ request()->is('peminjam/katalog*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    Katalog Barang
                </a>
                <a href="{{ route('peminjam.tiket.index') }}"
                    class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ request()->is('peminjam/tiket*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                    Tiket Peminjaman
                </a>
            </nav>

            <!-- User Profile & Action Buttons -->
            <div class="flex items-center space-x-2 sm:space-x-3">

                <!-- Quick Cart Button (Desktop & Mobile) -->
                <button type="button" @click="window.dispatchEvent(new CustomEvent('toggle-cart'))"
                    class="relative p-2 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors flex items-center justify-center"
                    title="Keranjang Peminjaman">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <!-- Cart Badge Counter -->
                    <span x-show="cartCount > 0" x-transition
                        class="absolute -top-1 -right-1 bg-primary-600 text-white font-bold text-[10px] min-w-4.5 h-4.5 px-1 rounded-full flex items-center justify-center shadow-xs"
                        x-text="cartCount"></span>
                </button>

                <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

                <!-- User Profile Info -->
                <a href="{{ route('profile.edit', ['role' => 'peminjam']) }}"
                    class="flex items-center space-x-2 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 leading-tight">Budi Santoso</p>
                        <p class="text-[11px] text-gray-500 font-medium">Siswa &bull; XI TKJ 1</p>
                    </div>
                    <div
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs border border-primary-200 shadow-2xs">
                        B
                    </div>
                </a>

                <!-- Logout -->
                <a href="{{ route('login') }}"
                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors"
                    title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-7 pb-24 md:pb-8">
        @yield('content')
    </main>

    <!-- FOOTER (Desktop View) -->
    <footer class="bg-white border-t border-gray-200 py-5 text-center text-xs text-gray-500 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; 2026 SMK Negeri 3 Yogyakarta. Sistem Inventaris & Sirkulasi Bengkel (Sibenka).</p>
            <p class="text-gray-400 text-[11px]">Bengkel Aktif: <strong>Teknik Komputer & Jaringan (TKJ)</strong></p>
        </div>
    </footer>

    <!-- MOBILE BOTTOM NAVIGATION (Fixed Bottom Bar - App Feel) -->
    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-2 py-1.5 shadow-lg flex items-center justify-around">
        <!-- Katalog -->
        <a href="{{ route('peminjam.katalog.index') }}"
            class="flex-1 flex flex-col items-center justify-center py-1 rounded-lg transition-colors {{ request()->is('peminjam/katalog*') ? 'text-primary-600 font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            <span class="text-[10px] mt-0.5">Katalog</span>
        </a>

        <!-- Keranjang Pinjam -->
        <button type="button" @click="window.dispatchEvent(new CustomEvent('toggle-cart'))"
            class="flex-1 flex flex-col items-center justify-center py-1 rounded-lg text-gray-500 hover:text-primary-600 relative transition-colors">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span x-show="cartCount > 0"
                    class="absolute -top-1.5 -right-2 bg-primary-600 text-white font-bold text-[9px] min-w-3.5 h-3.5 px-0.5 rounded-full flex items-center justify-center shadow-xs"
                    x-text="cartCount"></span>
            </div>
            <span class="text-[10px] mt-0.5">Keranjang</span>
        </button>

        <!-- Tiket Saya -->
        <a href="{{ route('peminjam.tiket.index') }}"
            class="flex-1 flex flex-col items-center justify-center py-1 rounded-lg transition-colors {{ request()->is('peminjam/tiket*') ? 'text-primary-600 font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                    </path>
                </svg>
                <!-- Dot indicator for active loan -->
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
            </div>
            <span class="text-[10px] mt-0.5">Tiket Saya</span>
        </a>

        <!-- Profil -->
        <a href="{{ route('profile.edit', ['role' => 'peminjam']) }}"
            class="flex-1 flex flex-col items-center justify-center py-1 rounded-lg transition-colors {{ request()->is('profile*') || request()->is('peminjam/profile*') ? 'text-primary-600 font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-[10px] mt-0.5">Profil</span>
        </a>
    </nav>

    @stack('scripts')
</body>

</html>
