@extends('layouts.admin')

@section('title', 'Tambah Data Bengkel & Jurusan')
@section('header_title', 'Master Data Bengkel')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">

        <!-- Breadcrumb & Top Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('superadmin.master.bengkel') }}" class="hover:text-primary-600 transition-colors">Master Bengkel</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">Tambah Bengkel Baru</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Bengkel & Jurusan Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi formulir untuk mendaftarkan data bengkel atau kompetensi keahlian ke sistem.</p>
            </div>

            <a href="{{ route('superadmin.master.bengkel') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium border border-gray-300 shadow-sm transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Form Card -->
        <form id="formTambahBengkel" action="{{ route('superadmin.master.bengkel') }}" method="GET" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            
            <!-- SECTION 1: Identitas Bengkel -->
            <div class="p-6 sm:p-8 border-b border-gray-200 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Identitas Bengkel & Jurusan</h3>
                        <p class="text-xs text-gray-500">Informasi dasar mengenai nama, kode unik, dan program keahlian.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Nama Bengkel -->
                    <div class="md:col-span-2">
                        <label for="nama_bengkel" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Bengkel / Laboratorium <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="nama_bengkel" name="nama_bengkel" required
                                placeholder="Contoh: Bengkel Teknik Komputer & Jaringan"
                                class="w-full rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm placeholder:text-gray-400">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Gunakan nama resmi bengkel atau laboratorium praktik.</p>
                    </div>

                    <!-- Kode Bengkel -->
                    <div>
                        <label for="kode_bengkel" class="block text-sm font-semibold text-gray-700 mb-1">
                            Kode Bengkel <span class="text-red-500">*</span>
                        </label>
                        <div class="flex rounded-lg shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-600 text-xs font-mono font-bold tracking-wider">
                                BGK-
                            </span>
                            <input type="text" id="kode_bengkel" name="kode_bengkel" required
                                placeholder="TKJ"
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm font-mono uppercase">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Prefix otomatis <code class="text-gray-600 font-semibold">BGK-</code> (contoh hasil: BGK-TKJ).</p>
                    </div>

                    <!-- Singkatan / Alias -->
                    <div>
                        <label for="singkatan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Singkatan / Inisial Jurusan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="singkatan" name="singkatan" required
                            placeholder="Contoh: TKJ, TKR, TAV, TITL"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm uppercase">
                        <p class="text-xs text-gray-400 mt-1">Inisial singkat untuk label cetak dan barcode.</p>
                    </div>

                    <!-- Program Keahlian -->
                    <div class="md:col-span-2">
                        <label for="program_keahlian" class="block text-sm font-semibold text-gray-700 mb-1">
                            Bidang / Program Keahlian <span class="text-red-500">*</span>
                        </label>
                        <select id="program_keahlian" name="program_keahlian" required
                            class="w-full rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm">
                            <option value="" disabled selected>-- Pilih Program Keahlian --</option>
                            <option value="Teknologi Informasi & Komunikasi">Teknologi Informasi & Komunikasi (TIK)</option>
                            <option value="Teknologi Manufaktur & Rekayasa">Teknologi Manufaktur & Rekayasa (TMR)</option>
                            <option value="Energi & Pertambangan">Energi & Pertambangan</option>
                            <option value="Seni & Ekonomi Kreatif">Seni & Ekonomi Kreatif</option>
                            <option value="Teknologi Konstruksi & Bangunan">Teknologi Konstruksi & Bangunan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Penanggung Jawab & Toolman -->
            <div class="p-6 sm:p-8 border-b border-gray-200 space-y-6 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Penanggung Jawab & Personil</h3>
                        <p class="text-xs text-gray-500">Tentukan Kepala Bengkel dan staf Toolman yang bertanggung jawab.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Nama Kepala Bengkel -->
                    <div>
                        <label for="kepala_bengkel" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Kepala Bengkel (Guru Produktif) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="kepala_bengkel" name="kepala_bengkel" required
                            placeholder="Contoh: Ahmad Riyadi, S.Kom."
                            class="w-full rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm">
                    </div>

                    <!-- NIP / NUPTK -->
                    <div>
                        <label for="nip_kepala" class="block text-sm font-semibold text-gray-700 mb-1">
                            NIP / NUPTK Kepala Bengkel
                        </label>
                        <input type="text" id="nip_kepala" name="nip_kepala"
                            placeholder="Contoh: 19800512 200501 1 003"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm font-mono">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika bukan ASN / belum memiliki NIP.</p>
                    </div>

                    <!-- Kontak Bengkel -->
                    <div>
                        <label for="kontak_bengkel" class="block text-sm font-semibold text-gray-700 mb-1">
                            No. WhatsApp / Kontak Darurat
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                                +62
                            </div>
                            <input type="tel" id="kontak_bengkel" name="kontak_bengkel"
                                placeholder="812-3456-7890"
                                class="w-full pl-12 rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm">
                        </div>
                    </div>

                    <!-- Penugasan Toolman -->
                    <div>
                        <label for="toolman_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Toolman Utama Penanggung Jawab
                        </label>
                        <select id="toolman_id" name="toolman_id"
                            class="w-full rounded-lg border-gray-300 focus:border-primary-600 focus:ring-primary-600 text-sm shadow-sm">
                            <option value="">-- Belum Ditugaskan / Pilih Nanti --</option>
                            <option value="1">Joko Susilo (Toolman TKJ - Aktif)</option>
                            <option value="2">Rian Ardiansyah (Toolman TKR - Aktif)</option>
                            <option value="3">Agus Setiawan (Toolman Elektronika - Aktif)</option>
                            <option value="4">Bambang Hariyadi (Toolman Mesin - Aktif)</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Dapat dihubungkan dengan akun Toolman di menu Akun Toolman.</p>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS FOOTER -->
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 flex items-center gap-1.5 order-2 sm:order-1">
                    <span class="text-red-500 font-bold">*</span> Menandakan bidang wajib diisi sebelum menyimpan data.
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end order-1 sm:order-2">
                    <a href="{{ route('superadmin.master.bengkel') }}"
                        class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 text-sm font-medium rounded-lg transition-colors shadow-sm text-center">
                        Batal
                    </a>
                    <button type="reset"
                        class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Reset
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-semibold shadow transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Bengkel
                    </button>
                </div>
            </div>

        </form>

        <!-- Live Preview Modal / Toast Notification Script (Frontend Interaction Simulation) -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formTambahBengkel');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const nama = document.getElementById('nama_bengkel').value || 'Bengkel Baru';
                    const kode = document.getElementById('kode_bengkel').value || 'NEW';

                    // Tampilkan notifikasi simulasi
                    alert(`✅ Sukses (Prototipe UI):\nData bengkel "${nama}" (BGK-${kode.toUpperCase()}) berhasil disimulasikan disimpan!\n\nMengalihkan kembali ke daftar bengkel...`);
                    
                    window.location.href = "{{ route('superadmin.master.bengkel') }}";
                });
            });
        </script>

    </div>
@endsection
