<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\Pengadaan;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
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
     * 4. Permintaan forgot-password mengembalikan respon publik yang konsisten (tidak membocorkan status atau eksistensi akun),
     *    sambil tetap menjamin token dan notifikasi HANYA dikirimkan untuk akun yang aktif dan memenuhi syarat.
     */
    public function test_forgot_password_returns_consistent_response_across_all_account_states_and_protects_ineligible_users(): void
    {
        Notification::fake();

        $activeUser = $this->createUser([
            'email' => 'siswa.aktif@example.com',
            'status' => 'aktif',
        ]);

        $suspendedUser = $this->createUser([
            'email' => 'siswa.suspend@example.com',
            'status' => 'suspend',
        ]);

        $pendingUser = $this->createUser([
            'email' => 'pendaftar.baru@example.com',
            'status' => 'menunggu_acc',
        ]);

        $nonexistentEmail = 'tidak.terdaftar@example.com';

        // 1. Akun Aktif: Berhasil kirim notifikasi, terbuat token di DB, respon publik konsisten
        $resActive = $this->post('/forgot-password', ['email' => $activeUser->email]);
        $resActive->assertStatus(302);
        $resActive->assertSessionHasNoErrors();
        $resActive->assertSessionHas('status', __(Password::RESET_LINK_SENT));
        Notification::assertSentTo($activeUser, ResetPassword::class);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $activeUser->email]);

        // 2. Akun Suspend: Respon publik identik 100%, TIDAK kirim notifikasi, TIDAK buat token
        $resSuspend = $this->post('/forgot-password', ['email' => $suspendedUser->email]);
        $resSuspend->assertStatus(302);
        $resSuspend->assertSessionHasNoErrors();
        $resSuspend->assertSessionHas('status', __(Password::RESET_LINK_SENT));
        Notification::assertNotSentTo($suspendedUser, ResetPassword::class);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $suspendedUser->email]);

        // 3. Akun Menunggu ACC: Respon publik identik 100%, TIDAK kirim notifikasi, TIDAK buat token
        $resPending = $this->post('/forgot-password', ['email' => $pendingUser->email]);
        $resPending->assertStatus(302);
        $resPending->assertSessionHasNoErrors();
        $resPending->assertSessionHas('status', __(Password::RESET_LINK_SENT));
        Notification::assertNotSentTo($pendingUser, ResetPassword::class);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $pendingUser->email]);

        // 4. Akun Nonexistent: Respon publik identik 100%, TIDAK kirim notifikasi, TIDAK buat token
        $resNonexistent = $this->post('/forgot-password', ['email' => $nonexistentEmail]);
        $resNonexistent->assertStatus(302);
        $resNonexistent->assertSessionHasNoErrors();
        $resNonexistent->assertSessionHas('status', __(Password::RESET_LINK_SENT));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $nonexistentEmail]);
    }

    /**
     * 5. Form reset-password menolak penggantian sandi untuk akun suspend/pending dengan respon error standar,
     *    tanpa membocorkan status akun atau merusak validasi token.
     */
    public function test_reset_password_returns_consistent_error_and_does_not_mutate_ineligible_accounts(): void
    {
        $suspendedUser = $this->createUser([
            'email' => 'siswa.suspend.reset@example.com',
            'password' => bcrypt('old-password-123'),
            'status' => 'suspend',
        ]);

        $pendingUser = $this->createUser([
            'email' => 'siswa.pending.reset@example.com',
            'password' => bcrypt('old-password-123'),
            'status' => 'menunggu_acc',
        ]);

        $oldSuspendedPasswordHash = $suspendedUser->password;
        $oldPendingPasswordHash = $pendingUser->password;

        // Coba reset akun suspend dengan token dummy
        $resSuspend = $this->post('/reset-password', [
            'token' => 'dummy-token',
            'email' => $suspendedUser->email,
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $resSuspend->assertStatus(302);
        $resSuspend->assertSessionHasErrors(['email' => __(Password::INVALID_TOKEN)]);
        $suspendedUser->refresh();
        $this->assertEquals($oldSuspendedPasswordHash, $suspendedUser->password, 'Password akun suspended tidak boleh berubah.');

        // Coba reset akun pending dengan token dummy
        $resPending = $this->post('/reset-password', [
            'token' => 'dummy-token',
            'email' => $pendingUser->email,
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $resPending->assertStatus(302);
        $resPending->assertSessionHasErrors(['email' => __(Password::INVALID_TOKEN)]);
        $pendingUser->refresh();
        $this->assertEquals($oldPendingPasswordHash, $pendingUser->password, 'Password akun pending tidak boleh berubah.');

        // Coba reset akun tidak terdaftar
        $resNonexistent = $this->post('/reset-password', [
            'token' => 'dummy-token',
            'email' => 'tidak.ada@example.com',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);
        $resNonexistent->assertStatus(302);
        $resNonexistent->assertSessionHasErrors(['email' => __(Password::INVALID_TOKEN)]);
    }

    /**
     * 7. Rute publik otentikasi (register, forgot-password, reset-password) memiliki throttling terhadap abuse.
     */
    public function test_public_auth_routes_are_throttled_against_abuse(): void
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
