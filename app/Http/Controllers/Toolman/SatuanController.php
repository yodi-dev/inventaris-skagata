<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\Satuan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SatuanController extends Controller
{
    /**
     * Memastikan tabel satuans sudah siap dan memiliki data bawaan standar bengkel SMK.
     */
    public static function ensureDefaults(): void
    {
        try {
            if (Schema::hasTable('satuans')) {
                if (Satuan::count() === 0) {
                    $defaults = [
                        ['nama' => 'Unit', 'singkatan' => 'unit', 'deskripsi' => 'Satuan untuk mesin, laptop, komputer, dan peralatan utama'],
                        ['nama' => 'Pcs', 'singkatan' => 'pcs', 'deskripsi' => 'Satuan per butir, buah, atau komponen lepasan'],
                        ['nama' => 'Set', 'singkatan' => 'set', 'deskripsi' => 'Satu set perlengkapan lengkap atau tool kit'],
                        ['nama' => 'Meter', 'singkatan' => 'm', 'deskripsi' => 'Satuan panjang untuk kabel, kawat, pipa, atau selang'],
                        ['nama' => 'Roll', 'singkatan' => 'roll', 'deskripsi' => 'Satuan gulungan bahan baku seperti timah atau isolasi'],
                        ['nama' => 'Box', 'singkatan' => 'box', 'deskripsi' => 'Satuan kemasan kotak atau kardus tertutup'],
                        ['nama' => 'Lembar', 'singkatan' => 'lbr', 'deskripsi' => 'Satuan bidang tipis seperti plat seng, PCB, atau mika'],
                        ['nama' => 'Batang', 'singkatan' => 'btg', 'deskripsi' => 'Satuan batangan seperti besi pipa, aluminium, atau kayu'],
                        ['nama' => 'Liter', 'singkatan' => 'L', 'deskripsi' => 'Satuan volume cairan kimia, oli, thinner, atau pelumas'],
                        ['nama' => 'Botol', 'singkatan' => 'btl', 'deskripsi' => 'Kemasan botol cairan kimia, lem perekat, atau flux'],
                        ['nama' => 'Pack', 'singkatan' => 'pack', 'deskripsi' => 'Kemasan bungkusan isi beberapa item'],
                        ['nama' => 'Pasang', 'singkatan' => 'psg', 'deskripsi' => 'Barang berpasangan seperti sarung tangan atau sepatu safety'],
                    ];

                    foreach ($defaults as $item) {
                        Satuan::create($item);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore error jika terjadi race condition
        }
    }

    /**
     * Tampilkan daftar master satuan barang.
     */
    public function index(Request $request)
    {
        self::ensureDefaults();

        $user = $request->user() ?? auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = Satuan::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('singkatan', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Statistik
        $totalSatuan = Satuan::count();

        // Ambil data untuk view
        $satuans = $query->orderBy('nama', 'asc')->paginate(12)->withQueryString();

        // Hitung pemakaian barang per satuan untuk item yang ditampilkan
        foreach ($satuans as $satuan) {
            $satuan->terpakai_count = Barang::where(function ($q) use ($satuan) {
                $q->where('satuan', $satuan->nama);
                if (!empty($satuan->singkatan)) {
                    $q->orWhere('satuan', $satuan->singkatan);
                }
            })->count();
        }

        $satuanTerpakaiCount = Satuan::all()->filter(function ($s) {
            return $s->barang_terhubung_count > 0;
        })->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => Satuan::orderBy('nama', 'asc')->get(),
            ]);
        }

        return view('toolman.satuan.index', compact(
            'satuans',
            'bengkel',
            'totalSatuan',
            'satuanTerpakaiCount'
        ));
    }

    /**
     * Simpan data satuan baru.
     */
    public function store(Request $request)
    {
        self::ensureDefaults();

        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:satuans,nama',
            'singkatan' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'nama.required' => 'Nama satuan wajib diisi.',
            'nama.unique' => 'Satuan dengan nama ini sudah terdaftar.',
            'nama.max' => 'Nama satuan maksimal 100 karakter.',
            'singkatan.max' => 'Singkatan satuan maksimal 20 karakter.',
        ]);

        $satuan = Satuan::create([
            'nama' => trim($validated['nama']),
            'singkatan' => !empty($validated['singkatan']) ? trim($validated['singkatan']) : null,
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Satuan '{$satuan->nama}' berhasil ditambahkan!",
                'data' => $satuan,
            ], 201);
        }

        return redirect()->route('toolman.satuan.index')
            ->with('success', "Satuan '{$satuan->nama}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data satuan.
     */
    public function update(Request $request, $id)
    {
        self::ensureDefaults();

        $satuan = Satuan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:satuans,nama,' . $satuan->id,
            'singkatan' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'nama.required' => 'Nama satuan wajib diisi.',
            'nama.unique' => 'Satuan dengan nama ini sudah digunakan oleh data lain.',
            'nama.max' => 'Nama satuan maksimal 100 karakter.',
        ]);

        $oldNama = $satuan->nama;
        $newNama = trim($validated['nama']);

        $satuan->update([
            'nama' => $newNama,
            'singkatan' => !empty($validated['singkatan']) ? trim($validated['singkatan']) : null,
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        // Opsional: Jika nama satuan diubah, perbarui juga nilai string satuan di tabel barangs
        if ($oldNama !== $newNama) {
            Barang::where('satuan', $oldNama)->update(['satuan' => $newNama]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Satuan '{$satuan->nama}' berhasil diperbarui!",
                'data' => $satuan,
            ]);
        }

        return redirect()->route('toolman.satuan.index')
            ->with('success', "Satuan '{$satuan->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus data satuan jika belum dipakai oleh barang.
     */
    public function destroy(Request $request, $id)
    {
        self::ensureDefaults();

        $satuan = Satuan::findOrFail($id);

        $terpakaiCount = Barang::where(function ($q) use ($satuan) {
            $q->where('satuan', $satuan->nama);
            if (!empty($satuan->singkatan)) {
                $q->orWhere('satuan', $satuan->singkatan);
            }
        })->count();

        if ($terpakaiCount > 0) {
            $msg = "Satuan '{$satuan->nama}' tidak dapat dihapus karena masih digunakan oleh {$terpakaiCount} data barang.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $nama = $satuan->nama;
        $satuan->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Satuan '{$nama}' berhasil dihapus!",
            ]);
        }

        return redirect()->route('toolman.satuan.index')
            ->with('success', "Satuan '{$nama}' berhasil dihapus.");
    }
}
