<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\Pengadaan;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch3Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'TFLM', string $nama = 'Teknik Fabrikasi Logam'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => 'Bengkel untuk pengujian unit Batch 3',
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        static $counter = 200;
        $counter++;

        return User::create(array_merge([
            'name' => "User {$counter}",
            'email' => "user{$counter}@example.com",
            'password' => Hash::make('password123'),
            'role' => 'toolman',
            'status' => 'aktif',
        ], $attributes));
    }

    private function createBarang(Bengkel $bengkel, array $attributes = []): Barang
    {
        static $counter = 200;
        $counter++;

        return Barang::create(array_merge([
            'bengkel_id' => $bengkel->id,
            'kode_barang' => "BRG-B3-{$counter}",
            'nama' => "Barang {$counter}",
            'jenis_barang' => 'inventaris',
            'satuan' => 'Unit',
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 2,
        ], $attributes));
    }

    // =========================================================================
    // 1. SUCCESSFUL RECEIPT - EXISTING ITEM & NEW ITEM
    // =========================================================================

    public function test_successful_receipt_for_existing_item_increments_stock_and_creates_movement(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $barang = $this->createBarang($bengkel, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Alat Presisi',
            'status' => 'approved',
        ]);

        $detail = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $barang->id,
            'nama_barang' => $barang->nama,
            'jumlah' => 5,
            'satuan' => 'Unit',
            'harga_satuan' => 150000,
        ]);

        $response = $this->actingAs($toolman)
            ->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertRedirect(route('toolman.pengadaan.show', $pengadaan->id));
        $response->assertSessionHas('success');

        // Status RAB menjadi selesai
        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'status' => 'selesai',
        ]);

        // Stok bertambah 5 (total 15, tersedia 15)
        $barang->refresh();
        $this->assertEquals(15, $barang->stok_total);
        $this->assertEquals(15, $barang->stok_tersedia);

        // Tercatat StockMovement jenis stok_masuk
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 5,
            'referensi_tipe' => 'pengadaan',
            'referensi_id' => $pengadaan->id,
        ]);
    }

    public function test_successful_receipt_for_new_item_creates_barang_and_links_detail(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Mesin Baru',
            'status' => 'approved',
        ]);

        $detail = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null, // Barang baru
            'nama_barang' => 'Mesin Milling CNC Mini',
            'spesifikasi' => 'Spindle 24000 RPM, 3 Axis',
            'jumlah' => 2,
            'satuan' => 'Unit',
            'harga_satuan' => 25000000,
            'jenis_barang' => 'inventaris',
        ]);

        $response = $this->actingAs($toolman)
            ->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertSessionHas('success');

        // Master barang baru berhasil dibuat
        $newBarang = Barang::where('nama', 'Mesin Milling CNC Mini')
            ->where('bengkel_id', $bengkel->id)
            ->first();

        $this->assertNotNull($newBarang);
        $this->assertEquals(2, $newBarang->stok_total);
        $this->assertEquals(2, $newBarang->stok_tersedia);

        // Detail pengadaan terhubung ke barang baru
        $detail->refresh();
        $this->assertEquals($newBarang->id, $detail->barang_id);

        // Status pengadaan selesai
        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'status' => 'selesai',
        ]);

        // Stock movement tercatat
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $newBarang->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 2,
            'referensi_tipe' => 'pengadaan',
            'referensi_id' => $pengadaan->id,
        ]);
    }

    // =========================================================================
    // 2. REPEATED RECEIPT & CONCURRENCY RE-ENTRY SAFEGUARD
    // =========================================================================

    public function test_repeated_receipt_request_is_rejected_without_duplicate_stock_increment(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $barang = $this->createBarang($bengkel, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Uji Idempotensi Penerimaan',
            'status' => 'approved',
        ]);

        DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $barang->id,
            'nama_barang' => $barang->nama,
            'jumlah' => 10,
            'satuan' => 'Unit',
        ]);

        // Eksekusi penerimaan pertama
        $res1 = $this->actingAs($toolman)
            ->post(route('toolman.pengadaan.receive', $pengadaan->id));
        $res1->assertSessionHas('success');

        $barang->refresh();
        $this->assertEquals(20, $barang->stok_total);
        $this->assertEquals(20, $barang->stok_tersedia);
        $this->assertEquals(1, StockMovement::where('referensi_id', $pengadaan->id)->count());

        // Eksekusi penerimaan kedua (Duplikasi / Concurrency Re-entry)
        $res2 = $this->actingAs($toolman)
            ->post(route('toolman.pengadaan.receive', $pengadaan->id));
        $res2->assertSessionHas('error');

        // Pastikan stok tidak bertambah 2x lipat (tetap 20, bukan 30)
        $barang->refresh();
        $this->assertEquals(20, $barang->stok_total);
        $this->assertEquals(20, $barang->stok_tersedia);
        $this->assertEquals(1, StockMovement::where('referensi_id', $pengadaan->id)->count());
    }

    // =========================================================================
    // 3. INVALID STATUS
    // =========================================================================

    public function test_receipt_is_blocked_for_unapproved_statuses(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $barang = $this->createBarang($bengkel, ['stok_total' => 5, 'stok_tersedia' => 5]);

        $statuses = ['draft', 'pending', 'revisi', 'rejected'];

        foreach ($statuses as $st) {
            $pengadaan = Pengadaan::create([
                'bengkel_id' => $bengkel->id,
                'dibuat_oleh' => $toolman->id,
                'judul' => "RAB Status {$st}",
                'status' => $st,
            ]);

            DetailPengadaan::create([
                'pengadaan_id' => $pengadaan->id,
                'barang_id' => $barang->id,
                'nama_barang' => $barang->nama,
                'jumlah' => 5,
                'satuan' => 'Unit',
            ]);

            $response = $this->actingAs($toolman)
                ->post(route('toolman.pengadaan.receive', $pengadaan->id));

            $response->assertSessionHas('error');

            // Status tidak berubah dan stok tidak bertambah
            $this->assertDatabaseHas('pengadaans', [
                'id' => $pengadaan->id,
                'status' => $st,
            ]);

            $barang->refresh();
            $this->assertEquals(5, $barang->stok_total);
        }
    }

    // =========================================================================
    // 4. CROSS-WORKSHOP ITEM VALIDATION & ATOMIC ROLLBACK
    // =========================================================================

    public function test_receipt_is_rejected_when_referencing_cross_workshop_item(): void
    {
        $bengkelA = $this->createBengkel('TKP', 'Konstruksi Properti');
        $bengkelB = $this->createBengkel('TFLM', 'Fabrikasi Logam');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id]);

        // Barang milik Bengkel B
        $barangB = $this->createBarang($bengkelB, [
            'nama' => 'Barang Khusus Bengkel B',
            'stok_total' => 10,
            'stok_tersedia' => 10,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkelA->id,
            'dibuat_oleh' => $toolmanA->id,
            'judul' => 'Pengadaan Memuat Barang Bengkel Lain',
            'status' => 'approved',
        ]);

        DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $barangB->id, // Barang milik Bengkel B, diajukan di Bengkel A
            'nama_barang' => $barangB->nama,
            'jumlah' => 5,
            'satuan' => 'Unit',
        ]);

        $response = $this->actingAs($toolmanA)
            ->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertSessionHas('error');

        // Status tidak berubah menjadi selesai
        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'status' => 'approved',
        ]);

        // Stok barang bengkel B tidak terpengaruh
        $barangB->refresh();
        $this->assertEquals(10, $barangB->stok_total);
        $this->assertEquals(10, $barangB->stok_tersedia);
        $this->assertEquals(0, StockMovement::where('referensi_id', $pengadaan->id)->count());
    }

    public function test_multi_item_receipt_rolls_back_entirely_if_any_item_fails(): void
    {
        $bengkelA = $this->createBengkel('TKP', 'Konstruksi Properti');
        $bengkelB = $this->createBengkel('TFLM', 'Fabrikasi Logam');
        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id]);

        $barangValid = $this->createBarang($bengkelA, [
            'nama' => 'Barang Valid Bengkel A',
            'stok_total' => 10,
            'stok_tersedia' => 10,
        ]);

        $barangInvalid = $this->createBarang($bengkelB, [
            'nama' => 'Barang Lintas Bengkel B',
            'stok_total' => 20,
            'stok_tersedia' => 20,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkelA->id,
            'dibuat_oleh' => $toolmanA->id,
            'judul' => 'Pengadaan Campuran Valid dan Invalid',
            'status' => 'approved',
        ]);

        // Item 1: Valid
        DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $barangValid->id,
            'nama_barang' => $barangValid->nama,
            'jumlah' => 5,
            'satuan' => 'Unit',
        ]);

        // Item 2: Invalid (Cross-workshop)
        DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $barangInvalid->id,
            'nama_barang' => $barangInvalid->nama,
            'jumlah' => 3,
            'satuan' => 'Unit',
        ]);

        $response = $this->actingAs($toolmanA)
            ->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertSessionHas('error');

        // Rollback penuh: Barang valid TIDAK boleh bertambah stoknya (tetap 10)
        $barangValid->refresh();
        $this->assertEquals(10, $barangValid->stok_total);
        $this->assertEquals(10, $barangValid->stok_tersedia);

        // Barang invalid tetap 20
        $barangInvalid->refresh();
        $this->assertEquals(20, $barangInvalid->stok_total);

        // Status RAB tetap approved
        $this->assertDatabaseHas('pengadaans', [
            'id' => $pengadaan->id,
            'status' => 'approved',
        ]);

        // Tidak ada StockMovement yang tercipta
        $this->assertEquals(0, StockMovement::where('referensi_id', $pengadaan->id)->count());
    }
}
