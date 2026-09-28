<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPeminjaman;
use App\Models\DetailPengadaan;
use App\Models\LokasiPenyimpanan;
use App\Models\Peminjaman;
use App\Models\Pengadaan;
use App\Models\Satuan;
use App\Models\StockMovement;
use App\Models\SumberDana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch5Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode, string $nama): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => "Deskripsi {$nama}",
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        static $counter = 500;
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
        static $counter = 500;
        $counter++;

        return Barang::create(array_merge([
            'bengkel_id' => $bengkel->id,
            'kode_barang' => "BRG-B5-{$counter}",
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

    private function createLokasi(Bengkel $bengkel, string $kode = 'RAK-01'): LokasiPenyimpanan
    {
        static $counter = 500;
        $counter++;

        return LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => "{$kode}-{$counter}",
            'nama' => "Lokasi {$counter}",
            'deskripsi' => 'Deskripsi lokasi',
        ]);
    }

    // =========================================================================
    // 1. UJI RUTE FALLBACK /dashboard & REDIRECT SESUAI ROLE & STATUS
    // =========================================================================

    public function test_dashboard_fallback_route_redirects_guests_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_fallback_route_redirects_waka_to_superadmin_dashboard(): void
    {
        $waka = $this->createUser(['role' => 'waka', 'status' => 'aktif']);

        $response = $this->actingAs($waka)->get('/dashboard');
        $response->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_dashboard_fallback_route_redirects_toolman_to_toolman_dashboard(): void
    {
        $bengkel = $this->createBengkel('TFLM', 'Fabrikasi Logam');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id, 'status' => 'aktif']);

        $response = $this->actingAs($toolman)->get('/dashboard');
        $response->assertRedirect(route('toolman.dashboard'));
    }

    public function test_dashboard_fallback_route_redirects_peminjam_to_peminjam_dashboard(): void
    {
        $bengkel = $this->createBengkel('TFLM', 'Fabrikasi Logam');
        $siswa = $this->createUser([
            'role' => 'peminjam',
            'jenis_peminjam' => 'siswa',
            'bengkel_id' => $bengkel->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($siswa)->get('/dashboard');
        $response->assertRedirect(route('peminjam.dashboard'));
    }

    public function test_suspended_user_is_logged_out_and_redirected_to_login(): void
    {
        $bengkel = $this->createBengkel('TFLM', 'Fabrikasi Logam');
        $suspendedToolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => $bengkel->id,
            'status' => 'suspend',
        ]);

        $response = $this->actingAs($suspendedToolman)->get('/dashboard');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_pending_user_is_logged_out_and_redirected_to_login(): void
    {
        $bengkel = $this->createBengkel('TFLM', 'Fabrikasi Logam');
        $pendingStudent = $this->createUser([
            'role' => 'peminjam',
            'jenis_peminjam' => 'siswa',
            'bengkel_id' => $bengkel->id,
            'status' => 'menunggu_acc',
        ]);

        $response = $this->actingAs($pendingStudent)->get('/dashboard');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // =========================================================================
    // 2. UJI TOOLMAN TANPA BENGKEL (bengkel_id = NULL) DIBLOKIR 403
    // =========================================================================

    public function test_unassigned_toolman_is_blocked_with_403_on_dashboard_and_barang_paths(): void
    {
        // Buat bengkel dummy agar jika fallback Bengkel::first() aktif, ia akan mengambilnya
        $bengkel1 = $this->createBengkel('B1', 'Bengkel Satu');
        $barang1 = $this->createBarang($bengkel1);

        $unassignedToolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => null,
            'status' => 'aktif',
        ]);

        // Dashboard
        $response = $this->actingAs($unassignedToolman)->get(route('toolman.dashboard'));
        $response->assertStatus(403);

        // Barang Index
        $response = $this->actingAs($unassignedToolman)->get(route('toolman.barang.index'));
        $response->assertStatus(403);

        // Barang Create
        $response = $this->actingAs($unassignedToolman)->get(route('toolman.barang.create'));
        $response->assertStatus(403);

        // Barang Store
        $response = $this->actingAs($unassignedToolman)->post(route('toolman.barang.store'), [
            'kode_barang' => 'NEW-CODE',
            'nama_barang' => 'Test Barang',
            'tipe' => 'inventaris',
            'satuan' => 'Unit',
        ]);
        $response->assertStatus(403);

        // Barang Print Kartu
        $response = $this->actingAs($unassignedToolman)->get(route('toolman.barang.print-kartu', $barang1->id));
        $response->assertStatus(403);

        // Barang Template Excel & Import
        $response = $this->actingAs($unassignedToolman)->get(route('toolman.barang.template-excel'));
        $response->assertStatus(403);
    }

    public function test_unassigned_toolman_is_blocked_with_403_on_sirkulasi_paths(): void
    {
        $bengkel1 = $this->createBengkel('B1', 'Bengkel Satu');
        $barang1 = $this->createBarang($bengkel1);
        $peminjam = $this->createUser(['role' => 'peminjam', 'bengkel_id' => $bengkel1->id]);

        $pinjaman = Peminjaman::create([
            'bengkel_id' => $bengkel1->id,
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => now(),
            'batas_kembali' => now()->addDays(2),
            'status' => 'pending',
            'keperluan' => 'Praktik Uji',
        ]);

        $unassignedToolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => null,
            'status' => 'aktif',
        ]);

        // Peminjaman Index & Show & Approve & Reject
        $this->actingAs($unassignedToolman)->get(route('toolman.peminjaman.index'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.peminjaman.show', $pinjaman->id))->assertStatus(403);
        $this->actingAs($unassignedToolman)->post(route('toolman.peminjaman.approve', $pinjaman->id))->assertStatus(403);
        $this->actingAs($unassignedToolman)->post(route('toolman.peminjaman.reject', $pinjaman->id), ['alasan_penolakan' => 'Ditolak'])->assertStatus(403);

        // Pengembalian Index & Check & Print
        $this->actingAs($unassignedToolman)->get(route('toolman.pengembalian.index'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.pengembalian.check', $pinjaman->id))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.peminjaman.print-pinjam', $pinjaman->id))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.peminjaman.print-kembali', $pinjaman->id))->assertStatus(403);
    }

    public function test_unassigned_toolman_is_blocked_with_403_on_pengadaan_lokasi_mutasi_peminjam_paths(): void
    {
        $bengkel1 = $this->createBengkel('B1', 'Bengkel Satu');
        $lokasi1 = $this->createLokasi($bengkel1);
        $unassignedToolman = $this->createUser([
            'role' => 'toolman',
            'bengkel_id' => null,
            'status' => 'aktif',
        ]);

        // Pengadaan
        $this->actingAs($unassignedToolman)->get(route('toolman.pengadaan.index'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.pengadaan.create'))->assertStatus(403);

        // Lokasi
        $this->actingAs($unassignedToolman)->get(route('toolman.lokasi.index'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->post(route('toolman.lokasi.store'), ['kode' => 'LK-01', 'nama' => 'Rak'])->assertStatus(403);
        $this->actingAs($unassignedToolman)->put(route('toolman.lokasi.update', $lokasi1->id), ['kode' => 'LK-01', 'nama' => 'Rak'])->assertStatus(403);
        $this->actingAs($unassignedToolman)->delete(route('toolman.lokasi.destroy', $lokasi1->id))->assertStatus(403);

        // Mutasi
        $this->actingAs($unassignedToolman)->get(route('toolman.mutasi.index'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.mutasi.export'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.mutasi.print'))->assertStatus(403);

        // Peminjam
        $this->actingAs($unassignedToolman)->get(route('toolman.peminjam.index'))->assertStatus(403);

        // Satuan & Sumber Dana
        $this->actingAs($unassignedToolman)->get(route('toolman.satuan.index'))->assertStatus(403);
        $this->actingAs($unassignedToolman)->get(route('toolman.sumber-dana.index'))->assertStatus(403);
    }

    // =========================================================================
    // 3. UJI TOOLMAN TERDAFTAR DAPAT MENGAKSES BENGKELNYA SENDIRI
    // =========================================================================

    public function test_assigned_toolman_can_access_own_records(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer Jaringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id, 'status' => 'aktif']);
        $lokasi = $this->createLokasi($bengkel);
        $barang = $this->createBarang($bengkel, ['lokasi_penyimpanan_id' => $lokasi->id]);

        // Dashboard & Barang Index
        $this->actingAs($toolman)->get(route('toolman.dashboard'))->assertStatus(200);
        $this->actingAs($toolman)->get(route('toolman.barang.index'))->assertStatus(200);

        // Edit & Print Kartu
        $this->actingAs($toolman)->get(route('toolman.barang.edit', $barang->id))->assertStatus(200);
        $this->actingAs($toolman)->get(route('toolman.barang.print-kartu', $barang->id))->assertStatus(200);

        // Create Lokasi Baru
        $response = $this->actingAs($toolman)->post(route('toolman.lokasi.store'), [
            'kode' => 'RAK-BARU-01',
            'nama' => 'Rak Baru TKJ',
        ]);
        $response->assertRedirect(route('toolman.lokasi.index'));
        $this->assertDatabaseHas('lokasi_penyimpanans', [
            'bengkel_id' => $bengkel->id,
            'kode' => 'RAK-BARU-01',
        ]);

        // Mutasi index & export
        $this->actingAs($toolman)->get(route('toolman.mutasi.index'))->assertStatus(200);
        $this->actingAs($toolman)->get(route('toolman.mutasi.export-excel'))->assertStatus(200);
        $this->actingAs($toolman)->get(route('toolman.mutasi.print'))->assertStatus(200);
    }

    // =========================================================================
    // 4. UJI CEGAH AKSES LINTAS BENGKEL (CROSS-WORKSHOP DEFENSE)
    // =========================================================================

    public function test_toolman_cannot_view_or_modify_barang_of_another_workshop(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $bengkelB = $this->createBengkel('BB', 'Bengkel B');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);
        $barangB = $this->createBarang($bengkelB, ['nama' => 'Barang Milik B']);

        // Toolman A mencoba edit barang Bengkel B -> 404
        $this->actingAs($toolmanA)->get(route('toolman.barang.edit', $barangB->id))->assertStatus(404);

        // Toolman A mencoba update barang Bengkel B -> 404
        $this->actingAs($toolmanA)->put(route('toolman.barang.update', $barangB->id), [
            'kode_barang' => $barangB->kode_barang,
            'nama_barang' => 'Nama Diretas',
            'satuan' => 'Unit',
        ])->assertStatus(404);

        // Toolman A mencoba destroy barang Bengkel B -> 404
        $this->actingAs($toolmanA)->delete(route('toolman.barang.destroy', $barangB->id))->assertStatus(404);

        // Toolman A mencoba print kartu barang Bengkel B -> 404
        $this->actingAs($toolmanA)->get(route('toolman.barang.print-kartu', $barangB->id))->assertStatus(404);
    }

    public function test_toolman_cannot_view_or_process_peminjaman_of_another_workshop(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $bengkelB = $this->createBengkel('BB', 'Bengkel B');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);
        $barangB = $this->createBarang($bengkelB);
        $siswaB = $this->createUser(['role' => 'peminjam', 'bengkel_id' => $bengkelB->id]);

        $pinjamanB = Peminjaman::create([
            'bengkel_id' => $bengkelB->id,
            'user_id' => $siswaB->id,
            'tanggal_pinjam' => now(),
            'batas_kembali' => now()->addDays(2),
            'status' => 'pending',
            'keperluan' => 'Praktikum Bengkel B',
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $pinjamanB->id,
            'barang_id' => $barangB->id,
            'jumlah' => 1,
            'kondisi_pinjam' => 'baik',
        ]);

        // Toolman A show pinjaman Bengkel B -> 404
        $this->actingAs($toolmanA)->get(route('toolman.peminjaman.show', $pinjamanB->id))->assertStatus(404);

        // Toolman A approve pinjaman Bengkel B -> 404
        $this->actingAs($toolmanA)->post(route('toolman.peminjaman.approve', $pinjamanB->id))->assertStatus(404);

        // Toolman A reject pinjaman Bengkel B -> 404
        $this->actingAs($toolmanA)->post(route('toolman.peminjaman.reject', $pinjamanB->id), [
            'alasan_penolakan' => 'Coba tolak milik B',
        ])->assertStatus(404);

        // Toolman A check & print pinjaman Bengkel B -> 404
        $this->actingAs($toolmanA)->get(route('toolman.pengembalian.check', $pinjamanB->id))->assertStatus(404);
        $this->actingAs($toolmanA)->get(route('toolman.peminjaman.print-pinjam', $pinjamanB->id))->assertStatus(404);
        $this->actingAs($toolmanA)->get(route('toolman.peminjaman.print-kembali', $pinjamanB->id))->assertStatus(404);
    }

    public function test_toolman_cannot_view_or_manipulate_pengadaan_of_another_workshop(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $bengkelB = $this->createBengkel('BB', 'Bengkel B');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);
        $toolmanB = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelB->id, 'status' => 'aktif']);

        $rabB = Pengadaan::create([
            'bengkel_id' => $bengkelB->id,
            'dibuat_oleh' => $toolmanB->id,
            'judul' => 'RAB Bengkel B',
            'status' => 'draft',
            'total_estimasi' => 500000,
        ]);

        // Show, edit, update, submit, destroy, receive Pengadaan Bengkel B oleh Toolman A -> 404
        $this->actingAs($toolmanA)->get(route('toolman.pengadaan.show', $rabB->id))->assertStatus(404);
        $this->actingAs($toolmanA)->get(route('toolman.pengadaan.edit', $rabB->id))->assertStatus(404);
        $this->actingAs($toolmanA)->put(route('toolman.pengadaan.update', $rabB->id), [
            'judul' => 'Judul Baru',
            'action' => 'draft',
            'items' => [['nama' => 'Alat', 'jumlah' => 1, 'satuan' => 'Unit', 'harga_satuan' => 1000]],
        ])->assertStatus(404);
        $this->actingAs($toolmanA)->post(route('toolman.pengadaan.submit', $rabB->id))->assertStatus(404);
        $this->actingAs($toolmanA)->delete(route('toolman.pengadaan.destroy', $rabB->id))->assertStatus(404);
        $this->actingAs($toolmanA)->post(route('toolman.pengadaan.receive', $rabB->id))->assertStatus(404);
    }

    public function test_toolman_cannot_submit_pengadaan_referencing_barang_of_another_workshop(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $bengkelB = $this->createBengkel('BB', 'Bengkel B');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);
        $barangB = $this->createBarang($bengkelB, ['nama' => 'Mesin Bubut B']);

        // Toolman A mencoba membuat usulan RAB di Bengkel A tapi menyelipkan barang_id milik Bengkel B
        $response = $this->actingAs($toolmanA)->post(route('toolman.pengadaan.store'), [
            'judul' => 'Pengadaan Alat Bengkel A',
            'action' => 'draft',
            'items' => [
                [
                    'barang_id' => $barangB->id,
                    'nama' => 'Mesin Bubut',
                    'jumlah' => 2,
                    'satuan' => 'Unit',
                    'harga_satuan' => 15000000,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['items.0.barang_id']);
    }

    public function test_toolman_cannot_view_or_modify_lokasi_of_another_workshop(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $bengkelB = $this->createBengkel('BB', 'Bengkel B');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);
        $lokasiB = $this->createLokasi($bengkelB, 'RAK-B');

        // Toolman A update lokasi Bengkel B -> 404
        $this->actingAs($toolmanA)->put(route('toolman.lokasi.update', $lokasiB->id), [
            'kode' => $lokasiB->kode,
            'nama' => 'Rak Diretas',
        ])->assertStatus(404);

        // Toolman A destroy lokasi Bengkel B -> 404
        $this->actingAs($toolmanA)->delete(route('toolman.lokasi.destroy', $lokasiB->id))->assertStatus(404);
    }

    public function test_toolman_cannot_view_student_belonging_to_another_workshop(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $bengkelB = $this->createBengkel('BB', 'Bengkel B');

        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);
        $siswaB = $this->createUser([
            'role' => 'peminjam',
            'jenis_peminjam' => 'siswa',
            'bengkel_id' => $bengkelB->id,
            'status' => 'aktif',
        ]);

        // Toolman A mencoba melihat detail profil siswa Bengkel B -> 404
        $this->actingAs($toolmanA)->get(route('toolman.peminjam.show', $siswaB->id))->assertStatus(404);
    }

    public function test_toolman_can_view_and_process_teacher_peminjam(): void
    {
        $bengkelA = $this->createBengkel('BA', 'Bengkel A');
        $toolmanA = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelA->id, 'status' => 'aktif']);

        // Guru tidak terikat bengkel tertentu (bengkel_id = null)
        $guru = $this->createUser([
            'role' => 'peminjam',
            'jenis_peminjam' => 'guru',
            'bengkel_id' => null,
            'status' => 'aktif',
        ]);

        // Toolman A dapat melihat profil Guru
        $response = $this->actingAs($toolmanA)->get(route('toolman.peminjam.show', $guru->id));
        $response->assertStatus(200);
    }
}
