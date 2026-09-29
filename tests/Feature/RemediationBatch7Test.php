<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use App\Models\Satuan;
use App\Models\StockMovement;
use App\Models\SumberDana;
use App\Models\User;
use App\Http\Controllers\Toolman\SatuanController;
use App\Http\Controllers\Toolman\SumberDanaController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RemediationBatch7Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SatuanController::ensureDefaults();
        SumberDanaController::ensureSchemaReady();
    }

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
        static $counter = 700;
        $counter++;

        return User::create(array_merge([
            'name' => "User {$counter}",
            'email' => "user{$counter}@example.com",
            'password' => Hash::make('password123'),
            'role' => 'toolman',
            'status' => 'aktif',
        ], $attributes));
    }

    private function createCsvFile(string $filename, array $headers, array $rows): UploadedFile
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $headers, ';');
        foreach ($rows as $row) {
            fputcsv($fp, $row, ';');
        }
        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_valid_import_creates_items_and_initial_stock_movements_under_toolman_workshop(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer dan Jaringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TKJ-1',
            'nama' => 'Lab Komputer 1',
        ]);
        $sumberDana = SumberDana::firstOrCreate(['nama' => 'BOS Reguler'], ['kode' => 'BOS']);

        $headers = [
            'Kode Barang (Opsional)',
            'Nama Barang',
            'Tipe (inventaris/bhp)',
            'Satuan',
            'Lokasi Penyimpanan',
            'Sumber Dana',
            'Stok Baik / Bahan',
            'Stok Rusak Ringan',
            'Stok Rusak Berat',
            'Batas Minimum',
            'Harga Satuan (Rp)',
            'Spesifikasi / Keterangan',
        ];

        $rows = [
            [
                'INV-TKJ-101',
                'Laptop Dell Inspiron 14',
                'inventaris',
                'Unit',
                $lokasi->nama,
                $sumberDana->nama,
                '5',
                '1',
                '0',
                '2',
                '8500000',
                'Core i5 RAM 16GB',
            ],
            [
                '', // blank code -> auto generated
                'Kabel Patch Cord Cat6',
                'bhp',
                'Pcs',
                $lokasi->nama,
                $sumberDana->nama,
                '20',
                '0',
                '0',
                '5',
                '25000',
                'Panjang 2 meter',
            ],
        ];

        $file = $this->createCsvFile('import_valid.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));
        $response->assertSessionHas('success');

        // Verifikasi item 1
        $item1 = Barang::where('bengkel_id', $bengkel->id)->where('kode_barang', 'INV-TKJ-101')->first();
        $this->assertNotNull($item1);
        $this->assertEquals('Laptop Dell Inspiron 14', $item1->nama);
        $this->assertEquals('inventaris', $item1->jenis_barang);
        $this->assertEquals(6, $item1->stok_total); // 5 baik + 1 rusak
        $this->assertEquals(5, $item1->stok_tersedia);
        $this->assertEquals(1, $item1->stok_rusak);
        $this->assertEquals($lokasi->id, $item1->lokasi_penyimpanan_id);
        $this->assertEquals($sumberDana->id, $item1->sumber_dana_id);

        // Verifikasi mutasi stok item 1
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $item1->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 6,
        ]);

        // Verifikasi item 2 (auto-generated code)
        $item2 = Barang::where('bengkel_id', $bengkel->id)->where('nama', 'Kabel Patch Cord Cat6')->first();
        $this->assertNotNull($item2);
        $this->assertStringStartsWith('BHP-TKJ-', $item2->kode_barang);
        $this->assertEquals('bhp', $item2->jenis_barang);
        $this->assertEquals(20, $item2->stok_total);
        $this->assertEquals(20, $item2->stok_tersedia);

        // Verifikasi mutasi stok item 2
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $item2->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 20,
        ]);
    }

    public function test_import_rejects_duplicate_item_codes_within_same_uploaded_file(): void
    {
        $bengkel = $this->createBengkel('TKR', 'Teknik Kendaraan Ringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TKR-1',
            'nama' => 'Gudang TKR',
        ]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['TKR-DUP-01', 'Kunci Pas 10mm', 'inventaris', 'Unit', $lokasi->nama],
            ['TKR-DUP-01', 'Kunci Pas 12mm', 'inventaris', 'Unit', $lokasi->nama],
        ];

        $file = $this->createCsvFile('import_duplicate_in_file.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString("Kode barang 'TKR-DUP-01' duplikat di dalam file impor", $errorMessage);
        $this->assertStringContainsString('sama dengan baris 2', $errorMessage);

        // Pastikan tidak ada data yang masuk
        $this->assertEquals(0, Barang::where('bengkel_id', $bengkel->id)->count());
    }

    public function test_import_rejects_code_already_present_in_same_workshop_database_scope(): void
    {
        $bengkel = $this->createBengkel('TITL', 'Teknik Instalasi Tenaga Listrik');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TITL-1',
            'nama' => 'Lab Listrik 1',
        ]);

        // Sudah ada barang dengan kode ini di database
        $existing = Barang::create([
            'bengkel_id' => $bengkel->id,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'kode_barang' => 'EL-EXIST-99',
            'nama' => 'Tang Kombinasi Asli',
            'jenis_barang' => 'inventaris',
            'satuan' => 'Unit',
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 2,
        ]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['EL-EXIST-99', 'Tang Kombinasi Tambahan', 'inventaris', 'Unit', $lokasi->nama],
        ];

        $file = $this->createCsvFile('import_conflict_code.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString("Kode barang 'EL-EXIST-99' sudah terdaftar di bengkel ini", $errorMessage);

        // Verifikasi barang asli tidak tertimpa dan tidak ada suffix acak baru yang dibuat
        $this->assertEquals(1, Barang::where('bengkel_id', $bengkel->id)->count());
        $this->assertEquals('Tang Kombinasi Asli', $existing->fresh()->nama);
    }

    public function test_import_allows_same_code_if_it_belongs_to_different_workshop(): void
    {
        $bengkelA = $this->createBengkel('DPIB', 'Desain Pemodelan');
        $bengkelB = $this->createBengkel('TKP', 'Teknik Konstruksi Properti');

        $lokasiA = LokasiPenyimpanan::create(['bengkel_id' => $bengkelA->id, 'kode' => 'LOK-A', 'nama' => 'Studio Gambar']);
        $lokasiB = LokasiPenyimpanan::create(['bengkel_id' => $bengkelB->id, 'kode' => 'LOK-B', 'nama' => 'Lab Ukur Tanah']);

        // Bengkel A sudah memiliki kode SHARED-CODE-01
        Barang::create([
            'bengkel_id' => $bengkelA->id,
            'lokasi_penyimpanan_id' => $lokasiA->id,
            'kode_barang' => 'SHARED-CODE-01',
            'nama' => 'Meja Gambar Arsitek A',
            'jenis_barang' => 'inventaris',
            'satuan' => 'Unit',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 1,
        ]);

        $toolmanB = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelB->id]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['SHARED-CODE-01', 'Meja Gambar Arsitek B', 'inventaris', 'Unit', $lokasiB->nama],
        ];

        $file = $this->createCsvFile('import_cross_workshop_same_code.csv', $headers, $rows);

        $response = $this->actingAs($toolmanB)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));
        $response->assertSessionHas('success');

        // Kedua bengkel memiliki SHARED-CODE-01 masing-masing secara sah
        $this->assertEquals(1, Barang::where('bengkel_id', $bengkelA->id)->where('kode_barang', 'SHARED-CODE-01')->count());
        $this->assertEquals(1, Barang::where('bengkel_id', $bengkelB->id)->where('kode_barang', 'SHARED-CODE-01')->count());
    }

    public function test_import_rejects_location_belonging_to_another_workshop(): void
    {
        $bengkelMy = $this->createBengkel('TPM', 'Teknik Pemesinan');
        $bengkelOther = $this->createBengkel('TAV', 'Teknik Audio Video');

        $lokasiOther = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkelOther->id,
            'kode' => 'LOK-TAV-1',
            'nama' => 'Studio Recording TAV',
        ]);

        $toolmanMy = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkelMy->id]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['TPM-001', 'Mesin Bubut Mini', 'inventaris', 'Unit', 'Studio Recording TAV'],
        ];

        $file = $this->createCsvFile('import_foreign_location.csv', $headers, $rows);

        $response = $this->actingAs($toolmanMy)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString("Lokasi 'Studio Recording TAV' milik bengkel lain dan tidak dapat digunakan", $errorMessage);

        // Tidak ada barang yang dibuat
        $this->assertEquals(0, Barang::where('bengkel_id', $bengkelMy->id)->count());
    }

    public function test_unassigned_toolman_cannot_import_items_and_is_blocked_with_403(): void
    {
        $unassignedToolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => null]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan'];
        $rows = [
            ['UNASSIGNED-01', 'Barang Tanpa Bengkel', 'inventaris', 'Unit'],
        ];

        $file = $this->createCsvFile('import_unassigned.csv', $headers, $rows);

        $response = $this->actingAs($unassignedToolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertStatus(403);
        $this->assertEquals(0, Barang::count());
    }

    public function test_import_rejects_unknown_master_satuan_without_polluting_master_table(): void
    {
        $bengkel = $this->createBengkel('DKV', 'Desain Komunikasi Visual');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-DKV-1',
            'nama' => 'Lab Komputer Grafis',
        ]);

        $satuanCountBefore = Satuan::count();

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['DKV-001', 'Pen Tablet Wacom', 'inventaris', 'KodiTidakValid', $lokasi->nama],
        ];

        $file = $this->createCsvFile('import_invalid_satuan.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString("Satuan 'KodiTidakValid' tidak terdaftar dalam master data satuan", $errorMessage);

        // Verifikasi tabel master satuans tidak terpolusi
        $this->assertEquals($satuanCountBefore, Satuan::count());
        $this->assertDatabaseMissing('satuans', ['nama' => 'KodiTidakValid']);
        $this->assertEquals(0, Barang::where('bengkel_id', $bengkel->id)->count());
    }

    public function test_import_rejects_unknown_master_sumber_dana_without_polluting_master_table(): void
    {
        $bengkel = $this->createBengkel('TKJ2', 'Teknik Jaringan Dua');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TKJ2-1',
            'nama' => 'Ruang Server',
        ]);

        $sdCountBefore = SumberDana::count();

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan', 'Sumber Dana'];
        $rows = [
            ['SRV-001', 'Server Rack 24U', 'inventaris', 'Unit', $lokasi->nama, 'Dana Sumbangan Gelap 123'],
        ];

        $file = $this->createCsvFile('import_invalid_sd.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString("Sumber dana 'Dana Sumbangan Gelap 123' tidak terdaftar dalam master data sumber dana", $errorMessage);

        // Verifikasi tabel master sumber_danas tidak terpolusi
        $this->assertEquals($sdCountBefore, SumberDana::count());
        $this->assertDatabaseMissing('sumber_danas', ['nama' => 'Dana Sumbangan Gelap 123']);
        $this->assertEquals(0, Barang::where('bengkel_id', $bengkel->id)->count());
    }

    public function test_import_rolls_back_entirely_when_one_row_fails_in_multi_row_file(): void
    {
        $bengkel = $this->createBengkel('TITL2', 'Teknik Listrik Dua');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TITL2-1',
            'nama' => 'Gudang Listrik',
        ]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['ROW-01', 'Barang Baris Pertama Valid', 'inventaris', 'Unit', $lokasi->nama],
            ['ROW-02', 'Barang Baris Kedua Error', 'inventaris', 'Unit', 'LokasiGhaibTidakAda'],
            ['ROW-03', 'Barang Baris Ketiga Valid', 'inventaris', 'Unit', $lokasi->nama],
        ];

        $file = $this->createCsvFile('import_atomic_rollback.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Seluruh baris harus di-rollback: tidak ada barang yang tersimpan
        $this->assertEquals(0, Barang::where('bengkel_id', $bengkel->id)->count());
        $this->assertDatabaseMissing('barangs', ['kode_barang' => 'ROW-01']);
        $this->assertDatabaseMissing('barangs', ['kode_barang' => 'ROW-03']);
    }

    public function test_import_returns_actionable_error_when_database_duplicate_collision_occurs(): void
    {
        $bengkel = $this->createBengkel('TAV2', 'Teknik Audio Video Dua');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TAV2-1',
            'nama' => 'Lab Audio',
        ]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['TAV-RACE-01', 'Speaker Monitor 1', 'inventaris', 'Unit', $lokasi->nama],
            ['TAV-RACE-02', 'Speaker Monitor 2', 'inventaris', 'Unit', $lokasi->nama],
        ];

        $file = $this->createCsvFile('import_race.csv', $headers, $rows);

        // Simulasikan race condition: tepat sebelum TAV-RACE-01 disimpan, ada proses lain yang menyisipkannya ke DB
        Barang::creating(function ($barang) use ($bengkel, $lokasi) {
            static $alreadyInjected = false;
            if (!$alreadyInjected && $barang->kode_barang === 'TAV-RACE-01') {
                $alreadyInjected = true;
                Barang::withoutEvents(function () use ($bengkel, $lokasi) {
                    Barang::create([
                        'bengkel_id' => $bengkel->id,
                        'lokasi_penyimpanan_id' => $lokasi->id,
                        'kode_barang' => 'TAV-RACE-01',
                        'nama' => 'Speaker Monitor Menyela',
                        'jenis_barang' => 'inventaris',
                        'satuan' => 'Unit',
                        'stok_total' => 1,
                        'stok_tersedia' => 1,
                        'stok_dipinjam' => 0,
                        'stok_rusak' => 0,
                        'minimum_stok' => 1,
                    ]);
                });
            }
        });

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString('tabrakan kode barang yang sama dengan proses lain', $errorMessage);

        // Pastikan TAV-RACE-02 tidak tersimpan (transaksi di-rollback penuh)
        $this->assertDatabaseMissing('barangs', ['kode_barang' => 'TAV-RACE-02']);
    }
}
