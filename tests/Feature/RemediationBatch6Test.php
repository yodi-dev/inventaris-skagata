<?php

namespace Tests\Feature;

use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\Pengadaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch6Test extends TestCase
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
        static $counter = 600;
        $counter++;

        return User::create(array_merge([
            'name' => "User {$counter}",
            'email' => "user{$counter}@example.com",
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
            'status' => 'aktif',
        ], $attributes));
    }

    private function createPengadaan(Bengkel $bengkel, User $creator, array $attributes = []): Pengadaan
    {
        return Pengadaan::create(array_merge([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $creator->id,
            'judul' => 'Pengadaan Alat Uji ' . uniqid(),
            'catatan' => 'Kebutuhan praktik semester genap',
            'status' => 'pending',
            'diajukan_pada' => now(),
        ], $attributes));
    }

    private function addDetail(Pengadaan $pengadaan, string $nama, int $jumlah, float $hargaSatuan, string $satuan = 'Unit'): DetailPengadaan
    {
        return DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'nama_barang' => $nama,
            'spesifikasi' => 'Spesifikasi standar industri',
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            'harga_satuan' => $hargaSatuan,
        ]);
    }

    public function test_waka_can_view_completed_rab_in_index_and_stats(): void
    {
        $bengkel = $this->createBengkel('TKR', 'Teknik Kendaraan Ringan');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rabSelesai = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Multitester Digital Selesai',
            'status' => 'selesai',
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDays(2),
        ]);
        $this->addDetail($rabSelesai, 'Multitester Digital', 5, 250000);

        $rabPending = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Kompresor Pending',
            'status' => 'pending',
        ]);
        $this->addDetail($rabPending, 'Kompresor Udara', 1, 3500000);

        $response = $this->actingAs($waka)->get(route('superadmin.pengadaan.index'));

        $response->assertOk();
        $response->assertSee('RAB Multitester Digital Selesai');
        $response->assertSee('RAB Kompresor Pending');
        $response->assertSee('Selesai');
        $response->assertViewHas('totalSelesai', 1);
        $response->assertViewHas('totalPending', 1);
        $response->assertViewHas('totalRAB', 2);
    }

    public function test_waka_can_filter_completed_rabs_in_index(): void
    {
        $bengkel = $this->createBengkel('TITL', 'Teknik Instalasi Tenaga Listrik');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rabSelesai = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Tang Ampere Selesai Diterima',
            'status' => 'selesai',
            'direview_oleh' => $waka->id,
        ]);
        $this->addDetail($rabSelesai, 'Tang Ampere', 3, 400000);

        $rabApproved = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Kabel NYM Disetujui',
            'status' => 'approved',
            'direview_oleh' => $waka->id,
        ]);
        $this->addDetail($rabApproved, 'Kabel NYM 3x2.5', 2, 850000);

        // Filter status=selesai
        $response = $this->actingAs($waka)->get(route('superadmin.pengadaan.index', ['status' => 'selesai']));

        $response->assertOk();
        $response->assertSee('RAB Tang Ampere Selesai Diterima');
        $response->assertDontSee('RAB Kabel NYM Disetujui');
    }

    public function test_waka_can_view_detail_show_of_completed_rab_via_direct_url(): void
    {
        $bengkel = $this->createBengkel('TPM', 'Teknik Pemesinan');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rabSelesai = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Pahat Bubut HSS Selesai',
            'status' => 'selesai',
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
            'catatan_review' => 'Disetujui penuh untuk praktikum mesin bubut.',
        ]);
        $this->addDetail($rabSelesai, 'Pahat Bubut HSS 1/2 inch', 10, 75000);

        $response = $this->actingAs($waka)->get(route('superadmin.pengadaan.show', $rabSelesai->id));

        $response->assertOk();
        $response->assertSee('RAB Pahat Bubut HSS Selesai');
        $response->assertSee('Selesai (Barang Diterima)');
        $response->assertSee('Pengadaan telah selesai dan seluruh barang telah diterima fisik ke inventaris bengkel.');
        $response->assertSee('Disetujui penuh untuk praktikum mesin bubut.');
        // Form review disposisi tidak boleh tampil untuk RAB yang sudah selesai
        $response->assertDontSee('Form Disposisi & Keputusan Waka Sarpras', false);
    }

    public function test_waka_can_view_print_preview_of_completed_rab(): void
    {
        $bengkel = $this->createBengkel('TAV', 'Teknik Audio Video');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rabSelesai = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Solder Station ESD Safe',
            'status' => 'selesai',
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDays(3),
        ]);
        $this->addDetail($rabSelesai, 'Solder Station ESD Safe 60W', 4, 350000);

        $response = $this->actingAs($waka)->get(route('superadmin.pengadaan.print', $rabSelesai->id));

        $response->assertOk();
        $response->assertSee('RENCANA ANGGARAN BIAYA (RAB) PENGADAAN BARANG');
        $response->assertSee('RAB Solder Station ESD Safe');
        $response->assertSee('Selesai');
        $response->assertSee('status-selesai');
        $response->assertSee('Rp 1.400.000');
    }

    public function test_completed_rab_cannot_be_reviewed_or_overwritten(): void
    {
        $bengkel = $this->createBengkel('DPIB', 'Desain Pemodelan dan Informasi Bangunan');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rabSelesai = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Meteran Laser Digital',
            'status' => 'selesai',
            'direview_oleh' => $waka->id,
        ]);
        $this->addDetail($rabSelesai, 'Meteran Laser 50M', 2, 450000);

        $response = $this->actingAs($waka)->from(route('superadmin.pengadaan.show', $rabSelesai->id))
            ->post(route('superadmin.pengadaan.review', $rabSelesai->id), [
                'action' => 'rejected',
                'catatan_review' => 'Membatalkan yang sudah selesai',
            ]);

        $response->assertRedirect(route('superadmin.pengadaan.show', $rabSelesai->id));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('pengadaans', [
            'id' => $rabSelesai->id,
            'status' => 'selesai',
        ]);
    }

    public function test_rab_pricing_calculations_and_document_totals_are_rendered_accurately(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer dan Jaringan');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rab = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Peralatan Lab Jaringan Komputer',
            'status' => 'approved',
        ]);

        // Item 1: 4 x 250.000 = 1.000.000
        $this->addDetail($rab, 'Crimping Tool RJ45 Pro', 4, 250000);
        // Item 2: 10 x 75.000 = 750.000
        $this->addDetail($rab, 'LAN Tester Digital RJ45', 10, 75000);

        // Total: 14 unit, Rp 1.750.000
        $responseShow = $this->actingAs($waka)->get(route('superadmin.pengadaan.show', $rab->id));
        $responseShow->assertOk();
        $responseShow->assertViewHas('totalAnggaran', 1750000.0);
        $responseShow->assertViewHas('totalItems', 14);
        $responseShow->assertViewHas('terbilang', 'Satu Juta Tujuh Ratus Lima Puluh Ribu Rupiah');
        $responseShow->assertSee('Rp 1.000.000');
        $responseShow->assertSee('Rp 750.000');
        $responseShow->assertSee('Rp 1.750.000');

        $responsePrint = $this->actingAs($waka)->get(route('superadmin.pengadaan.print', $rab->id));
        $responsePrint->assertOk();
        $responsePrint->assertViewHas('totalAnggaran', 1750000.0);
        $responsePrint->assertViewHas('totalItems', 14);
        $responsePrint->assertSee('Rp 1.000.000');
        $responsePrint->assertSee('Rp 750.000');
        $responsePrint->assertSee('Rp 1.750.000');
        $responsePrint->assertSee('Satu Juta Tujuh Ratus Lima Puluh Ribu Rupiah');
    }

    public function test_waka_profile_calculates_anggaran_acc_using_harga_satuan_including_completed_rabs(): void
    {
        $bengkel = $this->createBengkel('BKG', 'Bengkel Kayu Bangunan');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        // 1. RAB Approved: 2 x 1.500.000 = 3.000.000
        $rab1 = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Mesin Serut Kayu',
            'status' => 'approved',
        ]);
        $this->addDetail($rab1, 'Mesin Ketam Kayu Portable', 2, 1500000);

        // 2. RAB Selesai: 1 x 2.000.000 = 2.000.000
        $rab2 = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Gergaji Mesin Duduk',
            'status' => 'selesai',
        ]);
        $this->addDetail($rab2, 'Table Saw 10 Inch', 1, 2000000);

        // Total ACC = 5.000.000 -> 5.0 Jt
        $response = $this->actingAs($waka)->get(route('profile.edit'));
        $response->assertOk();

        // Verifikasi bahwa Anggaran ACC tidak bernilai 0 (karena bug harga_estimasi sebelumnya)
        $response->assertSee('Anggaran ACC');
        $response->assertSee('Rp 5,0 Jt');
        $response->assertSee('2 Usulan'); // 2 RAB Diverifikasi (approved + selesai)
    }

    public function test_unauthorized_roles_cannot_access_waka_pengadaan_endpoints(): void
    {
        $bengkel = $this->createBengkel('OTO', 'Teknik Ototronik');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $siswa = $this->createUser(['role' => 'peminjam']);

        $rab = $this->createPengadaan($bengkel, $toolman, ['status' => 'selesai']);
        $this->addDetail($rab, 'Scanner OBD2', 1, 1200000);

        // Toolman akses index superadmin -> 403
        $this->actingAs($toolman)->get(route('superadmin.pengadaan.index'))->assertForbidden();
        // Toolman akses show superadmin -> 403
        $this->actingAs($toolman)->get(route('superadmin.pengadaan.show', $rab->id))->assertForbidden();
        // Toolman akses print superadmin -> 403
        $this->actingAs($toolman)->get(route('superadmin.pengadaan.print', $rab->id))->assertForbidden();

        // Siswa akses index superadmin -> 403
        $this->actingAs($siswa)->get(route('superadmin.pengadaan.index'))->assertForbidden();
        // Siswa akses show superadmin -> 403
        $this->actingAs($siswa)->get(route('superadmin.pengadaan.show', $rab->id))->assertForbidden();

        // Logout dan pastikan guest dialihkan ke login
        auth()->logout();
        $this->get(route('superadmin.pengadaan.index'))->assertRedirect(route('login'));
    }
}
