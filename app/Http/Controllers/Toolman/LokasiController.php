<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LokasiController extends Controller
{
    /**
     * Tampilkan daftar lokasi penyimpanan bengkel Toolman.
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = LokasiPenyimpanan::where('bengkel_id', $bengkelId)
            ->withCount('barangs');

        // Pencarian Nama / Kode Lokasi
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Statistik KPI ringkas
        $totalLokasi = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->count();
        $totalTerisi = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->has('barangs')->count();
        $totalKosong = max(0, $totalLokasi - $totalTerisi);

        $lokasis = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $lokasis,
            ]);
        }

        return view('toolman.lokasi.index', compact(
            'lokasis',
            'bengkel',
            'totalLokasi',
            'totalTerisi',
            'totalKosong'
        ));
    }

    /**
     * Simpan data lokasi penyimpanan baru ke database.
     */
    public function store(Request $request)
    {
        $user = $request->user() ?? auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('lokasi_penyimpanans', 'kode')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                }),
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'kode.required' => 'Kode lokasi wajib diisi.',
            'kode.unique' => 'Kode lokasi ini sudah digunakan pada bengkel Anda. Gunakan kode lain.',
            'nama.required' => 'Nama lokasi penyimpanan wajib diisi.',
        ]);

        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkelId,
            'kode' => strtoupper(trim($validated['kode'])),
            'nama' => trim($validated['nama']),
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        // Jika dipanggil via AJAX dari form barang/create
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Lokasi {$lokasi->nama} ({$lokasi->kode}) berhasil ditambahkan!",
                'data' => $lokasi,
            ]);
        }

        return redirect()->route('toolman.lokasi.index')
            ->with('success', "Lokasi penyimpanan {$lokasi->nama} ({$lokasi->kode}) berhasil didaftarkan.");
    }

    /**
     * Perbarui data lokasi penyimpanan.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user() ?? auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $lokasi = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->findOrFail($id);

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('lokasi_penyimpanans', 'kode')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                })->ignore($lokasi->id),
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'kode.required' => 'Kode lokasi wajib diisi.',
            'kode.unique' => 'Kode lokasi ini sudah digunakan pada bengkel Anda.',
            'nama.required' => 'Nama lokasi penyimpanan wajib diisi.',
        ]);

        $lokasi->update([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama' => trim($validated['nama']),
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        return redirect()->route('toolman.lokasi.index')
            ->with('success', "Data lokasi {$lokasi->nama} ({$lokasi->kode}) berhasil diperbarui.");
    }

    /**
     * Hapus lokasi penyimpanan dengan proteksi jika masih ada barang tersimpan.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $lokasi = LokasiPenyimpanan::where('bengkel_id', $bengkelId)
            ->withCount('barangs')
            ->findOrFail($id);

        if ($lokasi->barangs_count > 0) {
            return redirect()->back()
                ->with('error', "Lokasi '{$lokasi->nama}' ({$lokasi->kode}) tidak dapat dihapus karena masih digunakan oleh {$lokasi->barangs_count} barang. Pindahkan barang ke lokasi lain terlebih dahulu.");
        }

        $nama = $lokasi->nama;
        $kode = $lokasi->kode;
        $lokasi->delete();

        return redirect()->route('toolman.lokasi.index')
            ->with('success', "Lokasi penyimpanan {$nama} ({$kode}) berhasil dihapus.");
    }
}

