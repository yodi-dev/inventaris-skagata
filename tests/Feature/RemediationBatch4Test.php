<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPeminjaman;
use App\Models\LokasiPenyimpanan;
use App\Models\Peminjaman;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch4Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'TKJ', string $nama = 'Teknik Komputer dan Jaringan'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => 'Bengkel untuk pengujian Remediation Batch 4',
        ]);
    }

    private function createLokasi(Bengkel $bengkel, string $kode = 'RAK-01', string $nama = 'Rak Utama'): LokasiPenyimpanan
    {
        return LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => $kode,
            'nama' => $nama,
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        static $counter = 300;
        $counter++;

        return User::create(array_merge([
            'name' => "User {$counter}",
            'email' => "user{$counter}@example.com",
            'password' => Hash::make('password123'),
            'role' => 'toolman',
            'status' => 'aktif',
        ], $attributes));
    }

    private function createBarang(Bengkel $bengkel, LokasiPenyimpanan $lokasi, array $attributes = []): Barang
    {
        static $counter = 300;
        $counter++;

        return Barang::create(array_merge([
            'bengkel_id' => $bengkel->id,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'kode_barang' => "BRG-B4-{$counter}",
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

    public function test_lost_item_return_does_not_double_deduct_stock_in_print_kartu(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser(['role' => 'peminjam']);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        // 1. Stok Masuk Awal
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal barang baru',
            'created_at' => now()->subDays(5),
        ]);

        // 2. Peminjaman 2 unit
        $barang->update([
            'stok_tersedia' => 8,
            'stok_dipinjam' => 2,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDays(2),
            'status' => 'active',
            'keperluan' => 'Praktikum Jaringan',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'peminjaman',
            'jumlah' => 2,
            'referensi_tipe' => 'peminjamans',
            'referensi_id' => $peminjaman->id,
            'keterangan' => 'Peminjaman alat #TRX-0001',
            'created_at' => now()->subDays(2),
        ]);

        // 3. Pengembalian: 1 kembali baik, 1 hilang
        // Sesuai PRD 3.6: stok_tersedia bertambah 1 (jadi 9), stok_total berkurang 1 (jadi 9), dipinjam jadi 0
        $barang->update([
            'stok_tersedia' => 9,
            'stok_dipinjam' => 0,
            'stok_total' => 9,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'pengembalian_baik',
            'jumlah' => 1,
            'referensi_tipe' => 'peminjamans',
            'referensi_id' => $peminjaman->id,
            'keterangan' => 'Pengembalian barang kondisi baik #TRX-0001',
            'created_at' => now()->subDay(),
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'barang_hilang',
            'jumlah' => 1,
            'referensi_tipe' => 'peminjamans',
            'referensi_id' => $peminjaman->id,
            'keterangan' => 'Barang hilang pada tiket peminjaman #TRX-0001',
            'created_at' => now()->subDay(),
        ]);

        // Cek kartu barang
        $response = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $response->assertOk();

        $movementRows = $response->viewData('movementRows');
        $this->assertCount(4, $movementRows);

        // Row 1: Masuk awal
        $this->assertEquals(10, $movementRows[0]['masuk_baik']);
        $this->assertEquals(10, $movementRows[0]['sisa_baik']);

        // Row 2: Pinjam 2
        $this->assertEquals(2, $movementRows[1]['keluar_baik']);
        $this->assertEquals(8, $movementRows[1]['sisa_baik']);

        // Row 3: Kembali baik 1
        $this->assertEquals(1, $movementRows[2]['masuk_baik']);
        $this->assertEquals(9, $movementRows[2]['sisa_baik']);

        // Row 4: Hilang 1 -> TIDAK boleh mengurangi saldo_baik lagi (mencegah double deduction)
        $this->assertSame('', $movementRows[4 - 1]['keluar_baik']);
        $this->assertEquals(9, $movementRows[3]['sisa_baik']);
        $this->assertEquals(0, $movementRows[3]['sisa_rusak']);

        // Saldo akhir kartu barang harus identik dengan stok fisik di database
        $this->assertEquals($barang->fresh()->stok_tersedia, $movementRows[3]['sisa_baik']);
    }

    public function test_positive_stock_adjustment_creates_movement_and_reconciles_card(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(3),
        ]);

        // Toolman menambah stok master dari 10 menjadi 15 (+5)
        $response = $this->actingAs($toolman)->put(route('toolman.barang.update', $barang->id), [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'satuan' => 'Unit',
            'stok_baik' => 15,
            'stok_rusak_ringan' => 0,
            'stok_rusak_berat' => 0,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));

        $barang->refresh();
        $this->assertEquals(15, $barang->stok_total);
        $this->assertEquals(15, $barang->stok_tersedia);

        // Verifikasi StockMovement tercatat
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 5,
            'referensi_tipe' => 'penyesuaian_tambah',
        ]);

        // Verifikasi pada kartu barang
        $printResponse = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $printResponse->assertOk();

        $rows = $printResponse->viewData('movementRows');
        $this->assertCount(2, $rows);

        // Baris penyesuaian harus masuk sebagai masuk_baik = 5, sisa_baik = 15
        $this->assertEquals(5, $rows[1]['masuk_baik']);
        $this->assertSame('', $rows[1]['keluar_baik']);
        $this->assertEquals(15, $rows[1]['sisa_baik']);
    }

    public function test_negative_stock_adjustment_creates_movement_and_reconciles_card(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(3),
        ]);

        // Toolman mengurangi stok master dari 10 menjadi 6 (-4) karena opname minus
        $response = $this->actingAs($toolman)->put(route('toolman.barang.update', $barang->id), [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'satuan' => 'Unit',
            'stok_baik' => 6,
            'stok_rusak_ringan' => 0,
            'stok_rusak_berat' => 0,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));

        $barang->refresh();
        $this->assertEquals(6, $barang->stok_total);
        $this->assertEquals(6, $barang->stok_tersedia);

        // Verifikasi StockMovement tercatat sebagai penyesuaian berkurang
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 4,
            'referensi_tipe' => 'penyesuaian_kurang',
        ]);

        // Verifikasi kartu barang: penyesuaian negatif TIDAK boleh dihitung menambah stok (bug abs)
        $printResponse = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $printResponse->assertOk();

        $rows = $printResponse->viewData('movementRows');
        $this->assertCount(2, $rows);

        // Baris penyesuaian harus dicatat sebagai keluar_baik = 4, bukan masuk_baik
        $this->assertSame('', $rows[1]['masuk_baik']);
        $this->assertEquals(4, $rows[1]['keluar_baik']);
        $this->assertEquals(6, $rows[1]['sisa_baik']);
        $this->assertEquals($barang->stok_tersedia, $rows[1]['sisa_baik']);
    }

    public function test_manual_transfer_available_to_damaged_without_changing_total_stock(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(3),
        ]);

        // Toolman memindahkan 3 unit dari tersedia ke rusak (total stok tetap 10)
        $response = $this->actingAs($toolman)->put(route('toolman.barang.update', $barang->id), [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'satuan' => 'Unit',
            'stok_baik' => 7,
            'stok_rusak_ringan' => 3,
            'stok_rusak_berat' => 0,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));

        $barang->refresh();
        $this->assertEquals(10, $barang->stok_total);
        $this->assertEquals(7, $barang->stok_tersedia);
        $this->assertEquals(3, $barang->stok_rusak);

        // Verifikasi audit mutasi alih kondisi tercatat
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 3,
            'referensi_tipe' => 'alih_kondisi_rusak',
        ]);

        // Verifikasi kartu barang: keluar_baik = 3, masuk_rusak = 3, sisa_baik = 7, sisa_rusak = 3
        $printResponse = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $printResponse->assertOk();

        $rows = $printResponse->viewData('movementRows');
        $this->assertCount(2, $rows);

        $this->assertEquals(3, $rows[1]['keluar_baik']);
        $this->assertEquals(3, $rows[1]['masuk_rusak']);
        $this->assertEquals(7, $rows[1]['sisa_baik']);
        $this->assertEquals(3, $rows[1]['sisa_rusak']);
    }

    public function test_manual_transfer_damaged_to_available_perbaikan_without_changing_total_stock(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 6,
            'stok_dipinjam' => 0,
            'stok_rusak' => 4,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(5),
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 4,
            'referensi_tipe' => 'alih_kondisi_rusak',
            'keterangan' => 'Pengalihan kondisi stok: Baik ke Rusak (-4 baik, +4 rusak)',
            'created_at' => now()->subDays(3),
        ]);

        // Toolman berhasil memperbaiki 3 unit (rusak berkurang dari 4 jadi 1, baik naik dari 6 jadi 9)
        $response = $this->actingAs($toolman)->put(route('toolman.barang.update', $barang->id), [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'satuan' => 'Unit',
            'stok_baik' => 9,
            'stok_rusak_ringan' => 1,
            'stok_rusak_berat' => 0,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));

        $barang->refresh();
        $this->assertEquals(10, $barang->stok_total);
        $this->assertEquals(9, $barang->stok_tersedia);
        $this->assertEquals(1, $barang->stok_rusak);

        // Verifikasi audit mutasi perbaikan tercatat
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'perbaikan',
            'jumlah' => 3,
            'referensi_tipe' => 'alih_kondisi_baik',
        ]);

        // Verifikasi kartu barang: masuk_baik = 3, keluar_rusak = 3, sisa_baik = 9, sisa_rusak = 1
        $printResponse = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $printResponse->assertOk();

        $rows = $printResponse->viewData('movementRows');
        $this->assertCount(3, $rows);

        $this->assertEquals(3, $rows[2]['masuk_baik']);
        $this->assertEquals(3, $rows[2]['keluar_rusak']);
        $this->assertEquals(9, $rows[2]['sisa_baik']);
        $this->assertEquals(1, $rows[2]['sisa_rusak']);
    }

    public function test_multi_movement_running_balance_reconciliation(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser(['role' => 'peminjam']);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 20,
            'stok_tersedia' => 20,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        // 1. Stok Masuk 20
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 20,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(10),
        ]);

        // 2. Peminjaman 6
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'peminjaman',
            'jumlah' => 6,
            'keterangan' => 'Pinjam 6 unit',
            'created_at' => now()->subDays(8),
        ]);

        // 3. Pengembalian: 3 baik, 2 rusak, 1 hilang
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'pengembalian_baik',
            'jumlah' => 3,
            'keterangan' => 'Kembali baik 3 unit',
            'created_at' => now()->subDays(6),
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'pengembalian_rusak',
            'jumlah' => 2,
            'keterangan' => 'Kembali rusak 2 unit',
            'created_at' => now()->subDays(6),
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'barang_hilang',
            'jumlah' => 1,
            'keterangan' => 'Hilang 1 unit saat pinjam',
            'created_at' => now()->subDays(6),
        ]);

        // 4. Perbaikan 1 unit rusak ke baik
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'perbaikan',
            'jumlah' => 1,
            'referensi_tipe' => 'alih_kondisi_baik',
            'keterangan' => 'Perbaikan alat: Pengalihan kondisi dari rusak ke baik (+1 baik, -1 rusak)',
            'created_at' => now()->subDays(4),
        ]);

        // 5. Penyesuaian negatif opname (-2)
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 2,
            'referensi_tipe' => 'penyesuaian_kurang',
            'keterangan' => 'Penyesuaian stok master oleh Toolman (-2)',
            'created_at' => now()->subDays(3),
        ]);

        // 6. Penyesuaian positif (+4)
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 4,
            'referensi_tipe' => 'penyesuaian_tambah',
            'keterangan' => 'Penyesuaian stok master oleh Toolman (+4)',
            'created_at' => now()->subDays(2),
        ]);

        // 7. Alih kondisi 2 unit baik ke rusak
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 2,
            'referensi_tipe' => 'alih_kondisi_rusak',
            'keterangan' => 'Pengalihan kondisi stok: Baik ke Rusak (-2 baik, +2 rusak)',
            'created_at' => now()->subDay(),
        ]);

        // Update model barang agar mencerminkan akumulasi historis ini:
        // Masuk 20 -> Pinjam 6 (sisa 14) -> Kembali baik 3 (sisa 17), rusak 2, hilang 1 (total jadi 19)
        // -> Perbaikan 1 (baik 18, rusak 1) -> Kurang 2 (baik 16, total 17) -> Tambah 4 (baik 20, total 21)
        // -> Baik ke Rusak 2 (baik 18, rusak 3, total 21)
        $barang->update([
            'stok_tersedia' => 18,
            'stok_rusak' => 3,
            'stok_dipinjam' => 0,
            'stok_total' => 21,
        ]);

        $response = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $response->assertOk();

        $rows = $response->viewData('movementRows');
        $this->assertCount(9, $rows);

        // Row 0: Masuk 20 -> Saldo Baik 20, Rusak 0
        $this->assertEquals(20, $rows[0]['sisa_baik']);
        $this->assertEquals(0, $rows[0]['sisa_rusak']);

        // Row 1: Pinjam 6 -> Saldo Baik 14, Rusak 0
        $this->assertEquals(14, $rows[1]['sisa_baik']);
        $this->assertEquals(0, $rows[1]['sisa_rusak']);

        // Row 2: Kembali Baik 3 -> Saldo Baik 17, Rusak 0
        $this->assertEquals(17, $rows[2]['sisa_baik']);
        $this->assertEquals(0, $rows[2]['sisa_rusak']);

        // Row 3: Kembali Rusak 2 -> Saldo Baik 17, Rusak 2
        $this->assertEquals(17, $rows[3]['sisa_baik']);
        $this->assertEquals(2, $rows[3]['sisa_rusak']);

        // Row 4: Hilang 1 -> Saldo Baik 17, Rusak 2 (no double deduction)
        $this->assertEquals(17, $rows[4]['sisa_baik']);
        $this->assertEquals(2, $rows[4]['sisa_rusak']);

        // Row 5: Perbaikan 1 -> Saldo Baik 18, Rusak 1
        $this->assertEquals(18, $rows[5]['sisa_baik']);
        $this->assertEquals(1, $rows[5]['sisa_rusak']);

        // Row 6: Opname Kurang 2 -> Saldo Baik 16, Rusak 1
        $this->assertEquals(16, $rows[6]['sisa_baik']);
        $this->assertEquals(1, $rows[6]['sisa_rusak']);

        // Row 7: Opname Tambah 4 -> Saldo Baik 20, Rusak 1
        $this->assertEquals(20, $rows[7]['sisa_baik']);
        $this->assertEquals(1, $rows[7]['sisa_rusak']);

        // Row 8: Alih Baik ke Rusak 2 -> Saldo Baik 18, Rusak 3
        $this->assertEquals(18, $rows[8]['sisa_baik']);
        $this->assertEquals(3, $rows[8]['sisa_rusak']);

        // Rekonsiliasi total:
        $this->assertEquals($barang->stok_tersedia, $rows[8]['sisa_baik']);
        $this->assertEquals($barang->stok_rusak, $rows[8]['sisa_rusak']);
        $this->assertEquals($barang->stok_total, $rows[8]['sisa_baik'] + $rows[8]['sisa_rusak']);
    }

    public function test_freeform_description_with_part_number_does_not_misclassify_positive_adjustment(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(2),
        ]);

        // Catat mutasi penyesuaian yang memuat tanda minus pada nomor part (SN-7400) tetapi merupakan penambahan (+5)
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 5,
            'referensi_tipe' => null, // Simulasi data historis tanpa referensi_tipe
            'keterangan' => 'Penyesuaian stok IC 74LS00 (SN-7400) stok opname (+5)',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $response->assertOk();

        $rows = $response->viewData('movementRows');
        $this->assertCount(2, $rows);

        // Harus diklasifikasikan sebagai masuk_baik = 5, BUKAN keluar_baik
        $this->assertEquals(5, $rows[1]['masuk_baik']);
        $this->assertSame('', $rows[1]['keluar_baik']);
        $this->assertEquals(15, $rows[1]['sisa_baik']);
    }

    public function test_freeform_description_with_good_condition_found_does_not_misclassify_as_damaged_transfer(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(2),
        ]);

        // Catat mutasi penyesuaian yang mengandung kata "ditemukan", "kondisi", dan "rusak"
        // tetapi konteksnya "kondisi baik bukan rusak"
        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 3,
            'referensi_tipe' => null,
            'keterangan' => 'Penyesuaian stok: Ditemukan 3 unit di rak alat, kondisi baik bukan rusak',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $response->assertOk();

        $rows = $response->viewData('movementRows');
        $this->assertCount(2, $rows);

        // Harus menambah saldo baik (masuk_baik = 3), TIDAK boleh menambah saldo rusak
        $this->assertEquals(3, $rows[1]['masuk_baik']);
        $this->assertSame('', $rows[1]['masuk_rusak']);
        $this->assertSame('', $rows[1]['keluar_baik']);
        $this->assertEquals(13, $rows[1]['sisa_baik']);
        $this->assertEquals(0, $rows[1]['sisa_rusak']);
    }

    public function test_discarding_damaged_stock_afkir_reduces_damaged_balance_without_reducing_available_balance(): void
    {
        $bengkel = $this->createBengkel();
        $lokasi = $this->createLokasi($bengkel);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $barang = $this->createBarang($bengkel, $lokasi, [
            'stok_total' => 13,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 3,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 13,
            'keterangan' => 'Stok awal',
            'created_at' => now()->subDays(5),
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 3,
            'referensi_tipe' => 'alih_kondisi_rusak',
            'keterangan' => 'Pengalihan kondisi stok: Baik ke Rusak (-3 baik, +3 rusak)',
            'created_at' => now()->subDays(3),
        ]);

        // Toolman membuang/afkir 3 unit rusak. Stok tersedia tetap 10, stok rusak menjadi 0, total stok menjadi 10.
        $response = $this->actingAs($toolman)->put(route('toolman.barang.update', $barang->id), [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'satuan' => 'Unit',
            'stok_baik' => 10,
            'stok_rusak_ringan' => 0,
            'stok_rusak_berat' => 0,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));

        $barang->refresh();
        $this->assertEquals(10, $barang->stok_total);
        $this->assertEquals(10, $barang->stok_tersedia);
        $this->assertEquals(0, $barang->stok_rusak);

        // Verifikasi mutasi afkir tercatat khusus pengurangan rusak
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'penyesuaian',
            'jumlah' => 3,
            'referensi_tipe' => 'penyesuaian_rusak_kurang',
        ]);

        // Verifikasi kartu barang: keluar_rusak = 3, sisa_rusak = 0, sisa_baik tetap 10
        $printResponse = $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id));
        $printResponse->assertOk();

        $rows = $printResponse->viewData('movementRows');
        $this->assertCount(3, $rows);

        $this->assertEquals(3, $rows[2]['keluar_rusak']);
        $this->assertSame('', $rows[2]['keluar_baik']);
        $this->assertEquals(10, $rows[2]['sisa_baik']);
        $this->assertEquals(0, $rows[2]['sisa_rusak']);
    }
}
