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

        $rabApproved = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Kunci Momen Disetujui',
            'status' => 'approved',
            'direview_oleh' => $waka->id,
        ]);
        $this->addDetail($rabApproved, 'Kunci Momen 1/2', 2, 750000);

        $response = $this->actingAs($waka)->get(route('superadmin.pengadaan.index'));

        $response->assertOk();
        $response->assertSee('RAB Multitester Digital Selesai');
        $response->assertSee('RAB Kompresor Pending');
        $response->assertSee('RAB Kunci Momen Disetujui');
        $response->assertSee('Selesai');
        $response->assertSee('Pagu Menunggu Penerimaan');
        $response->assertViewHas('totalSelesai', 1);
        $response->assertViewHas('totalPending', 1);
        $response->assertViewHas('totalApproved', 1);
        $response->assertViewHas('totalRAB', 3);
        $response->assertViewHas('totalAnggaranMenungguPenerimaan', 1500000.0);
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

    public function test_rejected_rab_cannot_be_reviewed_again(): void
    {
        $bengkel = $this->createBengkel('DPIB', 'Desain Pemodelan dan Informasi Bangunan');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $rabRejected = $this->createPengadaan($bengkel, $toolman, [
            'judul' => 'RAB Printer 3D Ditolak',
            'status' => 'rejected',
            'direview_oleh' => $waka->id,
            'catatan_review' => 'Pagu tidak mencukupi',
        ]);
        $this->addDetail($rabRejected, 'Printer 3D Pro', 1, 15000000);

        $response = $this->actingAs($waka)->from(route('superadmin.pengadaan.show', $rabRejected->id))
            ->post(route('superadmin.pengadaan.review', $rabRejected->id), [
                'action' => 'approved',
                'catatan_review' => 'Mengubah keputusan menjadi disetujui',
            ]);

        $response->assertRedirect(route('superadmin.pengadaan.show', $rabRejected->id));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('pengadaans', [
            'id' => $rabRejected->id,
            'status' => 'rejected',
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

    public function test_metric_definitions_across_pending_approved_and_selesai(): void
    {
        $bengkel = $this->createBengkel('DKV', 'Desain Komunikasi Visual');
        $waka = $this->createUser(['role' => 'waka', 'name' => 'Waka Sarpras']);
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        // 1. Pending: 1 unit x 1.000.000 = 1.000.000
        $rabPending = $this->createPengadaan($bengkel, $toolman, ['judul' => 'RAB Pending DKV', 'status' => 'pending']);
        $this->addDetail($rabPending, 'Drawing Tablet', 1, 1000000);

        // 2. Approved (Menunggu Penerimaan): 2 unit x 2.000.000 = 4.000.000
        $rabApproved = $this->createPengadaan($bengkel, $toolman, ['judul' => 'RAB Approved DKV', 'status' => 'approved']);
        $this->addDetail($rabApproved, 'Kamera DSLR', 2, 2000000);

        // 3. Selesai (Sudah Diterima Fisik): 1 unit x 3.000.000 = 3.000.000
        $rabSelesai = $this->createPengadaan($bengkel, $toolman, ['judul' => 'RAB Selesai DKV', 'status' => 'selesai']);
        $this->addDetail($rabSelesai, 'Lensa Telefoto', 1, 3000000);

        // 4. Rejected: 1 unit x 500.000 = 500.000
        $rabRejected = $this->createPengadaan($bengkel, $toolman, ['judul' => 'RAB Ditolak DKV', 'status' => 'rejected']);
        $this->addDetail($rabRejected, 'Tripod Portabel', 1, 500000);

        // Verifikasi pada Waka RAB Index:
        // - Pagu Menunggu Penerimaan HANYA mencakup status 'approved' (Rp 4.000.000)
        // - Status 'pending' belum disetujui
        // - Status 'selesai' sudah selesai diterima fisik sehingga tidak lagi menunggu penerimaan
        $responseIndex = $this->actingAs($waka)->get(route('superadmin.pengadaan.index'));
        $responseIndex->assertOk();
        $responseIndex->assertViewHas('totalRAB', 4);
        $responseIndex->assertViewHas('totalPending', 1);
        $responseIndex->assertViewHas('totalApproved', 1);
        $responseIndex->assertViewHas('totalSelesai', 1);
        $responseIndex->assertViewHas('totalRejected', 1);
        $responseIndex->assertViewHas('totalAnggaranMenungguPenerimaan', 4000000.0);
        $responseIndex->assertSee('Pagu Menunggu Penerimaan');
        $responseIndex->assertSee('Rp 4.000.000');

        // Verifikasi pada Waka Profil:
        // - RAB Diverifikasi: 3 usulan (approved, selesai, rejected)
        // - Total Disetujui (ACC): approved (4 Jt) + selesai (3 Jt) = 7.000.000 (7,0 Jt)
        // - Pending TIDAK masuk ACC dan TIDAK masuk diverifikasi
        // - Rejected MASUK diverifikasi, TIDAK masuk ACC
        $responseProfile = $this->actingAs($waka)->get(route('profile.edit'));
        $responseProfile->assertOk();
        $responseProfile->assertSee('RAB Diverifikasi');
        $responseProfile->assertSee('3 Usulan');
        $responseProfile->assertSee('Total Disetujui (ACC)');
        $responseProfile->assertSee('Rp 7,0 Jt');
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
