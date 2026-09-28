<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPeminjaman;
use App\Models\DetailPengadaan;
use App\Models\Peminjaman;
use App\Models\Pengadaan;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch1Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'TFLM', string $nama = 'Teknik Fabrikasi Logam'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => 'Bengkel untuk pengujian unit',
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        static $counter = 1;
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
        static $counter = 1;
        $counter++;

        return Barang::create(array_merge([
            'bengkel_id' => $bengkel->id,
            'kode_barang' => "BRG-{$counter}",
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
    // JALUR 1: DELETE /profile (ProfileController::destroy)
    // =========================================================================

    public function test_user_cannot_delete_account_if_they_have_borrowing_history(): void
    {
        $bengkel = $this->createBengkel();
        $user = $this->createUser();
        $barang = $this->createBarang($bengkel);

        // Buat peminjaman historis yang sudah berstatus 'selesai'
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDays(5),
            'keperluan' => 'Praktikum Pengelasan',
            'status' => 'selesai',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'jumlah_baik' => 1,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password123',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('peminjamans', ['id' => $peminjaman->id]);
    }

    public function test_user_can_delete_account_if_clean_without_history(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password123',
            ]);

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    // =========================================================================
    // JALUR 2: Superadmin\ToolmanController::destroy
    // =========================================================================

    public function test_toolman_cannot_be_deleted_if_they_have_stock_movement_records(): void
    {
        $bengkel = $this->createBengkel();
        $waka = $this->createUser(['role' => 'waka']);
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);
        $barang = $this->createBarang($bengkel);

        // Rekam transaksi mutasi stok oleh toolman
        $movement = StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 5,
            'keterangan' => 'Pengadaan barang baru',
        ]);

        $response = $this->actingAs($waka)
            ->delete(route('superadmin.toolman.destroy', $toolman->id));

        $response->assertRedirect(route('superadmin.toolman.index'));
        $response->assertSessionHas('error');

        // Pastikan akun toolman dan riwayat mutasi stok tetap utuh
        $this->assertDatabaseHas('users', ['id' => $toolman->id]);
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id]);
    }

    public function test_toolman_cannot_be_deleted_if_they_created_pengadaan_rab(): void
    {
        $bengkel = $this->createBengkel();
        $waka = $this->createUser(['role' => 'waka']);
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Elektroda Las 2026',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($waka)
            ->delete(route('superadmin.toolman.destroy', $toolman->id));

        $response->assertRedirect(route('superadmin.toolman.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $toolman->id]);
        $this->assertDatabaseHas('pengadaans', ['id' => $pengadaan->id]);
    }

    public function test_toolman_without_history_can_be_deleted(): void
    {
        $bengkel = $this->createBengkel();
        $waka = $this->createUser(['role' => 'waka']);
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);

        $response = $this->actingAs($waka)
            ->delete(route('superadmin.toolman.destroy', $toolman->id));

        $response->assertRedirect(route('superadmin.toolman.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $toolman->id]);
    }

    // =========================================================================
    // JALUR 3: Toolman\BarangController::destroy
    // =========================================================================

    public function test_barang_cannot_be_deleted_if_it_has_completed_borrowing_history(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);
        $peminjam = $this->createUser();
        $barang = $this->createBarang($bengkel, ['stok_dipinjam' => 0]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now()->subDays(2),
            'keperluan' => 'Praktek Bubut',
            'status' => 'selesai',
        ]);

        $detail = DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'jumlah_baik' => 2,
        ]);

        $response = $this->actingAs($toolman)
            ->delete(route('toolman.barang.destroy', $barang->id));

        $response->assertSessionHas('error');

        // Pastikan barang dan detail peminjaman historis tidak terhapus
        $this->assertDatabaseHas('barangs', ['id' => $barang->id]);
        $this->assertDatabaseHas('detail_peminjamans', ['id' => $detail->id]);
    }

    public function test_barang_cannot_be_deleted_if_it_has_stock_movements(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);
        $barang = $this->createBarang($bengkel);

        $movement = StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
        ]);

        $response = $this->actingAs($toolman)
            ->delete(route('toolman.barang.destroy', $barang->id));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('barangs', ['id' => $barang->id]);
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id]);
    }

    public function test_barang_without_history_can_be_deleted(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);
        $barang = $this->createBarang($bengkel);

        $response = $this->actingAs($toolman)
            ->delete(route('toolman.barang.destroy', $barang->id));

        $response->assertRedirect(route('toolman.barang.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('barangs', ['id' => $barang->id]);
    }

    // =========================================================================
    // JALUR 4: Superadmin\BengkelController::destroy
    // =========================================================================

    public function test_bengkel_cannot_be_deleted_if_it_has_pengadaan_records(): void
    {
        $bengkel = $this->createBengkel();
        $waka = $this->createUser(['role' => 'waka']);
        $toolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Pengadaan Mesin Bubut 2026',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($waka)
            ->delete(route('superadmin.bengkel.destroy', $bengkel->id));

        $response->assertRedirect(route('superadmin.bengkel.index'));
        $response->assertSessionHas('error');

        // Pastikan bengkel dan pengadaan tidak terhapus
        $this->assertDatabaseHas('bengkels', ['id' => $bengkel->id]);
        $this->assertDatabaseHas('pengadaans', ['id' => $pengadaan->id]);
    }

    public function test_bengkel_without_any_records_can_be_deleted(): void
    {
        $bengkel = $this->createBengkel('KOSONG', 'Bengkel Uji Kosong');
        $waka = $this->createUser(['role' => 'waka']);

        $response = $this->actingAs($waka)
            ->delete(route('superadmin.bengkel.destroy', $bengkel->id));

        $response->assertRedirect(route('superadmin.bengkel.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('bengkels', ['id' => $bengkel->id]);
    }

    // =========================================================================
    // JALUR 5: Database Foreign Key Restriction Integrity
    // =========================================================================

    public function test_database_constraint_prevents_raw_deletion_of_user_with_loans(): void
    {
        $bengkel = $this->createBengkel();
        $user = $this->createUser();

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now(),
            'keperluan' => 'Uji Integritas Foreign Key',
            'status' => 'active',
        ]);

        // Karena foreign key diatur restrictOnDelete, penghapusan langsung di level model/DB harus melempar exception
        $this->expectException(\Illuminate\Database\QueryException::class);
        $user->delete();
    }

    public function test_database_constraint_prevents_raw_deletion_of_barang_with_detail_peminjamans(): void
    {
        $bengkel = $this->createBengkel();
        $user = $this->createUser();
        $barang = $this->createBarang($bengkel);

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'bengkel_id' => $bengkel->id,
            'tanggal_pinjam' => now(),
            'keperluan' => 'Uji Integritas Barang',
            'status' => 'selesai',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'jumlah_baik' => 1,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $barang->delete();
    }

    public function test_database_constraint_prevents_raw_deletion_of_bengkel_with_pengadaans(): void
    {
        $bengkel = $this->createBengkel();
        $user = $this->createUser();

        Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $user->id,
            'judul' => 'RAB Pengadaan Test',
            'status' => 'draft',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $bengkel->delete();
    }
}
