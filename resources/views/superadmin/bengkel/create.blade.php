@extends('layouts.admin')

@section('title', 'Tambah Data Bengkel & Jurusan')
@section('header_title', 'Master Data Bengkel')

@section('content')
    <div class="max-w-5xl space-y-6 pb-12">

        <!-- Breadcrumb & Top Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('superadmin.bengkel.index') }}" class="hover:text-primary-600 transition-colors">Master Bengkel</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">Tambah Bengkel Baru</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Bengkel & Jurusan Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi formulir untuk mendaftarkan unit bengkel kejuruan baru ke sistem.</p>
            </div>

            <a href="{{ route('superadmin.bengkel.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium border border-gray-300 shadow-sm transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Alert Kesalahan Validasi -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm shadow-xs">
                <div class="font-bold flex items-center gap-2 mb-1.5">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Mohon periksa kembali formulir Anda:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs pl-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <form id="formTambahBengkel" action="{{ route('superadmin.bengkel.store') }}" method="POST"
            class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            @csrf
            
            <!-- SECTION 1: Identitas Bengkel -->
            <div class="p-6 sm:p-8 border-b border-gray-200 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Identitas Bengkel & Jurusan</h3>
                        <p class="text-xs text-gray-500">Informasi nama resmi bengkel, kode inventaris unik, dan deskripsi operasional.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Nama Bengkel -->
                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Bengkel / Laboratorium <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" required
                            value="{{ old('nama') }}"
                            placeholder="Contoh: Teknik Komputer & Jaringan (TKJ)"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm placeholder:text-gray-400 @error('nama') border-red-500 @enderror">
                        @error('nama')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Nama resmi bengkel praktik atau kompetensi keahlian.</p>
                        @enderror
                    </div>

                    <!-- Kode Bengkel -->
                    <div>
                        <label for="kode" class="block text-sm font-semibold text-gray-700 mb-1">
                            Kode Bengkel <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="kode" name="kode" required
                            value="{{ old('kode') }}"
                            placeholder="Contoh: BGK-TKJ atau TKJ"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm font-mono uppercase @error('kode') border-red-500 @enderror">
                        @error('kode')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Kode unik bengkel (prefix <code class="font-semibold text-gray-600">BGK-</code> akan ditambahkan otomatis jika hanya diisi singkatan).</p>
                        @enderror
                    </div>

                    <!-- Penugasan Staf Toolman -->
                    <div>
                        <label for="toolman_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Staf Toolman Penanggung Jawab <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <select id="toolman_id" name="toolman_id"
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm bg-white @error('toolman_id') border-red-500 @enderror">
                            <option value="">-- Belum Ditugaskan / Pilih Nanti --</option>
                            @foreach ($toolmans as $toolman)
                                <option value="{{ $toolman->id }}" {{ old('toolman_id') == $toolman->id ? 'selected' : '' }}>
                                    {{ $toolman->name }} {{ $toolman->bengkel ? '(Saat ini: ' . $toolman->bengkel->nama . ')' : '(Belum ada bengkel)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('toolman_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Dapat dihubungkan atau ditugaskan ulang kapan saja di menu Akun Toolman.</p>
                        @enderror
                    </div>

                    <!-- Deskripsi Bengkel -->
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-1">
                            Deskripsi / Ruang Lingkup Bengkel <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            placeholder="Tuliskan keterangan mengenai laboratorium, cakupan peralatan, atau catatan ruangan praktik..."
                            class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm shadow-sm placeholder:text-gray-400 @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Keterangan singkat mengenai fasilitas dan peralatan bengkel.</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS FOOTER -->
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 flex items-center gap-1.5 order-2 sm:order-1">
                    <span class="text-red-500 font-bold">*</span> Menandakan bidang wajib diisi sebelum menyimpan data.
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end order-1 sm:order-2">
                    <a href="{{ route('superadmin.bengkel.index') }}"
                        class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 text-sm font-medium rounded-lg transition-colors shadow-sm text-center">
                        Batal
                    </a>
                    <button type="reset"
                        class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Reset
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-semibold shadow transition-all hover:shadow-md cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Data Bengkel
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection
