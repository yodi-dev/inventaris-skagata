<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ToolmanController extends Controller
{
    /**
     * Menampilkan daftar seluruh akun staf toolman bengkel.
     */
    public function index(Request $request)
    {
        $bengkels = Bengkel::orderBy('nama', 'asc')->get();

        $query = User::with('bengkel')
            ->where('role', 'toolman')
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nomor_identitas', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bengkel')) {
            $query->where('bengkel_id', $request->bengkel);
        }

        $toolmans = $query->paginate(15)->withQueryString();

        return view('superadmin.toolman.index', compact('toolmans', 'bengkels'));
    }

    /**
     * Menampilkan formulir pendaftaran akun toolman baru.
     */
    public function create()
    {
        $bengkels = Bengkel::orderBy('nama', 'asc')->get();

        return view('superadmin.toolman.create', compact('bengkels'));
    }

    /**
     * Menyimpan data pendaftaran akun toolman baru ke database.
     */
    public function store(Request $request)
    {
        // Normalisasi alias input form jika ada
        $request->merge([
            'name' => $request->input('name', $request->input('nama_lengkap')),
            'nomor_identitas' => $request->input('nomor_identitas', $request->input('nip')),
            'nomor_wa' => $request->input('nomor_wa', $request->input('no_telepon')),
            'bengkel_id' => $request->input('bengkel_id', $request->input('bengkel_penempatan')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => ['nullable', 'string', 'max:50'],
            'nomor_wa' => ['required', 'string', 'max:25'],
            'bengkel_id' => ['required', 'exists:bengkels,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap staf toolman wajib diisi.',
            'nomor_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'bengkel_id.required' => 'Penempatan bengkel wajib dipilih.',
            'bengkel_id.exists' => 'Bengkel yang dipilih tidak valid.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $toolman = User::create([
            'name' => $validated['name'],
            'nomor_identitas' => $validated['nomor_identitas'] ?? null,
            'nomor_wa' => $validated['nomor_wa'],
            'bengkel_id' => $validated['bengkel_id'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'toolman',
            'status' => 'aktif',
            'jenis_peminjam' => null,
        ]);

        return redirect()->route('superadmin.toolman.index')
            ->with('success', "Akun toolman \"{$toolman->name}\" berhasil didaftarkan.");
    }

    /**
     * Menampilkan formulir edit akun staf toolman.
     */
    public function edit($id)
    {
        $toolman = User::where('role', 'toolman')->findOrFail($id);
        $bengkels = Bengkel::orderBy('nama', 'asc')->get();

        return view('superadmin.toolman.edit', compact('toolman', 'bengkels'));
    }

    /**
     * Memperbarui data akun toolman yang sudah ada di database.
     */
    public function update(Request $request, $id)
    {
        $toolman = User::where('role', 'toolman')->findOrFail($id);

        // Normalisasi alias input form jika ada
        $request->merge([
            'name' => $request->input('name', $request->input('nama_lengkap')),
            'nomor_identitas' => $request->input('nomor_identitas', $request->input('nip')),
            'nomor_wa' => $request->input('nomor_wa', $request->input('no_telepon')),
            'bengkel_id' => $request->input('bengkel_id', $request->input('bengkel_penempatan')),
            'status' => $request->input('status', $request->input('status_akun', $toolman->status)),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => ['nullable', 'string', 'max:50'],
            'nomor_wa' => ['required', 'string', 'max:25'],
            'bengkel_id' => ['required', 'exists:bengkels,id'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($toolman->id)],
            'status' => ['required', 'in:aktif,suspend,nonaktif'],
        ], [
            'name.required' => 'Nama lengkap staf toolman wajib diisi.',
            'nomor_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'bengkel_id.required' => 'Penempatan bengkel wajib dipilih.',
            'bengkel_id.exists' => 'Bengkel yang dipilih tidak valid.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'status.required' => 'Status akun wajib ditentukan.',
        ]);

        $status = in_array($validated['status'], ['nonaktif', 'suspend']) ? 'suspend' : 'aktif';

        $toolman->update([
            'name' => $validated['name'],
            'nomor_identitas' => $validated['nomor_identitas'] ?? null,
            'nomor_wa' => $validated['nomor_wa'],
            'bengkel_id' => $validated['bengkel_id'],
            'email' => $validated['email'],
            'status' => $status,
        ]);

        return redirect()->route('superadmin.toolman.index')
            ->with('success', "Perubahan data akun toolman \"{$toolman->name}\" berhasil disimpan.");
    }

    /**
     * Menghapus akun toolman dari database.
     */
    public function destroy($id)
    {
        $toolman = User::where('role', 'toolman')->findOrFail($id);
        $name = $toolman->name;

        try {
            $toolman->delete();
            return redirect()->route('superadmin.toolman.index')
                ->with('success', "Akun toolman \"{$name}\" berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('superadmin.toolman.index')
                ->with('error', "Gagal menghapus akun toolman: {$e->getMessage()}");
        }
    }

    /**
     * Mereset password akun toolman dari antarmuka Superadmin.
     */
    public function resetPassword(Request $request, $id)
    {
        $toolman = User::where('role', 'toolman')->findOrFail($id);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $toolman->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('superadmin.toolman.index')
            ->with('success', "Password untuk akun toolman \"{$toolman->name}\" berhasil direset.");
    }
}
