<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BengkelController extends Controller
{
    /**
     * Menampilkan daftar seluruh master data bengkel kejuruan.
     */
    public function index(Request $request)
    {
        $query = Bengkel::with(['users' => function ($q) {
            $q->where('role', 'toolman')->orderBy('name', 'asc');
        }])
            ->withCount([
                'barangs as inventaris_count' => function ($query) {
                    $query->where('jenis_barang', 'inventaris');
                },
                'barangs as bhp_count' => function ($query) {
                    $query->where('jenis_barang', 'bhp');
                },
                'users as toolman_count' => function ($query) {
                    $query->where('role', 'toolman');
                },
            ])
            ->orderBy('nama', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $bengkels = $query->paginate(15)->withQueryString();

        return view('superadmin.bengkel.index', compact('bengkels'));
    }

    /**
     * Menampilkan formulir pendaftaran bengkel kejuruan baru.
     */
    public function create()
    {
        $toolmans = User::where('role', 'toolman')
            ->orderBy('name', 'asc')
            ->get();

        return view('superadmin.bengkel.create', compact('toolmans'));
    }

    /**
     * Menyimpan data bengkel baru ke database.
     */
    public function store(Request $request)
    {
        // Normalisasi alias field dari form jika ada
        $request->merge([
            'nama' => $request->input('nama', $request->input('nama_bengkel')),
            'kode' => $request->input('kode', $request->input('kode_bengkel')),
        ]);

        // Format kode bengkel
        $kodeInput = strtoupper(trim((string) $request->input('kode', '')));
        if ($kodeInput !== '' && !str_starts_with($kodeInput, 'BGK-') && !str_contains($kodeInput, '-')) {
            $kodeInput = 'BGK-' . $kodeInput;
        }
        $request->merge(['kode' => $kodeInput]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:bengkels,kode'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'toolman_id' => ['nullable', 'exists:users,id'],
        ], [
            'nama.required' => 'Nama bengkel / laboratorium wajib diisi.',
            'nama.max' => 'Nama bengkel maksimal 255 karakter.',
            'kode.required' => 'Kode bengkel wajib diisi.',
            'kode.unique' => 'Kode bengkel ini sudah terdaftar di sistem.',
            'kode.max' => 'Kode bengkel maksimal 50 karakter.',
            'toolman_id.exists' => 'Staf Toolman yang dipilih tidak valid.',
        ]);

        $bengkel = Bengkel::create([
            'nama' => trim($validated['nama']),
            'kode' => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ? trim($validated['deskripsi']) : null,
        ]);

        // Hubungkan staf toolman jika dipilih
        if (!empty($validated['toolman_id'])) {
            User::where('id', $validated['toolman_id'])
                ->where('role', 'toolman')
                ->update(['bengkel_id' => $bengkel->id]);
        }

        return redirect()->route('superadmin.bengkel.index')
            ->with('success', "Data bengkel \"{$bengkel->nama}\" ({$bengkel->kode}) berhasil ditambahkan.");
    }

    /**
     * Menampilkan formulir edit data bengkel kejuruan.
     */
    public function edit($id)
    {
        $bengkel = Bengkel::with(['users' => function ($q) {
            $q->where('role', 'toolman')->orderBy('name', 'asc');
        }])
            ->withCount([
                'barangs as inventaris_count' => function ($query) {
                    $query->where('jenis_barang', 'inventaris');
                },
                'barangs as bhp_count' => function ($query) {
                    $query->where('jenis_barang', 'bhp');
                },
                'users as toolman_count' => function ($query) {
                    $query->where('role', 'toolman');
                },
            ])
            ->findOrFail($id);

        $toolmans = User::where('role', 'toolman')
            ->orderBy('name', 'asc')
            ->get();

        return view('superadmin.bengkel.edit', compact('bengkel', 'toolmans'));
    }

    /**
     * Memperbarui data bengkel yang sudah ada di database.
     */
    public function update(Request $request, $id)
    {
        $bengkel = Bengkel::findOrFail($id);

        // Normalisasi alias field dari form jika ada
        $request->merge([
            'nama' => $request->input('nama', $request->input('nama_bengkel')),
            'kode' => $request->input('kode', $request->input('kode_bengkel')),
        ]);

        // Format kode bengkel
        $kodeInput = strtoupper(trim((string) $request->input('kode', '')));
        if ($kodeInput !== '' && !str_starts_with($kodeInput, 'BGK-') && !str_contains($kodeInput, '-')) {
            $kodeInput = 'BGK-' . $kodeInput;
        }
        $request->merge(['kode' => $kodeInput]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', Rule::unique('bengkels', 'kode')->ignore($bengkel->id)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'toolman_id' => ['nullable', 'exists:users,id'],
        ], [
            'nama.required' => 'Nama bengkel / laboratorium wajib diisi.',
            'nama.max' => 'Nama bengkel maksimal 255 karakter.',
            'kode.required' => 'Kode bengkel wajib diisi.',
            'kode.unique' => 'Kode bengkel ini sudah digunakan oleh bengkel lain.',
            'kode.max' => 'Kode bengkel maksimal 50 karakter.',
            'toolman_id.exists' => 'Staf Toolman yang dipilih tidak valid.',
        ]);

        $bengkel->update([
            'nama' => trim($validated['nama']),
            'kode' => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ? trim($validated['deskripsi']) : null,
        ]);

        // Update penugasan toolman jika dipilih
        if (!empty($validated['toolman_id'])) {
            User::where('id', $validated['toolman_id'])
                ->where('role', 'toolman')
                ->update(['bengkel_id' => $bengkel->id]);
        }

        return redirect()->route('superadmin.bengkel.index')
            ->with('success', "Perubahan data bengkel \"{$bengkel->nama}\" berhasil disimpan.");
    }

    /**
     * Menghapus data bengkel dari database.
     */
    public function destroy($id)
    {
        $bengkel = Bengkel::withCount(['barangs', 'peminjamans', 'pengadaans', 'users'])
            ->findOrFail($id);

        $nama = $bengkel->nama;

        // Pengecekan integritas data: cegah penghapusan jika masih ada barang atau transaksi
        if ($bengkel->barangs_count > 0 || $bengkel->peminjamans_count > 0) {
            return redirect()->route('superadmin.bengkel.index')
                ->with('error', "Bengkel \"{$nama}\" tidak dapat dihapus karena masih memiliki {$bengkel->barangs_count} barang inventaris/BHP dan riwayat transaksi terkait.");
        }

        try {
            // Lepaskan ikatan bengkel_id pada user (siswa/toolman) jika ada sebelum dihapus
            User::where('bengkel_id', $bengkel->id)->update(['bengkel_id' => null]);

            $bengkel->delete();

            return redirect()->route('superadmin.bengkel.index')
                ->with('success', "Data bengkel \"{$nama}\" berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('superadmin.bengkel.index')
                ->with('error', "Gagal menghapus data bengkel: {$e->getMessage()}");
        }
    }
}
