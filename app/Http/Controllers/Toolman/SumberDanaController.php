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

                // Seed default sumber dana sekolah jika baru dibuat
                $defaultData = [
                    ['kode' => 'BOS', 'nama' => 'BOS Reguler', 'deskripsi' => 'Bantuan Operasional Sekolah Reguler'],
                    ['kode' => 'BOSDA', 'nama' => 'BOS Daerah (BOSDA)', 'deskripsi' => 'Bantuan Operasional Pendidikan Daerah DIY'],
                    ['kode' => 'KOMITE', 'nama' => 'Komite Sekolah', 'deskripsi' => 'Dana Partisipasi Masyarakat / Komite Sekolah'],
                    ['kode' => 'DAK', 'nama' => 'DAK Fisik', 'deskripsi' => 'Dana Alokasi Khusus Fisik Bidang Pendidikan'],
                    ['kode' => 'HIBAH', 'nama' => 'Hibah / CSR Industri', 'deskripsi' => 'Bantuan Hibah Kerjasama Industri / Mitra'],
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
     * Mengambil daftar data sumber dana (JSON untuk modal & dropdown).
     * Bersifat global untuk seluruh bengkel di sekolah.
     */
    public function index(Request $request)
    {
        self::ensureSchemaReady();

        $sumberDanas = SumberDana::withCount('barangs')
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sumberDanas,
        ]);
    }

    /**
     * Menyimpan data sumber dana baru via AJAX modal (Global).
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

        return response()->json([
            'success' => true,
            'message' => "Sumber Dana '{$sumberDana->nama}' berhasil ditambahkan!",
            'data' => $sumberDana,
        ], 201);
    }

    /**
     * Memperbarui data sumber dana via AJAX modal.
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

        return response()->json([
            'success' => true,
            'message' => "Sumber Dana '{$sumberDana->nama}' berhasil diperbarui!",
            'data' => $sumberDana,
        ]);
    }

    /**
     * Menghapus data sumber dana via AJAX modal.
     */
    public function destroy(Request $request, $id)
    {
        self::ensureSchemaReady();

        $sumberDana = SumberDana::withCount('barangs')->findOrFail($id);

        // Proteksi jika sumber dana sedang digunakan oleh barang di bengkel manapun
        if ($sumberDana->barangs_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat menghapus '{$sumberDana->nama}' karena sedang digunakan oleh {$sumberDana->barangs_count} data barang!",
            ], 422);
        }

        $nama = $sumberDana->nama;
        $sumberDana->delete();

        return response()->json([
            'success' => true,
            'message' => "Sumber Dana '{$nama}' berhasil dihapus!",
        ]);
    }
}

