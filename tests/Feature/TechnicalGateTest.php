<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\Pengadaan;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicalGateTest extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'TKJ', string $nama = 'Teknik Komputer Jaringan'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => 'Bengkel Praktik TKJ',
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'status' => 'aktif',
        ], $attributes));
    }

    /**
     * 1. Usaha mengubah klasifikasi jenis_barang yang sudah disetujui Waka Sarpras (attempted tampering)
     *    pada saat penerimaan fisik DITOLAK tegas demi integritas data persetujuan.
     */
    public function test_rab_receipt_rejects_attempted_tampering_of_approved_item_classification(): void
    {
        $bengkel = $this->createBengkel('TAV', 'Teknik Audio Video');
        $toolman = $this->createUser(['name' => 'Budi Toolman', 'role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Osiloskop Digital Approved',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(2),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
        ]);

        $detail = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null,
            'nama_barang' => 'Digital Storage Oscilloscope Dual Channel',
            'jenis_barang' => 'inventaris', // Disetujui resmi sebagai Inventaris
            'minimum_stok' => 2,
            'spesifikasi' => '100MHz 2 Channel',
            'jumlah' => 2,
            'satuan' => 'Unit',
            'harga_satuan' => 3500000,
        ]);

        // Toolman mencoba mengubah klasifikasi dari 'inventaris' menjadi 'bhp' saat receive
        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id), [
            'items_classification' => [
                $detail->id => [
                    'jenis_barang' => 'bhp', // Percobaan manipulasi klasifikasi!
                    'minimum_stok' => 2,
                ],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString('telah disetujui sebagai Alat Inventaris', $errorMessage);
        $this->assertStringContainsString('tidak dapat diubah saat penerimaan fisik', $errorMessage);

        // Pastikan status pengadaan tidak berubah menjadi selesai
        $pengadaan->refresh();
        $this->assertEquals('approved', $pengadaan->status);

        // Pastikan data detail tidak tertimpa
        $detail->refresh();
        $this->assertEquals('inventaris', $detail->jenis_barang);

        // Pastikan tidak ada barang BHP baru yang terbuat
        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Digital Storage Oscilloscope Dual Channel',
            'jenis_barang' => 'bhp',
        ]);
        $this->assertEquals(0, StockMovement::count());
    }

    /**
     * 2. Usaha mengubah batas minimum stok yang telah disetujui pada RAB saat penerimaan fisik DITOLAK.
     */
    public function test_rab_receipt_rejects_attempted_tampering_of_approved_minimum_stock(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer Jaringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Switch Managed',
            'status' => 'approved',
            'diajukan_pada' => now()->subDay(),
            'direview_oleh' => $waka->id,
            'direview_pada' => now(),
        ]);

        $detail = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null,
            'nama_barang' => 'Switch Gigabit 24 Port Managed',
            'jenis_barang' => 'inventaris',
            'minimum_stok' => 5, // Ditetapkan minimum 5
            'jumlah' => 3,
            'satuan' => 'Unit',
            'harga_satuan' => 2500000,
        ]);

        // Toolman mencoba mengubah minimum_stok menjadi 0
        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id), [
            'items_classification' => [
                $detail->id => [
                    'jenis_barang' => 'inventaris',
                    'minimum_stok' => 0, // Percobaan manipulasi batas minimum
                ],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Batas minimum stok barang', session('error'));
        $this->assertStringContainsString('telah ditetapkan (5)', session('error'));

        $pengadaan->refresh();
        $this->assertEquals('approved', $pengadaan->status);
        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Switch Gigabit 24 Port Managed',
        ]);
    }

    /**
     * 3. Alur valid item legacy tanpa jenis_barang: konfirmasi Toolman divalidasi, disimpan permanen di RAB,
     *    dan aktor pelaksana dicatat dalam audit trail StockMovement.
     */
    public function test_rab_receipt_valid_legacy_path_stores_confirmation_and_records_actor_audit(): void
    {
        $bengkel = $this->createBengkel('TKR', 'Teknik Kendaraan Ringan');
        $toolman = $this->createUser(['name' => 'Joko Toolman', 'role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Perlengkapan Oli Mesin Legacy',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(5),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDays(4),
        ]);

        $detailLegacy = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null,
            'nama_barang' => 'Cairan Pembersih Karburator Carb Cleaner',
            'jenis_barang' => null, // Data lama sebelum migrasi
            'minimum_stok' => null,
            'jumlah' => 12,
            'satuan' => 'Kaleng',
            'harga_satuan' => 45000,
        ]);

        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id), [
            'items_classification' => [
                $detailLegacy->id => [
                    'jenis_barang' => 'bhp',
                    'minimum_stok' => 4,
                ],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Status selesai
        $pengadaan->refresh();
        $this->assertEquals('selesai', $pengadaan->status);

        // Detail terupdate dan tersimpan permanen
        $detailLegacy->refresh();
        $this->assertEquals('bhp', $detailLegacy->jenis_barang);
        $this->assertEquals(4, $detailLegacy->minimum_stok);
        $this->assertNotNull($detailLegacy->barang_id);

        // Barang baru terbuat dengan kode BHP
        $newBarang = Barang::findOrFail($detailLegacy->barang_id);
        $this->assertEquals('bhp', $newBarang->jenis_barang);
        $this->assertStringStartsWith('BHP-TKR-', $newBarang->kode_barang);
        $this->assertEquals(4, $newBarang->minimum_stok);
        $this->assertEquals(12, $newBarang->stok_total);

        // Audit Trail StockMovement mencatat Toolman pelaksana dan konfirmasi klasifikasi
        $movement = StockMovement::where('barang_id', $newBarang->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals($toolman->id, $movement->user_id);
        $this->assertEquals(12, $movement->jumlah);
        $this->assertEquals('stok_masuk', $movement->jenis);
        $this->assertStringContainsString('Klasifikasi awal dikonfirmasi oleh Joko Toolman', $movement->keterangan);
    }

    /**
     * 4. Rute publik registrasi memiliki throttling terhadap abuse (maksimal 6 per menit).
     */
    public function test_registration_route_is_throttled_against_abuse(): void
    {
        $bengkel = $this->createBengkel();

        // Uji throttling pada POST /register (batas 6 request per menit)
        for ($i = 1; $i <= 6; $i++) {
            $this->post('/register', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'jenis_peminjam' => 'siswa',
                'nomor_identitas' => "NIS{$i}",
                'bengkel_id' => $bengkel->id,
            ]);
        }

        // Request ke-7 harus terkena HTTP 429 Too Many Requests
        $responseBlocked = $this->post('/register', [
            'name' => 'User 7',
            'email' => 'user7@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'jenis_peminjam' => 'siswa',
            'nomor_identitas' => 'NIS7',
            'bengkel_id' => $bengkel->id,
        ]);

        $this->assertEquals(429, $responseBlocked->getStatusCode(), 'POST /register must be rate limited to prevent spam/abuse.');
    }
}
