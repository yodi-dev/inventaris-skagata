@extends('layouts.auth')

@section('title', 'Daftar Akun Peminjam')

@section('content')
    <!-- Header Form -->
    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Buat Akun Peminjam 📝</h2>
        <p class="text-sm text-gray-500">Pendaftaran akun khusus Siswa & Guru SMKN 3 Yogyakarta.</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ jenis: '{{ old('jenis_peminjam', 'siswa') }}' }">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name" placeholder="Nama lengkap sesuai identitas resmi"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                placeholder="nama@email.com"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Pilihan Jenis Peminjam (Siswa / Guru) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Peminjam</label>
            <div class="grid grid-cols-2 gap-3">
                <label
                    class="flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all text-sm font-semibold"
                    :class="jenis === 'siswa'
                        ?
                        'bg-primary-50 border-primary-500 text-primary-700 ring-2 ring-primary-500/20 shadow-xs' :
                        'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <input type="radio" name="jenis_peminjam" value="siswa" x-model="jenis" class="sr-only">
                    <span class="mr-1.5">🎓</span>
                    <span>Siswa</span>
                </label>
                <label
                    class="flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all text-sm font-semibold"
                    :class="jenis === 'guru'
                        ?
                        'bg-primary-50 border-primary-500 text-primary-700 ring-2 ring-primary-500/20 shadow-xs' :
                        'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <input type="radio" name="jenis_peminjam" value="guru" x-model="jenis" class="sr-only">
                    <span class="mr-1.5">👨‍🏫</span>
                    <span>Guru / Pengajar</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('jenis_peminjam')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Nomor Identitas Dinamis (NIS / NIP) -->
        <div>
            <label for="nomor_identitas" class="block text-sm font-medium text-gray-700">
                <span x-text="jenis === 'siswa' ? 'Nomor Induk Siswa (NIS)' : 'Nomor Induk Pegawai (NIP)'"></span>
            </label>
            <input id="nomor_identitas" type="text" name="nomor_identitas" value="{{ old('nomor_identitas') }}" required
                :placeholder="jenis === 'siswa' ? 'Contoh: 12345' : 'Contoh: 198001012010011001'"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm font-mono">
            <x-input-error :messages="$errors->get('nomor_identitas')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Pilihan Bengkel (Hanya jika Siswa) -->
        <div x-show="jenis === 'siswa'" x-transition class="space-y-1">
            <label for="bengkel_id" class="block text-sm font-medium text-gray-700">
                Bengkel Kejuruan <span class="text-red-500">*</span>
            </label>
            <select id="bengkel_id" name="bengkel_id" :required="jenis === 'siswa'"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
                <option value="">-- Pilih Bengkel Kejuruan --</option>
                @foreach ($bengkels as $bengkel)
                    <option value="{{ $bengkel->id }}" {{ old('bengkel_id') == $bengkel->id ? 'selected' : '' }}>
                        {{ $bengkel->nama }} ({{ $bengkel->kode }})
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-gray-400">Akun siswa akan terikat pada bengkel yang dipilih.</p>
            <x-input-error :messages="$errors->get('bengkel_id')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- No. WhatsApp / HP -->
        <div>
            <label for="nomor_wa" class="block text-sm font-medium text-gray-700">
                No. WhatsApp / HP <span class="text-xs text-gray-400 font-normal">(Opsional)</span>
            </label>
            <input id="nomor_wa" type="text" name="nomor_wa" value="{{ old('nomor_wa') }}"
                placeholder="Contoh: 081234567890"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
            <x-input-error :messages="$errors->get('nomor_wa')" class="mt-1 text-red-500 text-xs" />
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    placeholder="Min. 8 karakter"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                    Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" placeholder="Ulangi password"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition duration-150 sm:text-sm">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-xs" />
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="pt-3">
            <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                Daftar Sebagai Peminjam
            </button>
        </div>
    </form>

    <!-- Link kembali ke Login -->
    <div class="mt-6 text-center text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
            Masuk di sini
        </a>
    </div>
@endsection
