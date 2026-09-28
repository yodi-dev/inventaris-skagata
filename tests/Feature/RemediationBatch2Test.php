<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch2Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'TFLM', string $nama = 'Teknik Fabrikasi Logam'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => 'Bengkel untuk pengujian unit Batch 2',
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        static $counter = 100;
        $counter++;

        return User::create(array_merge([
            'name' => "User {$counter}",
            'email' => "user{$counter}@example.com",
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
            'status' => 'aktif',
        ], $attributes));
    }

    private function createBarang(Bengkel $bengkel, array $attributes = []): Barang
    {
        static $counter = 100;
        $counter++;

        return Barang::create(array_merge([
            'bengkel_id' => $bengkel->id,
            'kode_barang' => "BRG-B2-{$counter}",
            'nama' => "Barang {$counter}",
            'jenis_barang' => 'inventaris',
            'satuan' => 'Unit',
            'stok_total' => 20,
            'stok_tersedia' => 20,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 2,
        ], $attributes));
    }

    // =========================================================================
    // 1. BORROWING APPROVAL - VALID TRANSITIONS & STOCK INVARIANTS
    // =========================================================================

    public function test_approval_transitions_inventaris_loan_to_active_and_updates_stock_atomically(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, [
            'jenis_barang' => 'inventaris',
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now(),
            'keperluan' => 'Praktikum Pengelasan',
            'status' => 'pending',
        ]);

        $detail = DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 3,
        ]);

        $response = $this->actingAs($toolman)
            ->post(route('toolman.peminjaman.approve', $peminjaman->id));

        $response->assertRedirect(route('toolman.peminjaman.index', ['tab' => 'riwayat']));
        $response->assertSessionHas('success');

        // Status tiket berubah menjadi active
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'active',
            'diproses_oleh' => $toolman->id,
        ]);

        // Invarian stok inventaris:
        // stok_tersedia berkurang 3 (10 - 3 = 7), stok_dipinjam bertambah 3 (0 + 3 = 3), stok_total tetap 10
        $barang->refresh();
        $this->assertEquals(7, $barang->stok_tersedia);
        $this->assertEquals(3, $barang->stok_dipinjam);
        $this->assertEquals(10, $barang->stok_total);

        // Tercatat 1 StockMovement jenis peminjaman
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'peminjaman',
            'jumlah' => 3,
            'referensi_tipe' => 'peminjamans',
            'referensi_id' => $peminjaman->id,
        ]);
    }

    public function test_approval_transitions_bhp_only_loan_directly_to_selesai(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $bhp = $this->createBarang($bengkel, [
            'jenis_barang' => 'bhp',
            'satuan' => 'Pcs',
            'stok_total' => 50,
            'stok_tersedia' => 50,
            'stok_dipinjam' => 0,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now(),
            'keperluan' => 'Bahan Habis Pakai Praktik Las',
            'status' => 'pending',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $bhp->id,
            'jumlah' => 10,
        ]);

        $response = $this->actingAs($toolman)
            ->post(route('toolman.peminjaman.approve', $peminjaman->id));

        $response->assertSessionHas('success');

        // PRD 3.7: Tiket BHP-only langsung 'selesai'
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'selesai',
        ]);

        // Invarian stok BHP: stok_tersedia dan stok_total berkurang permanen 10
        $bhp->refresh();
        $this->assertEquals(40, $bhp->stok_tersedia);
        $this->assertEquals(40, $bhp->stok_total);
        $this->assertEquals(0, $bhp->stok_dipinjam);

        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $bhp->id,
            'jenis' => 'bhp_keluar',
            'jumlah' => 10,
        ]);
    }

    // =========================================================================
    // 2. BORROWING APPROVAL - REPEATED / DUPLICATE REQUESTS & LOCK SAFETY
    // =========================================================================

    public function test_repeated_approval_request_is_rejected_without_duplicate_stock_deduction(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, [
            'stok_total' => 20,
            'stok_tersedia' => 20,
            'stok_dipinjam' => 0,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now(),
            'keperluan' => 'Uji Idempotensi Approval',
            'status' => 'pending',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 5,
        ]);

        // Eksekusi Approval Pertama
        $firstResponse = $this->actingAs($toolman)
            ->post(route('toolman.peminjaman.approve', $peminjaman->id));
        $firstResponse->assertSessionHas('success');

        // Pastikan stok terpotong 1 kali
        $barang->refresh();
        $this->assertEquals(15, $barang->stok_tersedia);
        $this->assertEquals(5, $barang->stok_dipinjam);
        $this->assertEquals(1, StockMovement::where('referensi_id', $peminjaman->id)->count());

        // Eksekusi Approval Kedua (Duplikasi / Concurrency Re-entry)
        $secondResponse = $this->actingAs($toolman)
            ->post(route('toolman.peminjaman.approve', $peminjaman->id));

        $secondResponse->assertSessionHas('error');

        // Pastikan stok TIDAK terpotong dua kali dan tidak ada StockMovement duplikat
        $barang->refresh();
        $this->assertEquals(15, $barang->stok_tersedia);
        $this->assertEquals(5, $barang->stok_dipinjam);
        $this->assertEquals(1, StockMovement::where('referensi_id', $peminjaman->id)->count());
    }

    public function test_approval_fails_and_rolls_back_if_stock_insufficient(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, [
            'stok_total' => 5,
            'stok_tersedia' => 2, // Hanya tersedia 2
            'stok_dipinjam' => 3,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now(),
            'keperluan' => 'Minta Lebih Banyak dari Stok',
            'status' => 'pending',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 4, // Meminta 4 padahal tersedia 2
        ]);

        $response = $this->actingAs($toolman)
            ->post(route('toolman.peminjaman.approve', $peminjaman->id));

        $response->assertSessionHas('error');

        // Transaksi harus tetap pending dan stok tidak berubah
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'pending',
        ]);

        $barang->refresh();
        $this->assertEquals(2, $barang->stok_tersedia);
        $this->assertEquals(3, $barang->stok_dipinjam);
        $this->assertEquals(0, StockMovement::where('referensi_id', $peminjaman->id)->count());
    }

    // =========================================================================
    // 3. PHYSICAL RETURN - VALID CONDITIONS & PRECISE STOCK INVARIANTS
    // =========================================================================

    public function test_physical_return_handles_all_conditions_correctly(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, [
            'jenis_barang' => 'inventaris',
            'stok_total' => 10,
            'stok_tersedia' => 4,
            'stok_dipinjam' => 6,
            'stok_rusak' => 0,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDay(),
            'keperluan' => 'Praktek Bubut Presisi',
            'status' => 'active',
        ]);

        $detail = DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 6, // Dipinjam 6
        ]);

        // Kembalikan 6 unit dengan variasi: 3 Baik, 2 Rusak, 1 Hilang
        $response = $this->actingAs($toolman)
            ->post(route('toolman.pengembalian.process-check', $peminjaman->id), [
                'items' => [
                    $detail->id => [
                        'jumlah_baik' => 3,
                        'jumlah_rusak' => 2,
                        'jumlah_hilang' => 1,
                        'catatan' => '2 rusak mata pahat patah, 1 hilang',
                    ],
                ],
            ]);

        $response->assertRedirect(route('toolman.pengembalian.index'));
        $response->assertSessionHas('success');

        // Status tiket peminjaman menjadi selesai
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'selesai',
        ]);

        // Verifikasi detail kondisi tercatat
        $this->assertDatabaseHas('detail_peminjamans', [
            'id' => $detail->id,
            'jumlah_baik' => 3,
            'jumlah_rusak' => 2,
            'jumlah_hilang' => 1,
        ]);

        // Invarian stok PRD 3.6:
        // - stok_tersedia: 4 + 3 = 7
        // - stok_rusak: 0 + 2 = 2
        // - stok_dipinjam: 6 - (3 + 2 + 1) = 0
        // - stok_total: 10 - 1 (hilang) = 9
        $barang->refresh();
        $this->assertEquals(7, $barang->stok_tersedia);
        $this->assertEquals(2, $barang->stok_rusak);
        $this->assertEquals(0, $barang->stok_dipinjam);
        $this->assertEquals(9, $barang->stok_total);

        // Verifikasi 3 StockMovement terpisah dibuat
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'pengembalian_baik',
            'jumlah' => 3,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'pengembalian_rusak',
            'jumlah' => 2,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'jenis' => 'barang_hilang',
            'jumlah' => 1,
        ]);
    }

    // =========================================================================
    // 4. PHYSICAL RETURN - REPEATED / DUPLICATE RETURN ATTEMPTS
    // =========================================================================

    public function test_repeated_physical_return_is_blocked_without_duplicate_stock_addition(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, [
            'stok_total' => 10,
            'stok_tersedia' => 6,
            'stok_dipinjam' => 4,
            'stok_rusak' => 0,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDay(),
            'keperluan' => 'Uji Duplikasi Pengembalian',
            'status' => 'menunggu_pengecekan',
        ]);

        $detail = DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 4,
        ]);

        $payload = [
            'items' => [
                $detail->id => [
                    'jumlah_baik' => 4,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                ],
            ],
        ];

        // Eksekusi pengembalian pertama
        $res1 = $this->actingAs($toolman)
            ->post(route('toolman.pengembalian.process-check', $peminjaman->id), $payload);
        $res1->assertSessionHas('success');

        $barang->refresh();
        $this->assertEquals(10, $barang->stok_tersedia);
        $this->assertEquals(0, $barang->stok_dipinjam);
        $this->assertEquals(1, StockMovement::where('referensi_id', $peminjaman->id)->count());

        // Eksekusi pengembalian kedua (duplicate check)
        $res2 = $this->actingAs($toolman)
            ->post(route('toolman.pengembalian.process-check', $peminjaman->id), $payload);
        $res2->assertSessionHas('error');

        // Pastikan stok tidak bertambah ganda (tetap 10, bukan 14)
        $barang->refresh();
        $this->assertEquals(10, $barang->stok_tersedia);
        $this->assertEquals(0, $barang->stok_dipinjam);
        $this->assertEquals(1, StockMovement::where('referensi_id', $peminjaman->id)->count());
    }

    // =========================================================================
    // 5. PHYSICAL RETURN - INVALID QUANTITIES & ROLLBACK
    // =========================================================================

    public function test_physical_return_fails_when_quantities_mismatch_loan_amount(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, [
            'stok_total' => 10,
            'stok_tersedia' => 5,
            'stok_dipinjam' => 5,
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDay(),
            'keperluan' => 'Uji Jumlah Tidak Cocok',
            'status' => 'menunggu_pengecekan',
        ]);

        $detail = DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 5,
        ]);

        // Total input 4 (3 + 1 + 0) padahal pinjam 5
        $response = $this->actingAs($toolman)
            ->post(route('toolman.pengembalian.process-check', $peminjaman->id), [
                'items' => [
                    $detail->id => [
                        'jumlah_baik' => 3,
                        'jumlah_rusak' => 1,
                        'jumlah_hilang' => 0,
                    ],
                ],
            ]);

        $response->assertSessionHas('error');

        // Transaksi tidak berubah dan status tetap menunggu_pengecekan
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'menunggu_pengecekan',
        ]);

        $barang->refresh();
        $this->assertEquals(5, $barang->stok_tersedia);
        $this->assertEquals(5, $barang->stok_dipinjam);
        $this->assertEquals(0, StockMovement::where('referensi_id', $peminjaman->id)->count());
    }

    // =========================================================================
    // 6. BORROWER RETURN REQUEST (ajukanPengembalian)
    // =========================================================================

    public function test_borrower_can_request_return_from_active_status(): void
    {
        $bengkel = $this->createBengkel();
        $peminjam = $this->createUser();

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDay(),
            'keperluan' => 'Praktek Selesai',
            'status' => 'active',
        ]);

        $response = $this->actingAs($peminjam)
            ->post(route('peminjam.tiket.kembalikan', $peminjaman->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'menunggu_pengecekan',
        ]);
    }

    public function test_borrower_cannot_request_return_from_invalid_status(): void
    {
        $bengkel = $this->createBengkel();
        $peminjam = $this->createUser();

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDay(),
            'keperluan' => 'Uji Tiket Pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($peminjam)
            ->post(route('peminjam.tiket.kembalikan', $peminjaman->id));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'pending',
        ]);
    }
}
