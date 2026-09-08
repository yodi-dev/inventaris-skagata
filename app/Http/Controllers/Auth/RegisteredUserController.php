<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $bengkels = Bengkel::orderBy('nama', 'asc')->get();

        return view('auth.register', compact('bengkels'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jenis_peminjam' => ['required', 'string', 'in:siswa,guru'],
            'nomor_identitas' => ['required', 'string', 'max:50'],
            'nomor_wa' => ['nullable', 'string', 'max:20'],
            'bengkel_id' => [
                Rule::requiredIf($request->jenis_peminjam === 'siswa'),
                'nullable',
                'exists:bengkels,id',
            ],
        ], [
            'jenis_peminjam.required' => 'Pilih jenis peminjam (Siswa atau Guru).',
            'jenis_peminjam.in' => 'Pilihan jenis peminjam tidak valid.',
            'nomor_identitas.required' => 'Nomor identitas (NIS / NIP) wajib diisi.',
            'bengkel_id.required' => 'Siswa wajib memilih bengkel.',
            'bengkel_id.exists' => 'Bengkel yang dipilih tidak valid.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'peminjam',
            'jenis_peminjam' => $request->jenis_peminjam,
            'nomor_identitas' => $request->nomor_identitas,
            'nomor_wa' => $request->nomor_wa,
            'bengkel_id' => $request->jenis_peminjam === 'siswa' ? $request->bengkel_id : null,
            'status' => 'menunggu_acc',
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan dari Toolman sebelum dapat digunakan.');
    }
}
