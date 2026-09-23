<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\SumberDana;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SumberDanaController extends Controller
{
    /**
     * Memastikan tabel sumber_danas dan kolom barangs.sumber_dana_id sudah siap di database.
     */
    public static function ensureSchemaReady(): void
    {
        try {
            if (!Schema::hasTable('sumber_danas')) {
                Schema::create('sumber_danas', function (Blueprint $table) {
                    $table->id();
                    $table->string('kode')->nullable();
                    $table->string('nama');
                    $table->text('deskripsi')->nullable();
                    $table->timestamps();
                });
            }

            if (SumberDana::count() === 0) {
                // Seed default sumber dana sekolah jika masih kosong
                $defaultData = [
                    ['kode' => 'BOS', 'nama' => 'BOS Reguler', 'deskripsi' => 'Bantuan Operasional Sekolah Reguler'],
                    ['kode' => 'BOSDA', 'nama' => 'BOS Daerah (BOSDA)', 'deskripsi' => 'Bantuan Operasional Pendidikan Daerah DIY'],
                    ['kode' => 'KOMITE', 'nama' => 'Komite Sekolah', 'deskripsi' => 'Dana Partisipasi Masyarakat / Komite Sekolah'],
                    ['kode' => 'DAK', 'nama' => 'DAK Fisik', 'deskripsi' => 'Dana Alokasi Khusus Fisik Bidang Pendidikan'],
                    ['kode' => 'HIBAH', 'nama' => 'Hibah / CSR Industri', 'deskripsi' => 'Bantuan Hibah Kerjasama Industri / Mitra Perusahaan'],
                ];
                foreach ($defaultData as $item) {
                    SumberDana::firstOrCreate(['nama' => $item['nama']], $item);
                }
            }

            if (Schema::hasTable('barangs') && !Schema::hasColumn('barangs', 'sumber_dana_id')) {
                Schema::table('barangs', function (Blueprint $table) {
                    $table->foreignId('sumber_dana_id')
                        ->nullable()
                        ->after('lokasi_penyimpanan_id')
                        ->constrained('sumber_danas')
                        ->nullOnDelete();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika sudah ada atau terjadi race condition
        }
    }

    /**
     * Mengambil daftar data sumber dana (Halaman View atau JSON).
     */
    public function index(Request $request)
    {
        self::ensureSchemaReady();

        $user = $request->user() ?? auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = SumberDana::withCount('barangs');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Jika request via AJAX / API (misal dari form barang create/edit)
        if ($request->wantsJson() || $request->ajax()) {
            $sumberDanas = $query->orderBy('nama', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $sumberDanas,
            ]);
        }

        // Statistik
        $totalSumberDana = SumberDana::count();
        $danaTerpakaiCount = SumberDana::has('barangs')->count();

        $sumberDanas = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();

        return view('toolman.sumber-dana.index', compact(
            'sumberDanas',
            'bengkel',
            'totalSumberDana',
            'danaTerpakaiCount'
        ));
    }

    /**
     * Menyimpan data sumber dana baru.
     */
    public function store(Request $request)
    {
        self::ensureSchemaReady();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'nama.required' => 'Nama sumber dana wajib diisi.',
            'nama.max' => 'Nama sumber dana maksimal 255 karakter.',
            'kode.max' => 'Kode sumber dana maksimal 50 karakter.',
        ]);

        $sumberDana = SumberDana::create([
            'kode' => !empty($validated['kode']) ? strtoupper(trim($validated['kode'])) : null,
            'nama' => trim($validated['nama']),
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        $sumberDana->loadCount('barangs');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sumber Dana '{$sumberDana->nama}' berhasil ditambahkan!",
                'data' => $sumberDana,
            ], 201);
        }

        return redirect()->route('toolman.sumber-dana.index')
            ->with('success', "Sumber Dana '{$sumberDana->nama}' berhasil ditambahkan.");
    }

    /**
     * Memperbarui data sumber dana.
     */
    public function update(Request $request, $id)
    {
        self::ensureSchemaReady();

        $sumberDana = SumberDana::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'nama.required' => 'Nama sumber dana wajib diisi.',
            'nama.max' => 'Nama sumber dana maksimal 255 karakter.',
        ]);

        $sumberDana->update([
            'kode' => !empty($validated['kode']) ? strtoupper(trim($validated['kode'])) : null,
            'nama' => trim($validated['nama']),
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        $sumberDana->loadCount('barangs');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sumber Dana '{$sumberDana->nama}' berhasil diperbarui!",
                'data' => $sumberDana,
            ]);
        }

        return redirect()->route('toolman.sumber-dana.index')
            ->with('success', "Sumber Dana '{$sumberDana->nama}' berhasil diperbarui.");
    }

    /**
     * Menghapus data sumber dana.
     */
    public function destroy(Request $request, $id)
    {
        self::ensureSchemaReady();

        $sumberDana = SumberDana::withCount('barangs')->findOrFail($id);

        // Proteksi jika sumber dana sedang digunakan oleh barang di bengkel manapun
        if ($sumberDana->barangs_count > 0) {
            $msg = "Tidak dapat menghapus '{$sumberDana->nama}' karena sedang digunakan oleh {$sumberDana->barangs_count} data barang!";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $nama = $sumberDana->nama;
        $sumberDana->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sumber Dana '{$nama}' berhasil dihapus!",
            ]);
        }

        return redirect()->route('toolman.sumber-dana.index')
            ->with('success', "Sumber Dana '{$nama}' berhasil dihapus.");
    }
}
