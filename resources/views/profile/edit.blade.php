@extends('layouts.admin')
<!-- Catatan: Untuk prototype kita pakai layout admin dulu. Di tahap integrasi nanti, kamu bisa bikin kondisional layout berdasarkan role pengguna -->

@section('content')
    <div class="min-h-screen bg-slate-50 p-6 flex justify-center">
        <!-- Container dibatasi max-width agar form tidak terlalu melar di layar besar -->
        <div class="w-full max-w-4xl">

            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Pengaturan Akun</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
            </div>

            <!-- Section 1: Informasi Profil -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-8 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Informasi Profil</h2>
                    <p class="text-sm text-gray-500">Perbarui nama, kontak, dan foto profil Anda.</p>
                </div>

                <div class="p-6">
                    <form action="" method="POST" class="space-y-6">
                        <!-- Foto Profil -->
                        <div class="flex items-center gap-6">
                            <div
                                class="h-24 w-24 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-3xl border-4 border-white shadow-md relative group overflow-hidden">
                                <!-- Inisial -->
                                <span>AR</span>
                                <!-- Overlay Hover untuk ganti foto -->
                                <div
                                    class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center cursor-pointer transition-all">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <button type="button"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm mb-1">
                                    Ubah Foto
                                </button>
                                <p class="text-xs text-gray-500">JPG, GIF atau PNG maksimal 2MB.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" value="Ahmad Riyadi, S.Kom."
                                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all">
                            </div>

                            <!-- NIP / NIS -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIP / NIS (Siswa)</label>
                                <input type="text" value="198005122005011003"
                                    class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed focus:border-gray-300 focus:ring-0 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all"
                                    readonly title="NIP/NIS tidak dapat diubah">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" value="ahmad.riyadi@smkn3yk.sch.id"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all">
                            </div>

                            <!-- No HP -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                                <input type="text" value="081234567890"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="px-6 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Section 2: Ubah Password -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Keamanan & Password</h2>
                    <p class="text-sm text-gray-500">Pastikan akun Anda menggunakan password yang panjang dan acak agar
                        tetap aman.</p>
                </div>

                <div class="p-6">
                    <form action="" method="POST" class="space-y-6">
                        <!-- Max width dibatasi karena form password tidak perlu terlalu lebar -->
                        <div class="max-w-xl space-y-5">
                            <!-- Password Lama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                <input type="password"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all">
                            </div>

                            <!-- Password Baru -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all">
                                <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
                            </div>

                            <!-- Konfirmasi Password Baru -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                <input type="password"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2.5 px-3 border shadow-sm outline-none transition-all">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                                Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
