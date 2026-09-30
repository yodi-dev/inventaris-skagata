<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\LokasiPenyimpanan;
use App\Models\Pengadaan;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class RemediationBatch9Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'RPL', string $nama = 'Rekayasa Perangkat Lunak'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'kepala_bengkel' => 'Dr. H. Ahmad Fauzi, M.Kom.',
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'status' => 'aktif',
        ], $attributes));
    }

    private function createCsvFile(string $filename, array $headers, array $rows, string $delimiter = ';'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'test_csv_') . '.csv';
        $handle = fopen($path, 'w');
        fputs($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $headers, $delimiter);
        foreach ($rows as $row) {
            fputcsv($handle, $row, $delimiter);
        }
        fclose($handle);

        return new UploadedFile($path, $filename, 'text/csv', null, true);
    }

    private function createXlsxFileWithColumns(string $filename, int $columnCount): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'test_xlsx_cols_') . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>
</workbook>');

        // Generate column letters for n columns (e.g. A, B, ... Z, AA, AB, ...)
        $colLetters = [];
        for ($i = 0; $i < $columnCount; $i++) {
            $colLetter = '';
            $temp = $i;
            while ($temp >= 0) {
                $colLetter = chr(($temp % 26) + 65) . $colLetter;
                $temp = intdiv($temp, 26) - 1;
            }
            $colLetters[] = $colLetter;
        }

        $sheetRows = "<row r=\"1\">";
        foreach ($colLetters as $idx => $letter) {
            $name = ($idx === 0) ? 'Nama Barang' : "Kolom {$idx}";
            $sheetRows .= "<c r=\"{$letter}1\" t=\"inlineStr\"><is><t>{$name}</t></is></c>";
        }
        $sheetRows .= "</row>";

        $sheetRows .= "<row r=\"2\">";
        foreach ($colLetters as $idx => $letter) {
            $val = ($idx === 0) ? 'Barang Uji Lebar' : "Data {$idx}";
            $sheetRows .= "<c r=\"{$letter}2\" t=\"inlineStr\"><is><t>{$val}</t></is></c>";
        }
        $sheetRows .= "</row>";

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $sheetRows . '</sheetData>
</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return new UploadedFile($path, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    /**
     * 1. Format legacy .xls ditolak secara eksplisit dan informatif.
     */
    public function test_import_rejects_legacy_xls_format_with_informative_error(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        // Berkas biner palsu atau berkas berakhiran .xls
        $tempXls = tempnam(sys_get_temp_dir(), 'test_legacy_') . '.xls';
        file_put_contents($tempXls, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . "Binary BIFF8 OLE Stream Data");

        $file = new UploadedFile($tempXls, 'data_inventaris.xls', 'application/vnd.ms-excel', null, true);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        // Validasi mimes gagal atau parser melempar pesan galat ramah pengguna
        $errorMsg = session('error') ?? (session('errors') ? session('errors')->first('file') : '');
        $this->assertTrue(
            str_contains($errorMsg, '.xlsx atau .csv')
            || str_contains($errorMsg, 'tidak didukung')
            || str_contains($errorMsg, 'warisan')
        );
    }

    /**
     * 2. Berkas CSV dengan kolom melebihi batas 50 ditolak (bukan dipotong diam-diam).
     */
    public function test_import_rejects_csv_with_columns_exceeding_limit_without_silent_truncation(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        // Buat 51 kolom header dan 51 kolom data
        $headers = [];
        $headers[0] = 'Nama Barang';
        for ($i = 1; $i <= 50; $i++) {
            $headers[$i] = "Header Kolom {$i}";
        }

        $row1 = [];
        $row1[0] = 'Kabel Jaringan UTP 50 Kolom';
        for ($i = 1; $i <= 50; $i++) {
            $row1[$i] = "Nilai {$i}";
        }

        $file = $this->createCsvFile('wide_51_columns.csv', $headers, [$row1]);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $errorMsg = session('error');

        $this->assertStringContainsString('terlalu banyak kolom', $errorMsg);
        $this->assertStringContainsString('50', $errorMsg);

        // Pastikan tidak ada data yang terimpor ke database
        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Kabel Jaringan UTP 50 Kolom',
        ]);
    }

    /**
     * 3. Berkas XLSX dengan kolom melebihi batas 50 ditolak dengan pesan informatif.
     */
    public function test_import_rejects_xlsx_with_columns_exceeding_limit(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $file = $this->createXlsxFileWithColumns('wide_sheet_52_cols.xlsx', 52);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $errorMsg = session('error');

        $this->assertStringContainsString('terlalu banyak kolom', $errorMsg);
        $this->assertStringContainsString('50', $errorMsg);

        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Barang Uji Lebar',
        ]);
    }

    /**
     * 4. Penerimaan fisik RAB membuat barang baru inventaris:
     *    - jenis_barang eksplisit = inventaris (meskipun nama mengandung kata "Kabel")
     *    - prefix INV-
     *    - minimum_stok sesuai input eksplisit pengguna (misal 3)
     *    - lokasi_penyimpanan_id = null, sumber_dana_id = null
     *    - StockMovement audit trail tercatat dengan benar.
     */
    public function test_rab_receipt_creates_new_inventaris_with_correct_classification_prefix_and_safe_nulls(): void
    {
        $bengkel = $this->createBengkel('TAV', 'Teknik Audio Video');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka_sarpras']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Alat Praktik Pengujian Kabel Audio',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(2),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
        ]);

        $detail = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null, // Usulan barang baru
            'nama_barang' => 'Kabel Tester Digital Analyzer TAV-01',
            'jenis_barang' => 'inventaris', // Eksplisit inventaris meskipun nama mengandung kata 'Kabel'
            'minimum_stok' => 3,            // Eksplisit dari pengguna
            'spesifikasi' => '2 Channel, LCD Color Display',
            'jumlah' => 2,
            'satuan' => 'Unit',
            'harga_satuan' => 4500000,
        ]);

        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Pastikan status pengadaan selesai
        $pengadaan->refresh();
        $this->assertEquals('selesai', $pengadaan->status);

        // Ambil barang baru yang terbuat
        $detail->refresh();
        $this->assertNotNull($detail->barang_id);

        $newBarang = Barang::findOrFail($detail->barang_id);
        $this->assertEquals('TAV', $bengkel->kode);
        $this->assertEquals($bengkel->id, $newBarang->bengkel_id);
        $this->assertEquals('Kabel Tester Digital Analyzer TAV-01', $newBarang->nama);
        $this->assertEquals('inventaris', $newBarang->jenis_barang);
        $this->assertStringStartsWith('INV-TAV-', $newBarang->kode_barang);
        $this->assertEquals(3, $newBarang->minimum_stok);
        $this->assertEquals(2, $newBarang->stok_total);
        $this->assertEquals(2, $newBarang->stok_tersedia);
        $this->assertEquals(0, $newBarang->stok_dipinjam);
        $this->assertEquals(0, $newBarang->stok_rusak);
        $this->assertEquals('4500000.00', (string) $newBarang->harga);

        // Field yang belum ada di RAB aman dibiarkan null
        $this->assertNull($newBarang->lokasi_penyimpanan_id);
        $this->assertNull($newBarang->sumber_dana_id);

        // Audit Trail StockMovement
        $movement = StockMovement::where('barang_id', $newBarang->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('stok_masuk', $movement->jenis);
        $this->assertEquals(2, $movement->jumlah);
        $this->assertEquals('pengadaan', $movement->referensi_tipe);
        $this->assertEquals($pengadaan->id, $movement->referensi_id);
        $this->assertEquals($toolman->id, $movement->user_id);
    }

    /**
     * 5. Penerimaan fisik RAB membuat barang baru BHP:
     *    - jenis_barang eksplisit = bhp (meskipun nama mengandung kata 'Perangkat' dan satuan 'Set')
     *    - prefix BHP-
     *    - minimum_stok = default schema 0 (jika diinput 0/null)
     *    - lokasi_penyimpanan_id = null, sumber_dana_id = null
     */
    public function test_rab_receipt_creates_new_bhp_with_correct_classification_prefix_and_minimum_stock(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer Jaringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka_sarpras']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Pengadaan Bahan Pembersih Konektor Fiber Optik',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(2),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
        ]);

        $detail = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null, // Barang baru
            'nama_barang' => 'Perangkat Pembersih Ujung Fiber Optik',
            'jenis_barang' => 'bhp', // Eksplisit BHP
            'minimum_stok' => 0,     // Nilai eksplisit
            'spesifikasi' => 'Alkohol isopropil dan tisu pembersih optik sekali pakai',
            'jumlah' => 5,
            'satuan' => 'Set',
            'harga_satuan' => 125000,
        ]);

        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $detail->refresh();
        $newBhp = Barang::findOrFail($detail->barang_id);

        $this->assertEquals('TKJ', $bengkel->kode);
        $this->assertEquals($bengkel->id, $newBhp->bengkel_id);
        $this->assertEquals('Perangkat Pembersih Ujung Fiber Optik', $newBhp->nama);
        $this->assertEquals('bhp', $newBhp->jenis_barang);
        $this->assertStringStartsWith('BHP-TKJ-', $newBhp->kode_barang);
        $this->assertEquals(0, $newBhp->minimum_stok);
        $this->assertEquals(5, $newBhp->stok_total);
        $this->assertEquals(5, $newBhp->stok_tersedia);
        $this->assertNull($newBhp->lokasi_penyimpanan_id);
        $this->assertNull($newBhp->sumber_dana_id);

        $movement = StockMovement::where('barang_id', $newBhp->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('stok_masuk', $movement->jenis);
        $this->assertEquals(5, $movement->jumlah);
    }

    /**
     * 6. Penerimaan fisik RAB untuk barang existing menambahkan stok tanpa membuat row baru.
     */
    public function test_rab_receipt_adds_stock_to_existing_item_with_movement_audit_trail(): void
    {
        $bengkel = $this->createBengkel('TKR', 'Teknik Kendaraan Ringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka_sarpras']);

        $existingBarang = Barang::create([
            'bengkel_id' => $bengkel->id,
            'kode_barang' => 'INV-TKR-001',
            'nama' => 'Kunci Torsi 1/2 Inch King Tony',
            'jenis_barang' => 'inventaris',
            'satuan' => 'Unit',
            'harga' => 850000,
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 2,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'Penambahan Unit Kunci Torsi Bengkel Otomotif',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(2),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
        ]);

        DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $existingBarang->id,
            'nama_barang' => 'Kunci Torsi 1/2 Inch King Tony',
            'spesifikasi' => 'Rentang 40-210 Nm',
            'jumlah' => 4,
            'satuan' => 'Unit',
            'harga_satuan' => 850000,
        ]);

        $initialBarangCount = Barang::count();

        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Pastikan tidak ada row barang baru yang dibuat
        $this->assertEquals($initialBarangCount, Barang::count());

        $existingBarang->refresh();
        $this->assertEquals(14, $existingBarang->stok_total);
        $this->assertEquals(14, $existingBarang->stok_tersedia);

        $movement = StockMovement::where('barang_id', $existingBarang->id)->latest('id')->first();
        $this->assertNotNull($movement);
        $this->assertEquals('stok_masuk', $movement->jenis);
        $this->assertEquals(4, $movement->jumlah);
        $this->assertEquals('pengadaan', $movement->referensi_tipe);
        $this->assertEquals($pengadaan->id, $movement->referensi_id);
    }

    /**
     * 7. Usaha penerimaan ulang (repeat receipt) pada RAB yang sudah 'selesai' atau belum 'approved' ditolak.
     */
    public function test_rab_receipt_rejects_repeat_receipt_on_completed_or_unapproved_request(): void
    {
        $bengkel = $this->createBengkel();
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        // Skenario 1: Status sudah 'selesai'
        $pengadaanSelesai = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Yang Sudah Pernah Diterima',
            'status' => 'selesai',
        ]);
        DetailPengadaan::create([
            'pengadaan_id' => $pengadaanSelesai->id,
            'nama_barang' => 'Barang Contoh',
            'jumlah' => 1,
            'satuan' => 'Unit',
        ]);

        $res1 = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaanSelesai->id));
        $res1->assertStatus(302);
        $res1->assertSessionHas('error');
        $this->assertStringContainsString('hanya dapat dilakukan untuk usulan RAB yang telah disetujui', session('error'));

        // Skenario 2: Status masih 'pending'
        $pengadaanPending = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Menunggu Persetujuan',
            'status' => 'pending',
        ]);
        DetailPengadaan::create([
            'pengadaan_id' => $pengadaanPending->id,
            'nama_barang' => 'Barang Contoh',
            'jumlah' => 1,
            'satuan' => 'Unit',
        ]);

        $res2 = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaanPending->id));
        $res2->assertStatus(302);
        $res2->assertSessionHas('error');
        $this->assertStringContainsString('hanya dapat dilakukan untuk usulan RAB yang telah disetujui', session('error'));
    }

    /**
     * 8. Penerimaan multi-item membatalkan seluruh transaksi (atomic rollback) jika salah satu item gagal.
     */
    public function test_rab_receipt_rolls_back_atomically_if_any_item_fails(): void
    {
        $bengkel1 = $this->createBengkel('B1', 'Bengkel 1');
        $bengkel2 = $this->createBengkel('B2', 'Bengkel 2');

        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel1->id]);
        $waka = $this->createUser(['role' => 'waka_sarpras']);

        // Barang milik bengkel 2 (lintas bengkel ilegal)
        $alienBarang = Barang::create([
            'bengkel_id' => $bengkel2->id,
            'kode_barang' => 'ALIEN-001',
            'nama' => 'Barang Bengkel Lain',
            'jenis_barang' => 'inventaris',
            'satuan' => 'Unit',
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 1,
        ]);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel1->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Multi Item Gagal',
            'status' => 'approved',
            'diajukan_pada' => now()->subDay(),
            'direview_oleh' => $waka->id,
            'direview_pada' => now(),
        ]);

        // Item 1: Usulan baru valid dengan jenis_barang eksplisit
        $detail1 = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null,
            'nama_barang' => 'Item Baru Yang Seharusnya Batal',
            'jenis_barang' => 'inventaris',
            'minimum_stok' => 1,
            'jumlah' => 3,
            'satuan' => 'Unit',
            'harga_satuan' => 100000,
        ]);

        // Item 2: Merujuk ke barang milik bengkel lain (memicu pengecekan DomainException kepemilikan bengkel)
        $detail2 = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => $alienBarang->id,
            'nama_barang' => 'Barang Bengkel Lain',
            'jumlah' => 2,
            'satuan' => 'Unit',
            'harga_satuan' => 200000,
        ]);

        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // Pastikan status pengadaan TIDAK berubah menjadi 'selesai'
        $pengadaan->refresh();
        $this->assertEquals('approved', $pengadaan->status);

        // Pastikan item 1 TIDAK terbuat di database
        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Item Baru Yang Seharusnya Batal',
        ]);

        // Pastikan alienBarang stoknya TIDAK bertambah
        $alienBarang->refresh();
        $this->assertEquals(10, $alienBarang->stok_total);

        // Pastikan TIDAK ADA StockMovement yang terbuat
        $this->assertEquals(0, StockMovement::where('referensi_tipe', 'pengadaan')->where('referensi_id', $pengadaan->id)->count());
    }

    /**
     * 9. Penerimaan usulan barang baru legacy (tanpa jenis_barang) ditolak jika tanpa konfirmasi eksplisit,
     *    dan rollback dilakukan tanpa merusak integritas stok.
     */
    public function test_rab_receipt_blocks_legacy_unclassified_item_without_confirmation(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer Jaringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka_sarpras']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Warisan Tanpa Klasifikasi Jenis Barang',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(3),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
        ]);

        $detailLegacy = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null,
            'nama_barang' => 'Perangkat Tester Jaringan Tanpa Label',
            'jenis_barang' => null, // Legacy record belum terisi jenis_barang
            'minimum_stok' => null,
            'jumlah' => 2,
            'satuan' => 'Unit',
            'harga_satuan' => 500000,
        ]);

        // Toolman memanggil penerimaan tanpa payload konfirmasi items_classification
        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $error = session('error');
        $this->assertStringContainsString('klasifikasi tipe barang', strtolower($error));
        $this->assertStringContainsString('Perangkat Tester Jaringan Tanpa Label', $error);

        // Pastikan status pengadaan tetap approved
        $pengadaan->refresh();
        $this->assertEquals('approved', $pengadaan->status);

        // Pastikan tidak ada barang baru yang terbuat
        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Perangkat Tester Jaringan Tanpa Label',
        ]);
        $this->assertEquals(0, StockMovement::count());
    }

    /**
     * 10. Penerimaan usulan barang baru legacy berhasil jika Toolman memberikan klasifikasi eksplisit saat penerimaan.
     */
    public function test_rab_receipt_succeeds_for_legacy_unclassified_item_when_confirmed_at_receipt(): void
    {
        $bengkel = $this->createBengkel('TAV', 'Teknik Audio Video');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $waka = $this->createUser(['role' => 'waka_sarpras']);

        $pengadaan = Pengadaan::create([
            'bengkel_id' => $bengkel->id,
            'dibuat_oleh' => $toolman->id,
            'judul' => 'RAB Warisan Yang Dikonfirmasi Saat Terima Fisik',
            'status' => 'approved',
            'diajukan_pada' => now()->subDays(3),
            'direview_oleh' => $waka->id,
            'direview_pada' => now()->subDay(),
        ]);

        $detailLegacy = DetailPengadaan::create([
            'pengadaan_id' => $pengadaan->id,
            'barang_id' => null,
            'nama_barang' => 'Kabel Audio Multi-Core Mogami 50M',
            'jenis_barang' => null, // Belum ada jenis_barang di database
            'minimum_stok' => null,
            'jumlah' => 1,
            'satuan' => 'Roll',
            'harga_satuan' => 2500000,
        ]);

        // Toolman menerima sambil menyertakan items_classification
        $response = $this->actingAs($toolman)->post(route('toolman.pengadaan.receive', $pengadaan->id), [
            'items_classification' => [
                $detailLegacy->id => [
                    'jenis_barang' => 'inventaris', // Toolman memilih bahwa kabel multi-core ini adalah inventaris
                    'minimum_stok' => 2,
                ],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $pengadaan->refresh();
        $this->assertEquals('selesai', $pengadaan->status);

        $detailLegacy->refresh();
        $this->assertEquals('inventaris', $detailLegacy->jenis_barang);
        $this->assertEquals(2, $detailLegacy->minimum_stok);
        $this->assertNotNull($detailLegacy->barang_id);

        $newBarang = Barang::findOrFail($detailLegacy->barang_id);
        $this->assertEquals('inventaris', $newBarang->jenis_barang);
        $this->assertStringStartsWith('INV-TAV-', $newBarang->kode_barang);
        $this->assertEquals(2, $newBarang->minimum_stok);
        $this->assertEquals(1, $newBarang->stok_total);
    }

    /**
     * 11. Validasi form usulan RAB mengharuskan jenis_barang untuk item baru saat diajukan (action=submit).
     */
    public function test_rab_store_and_update_validation_rules_for_jenis_barang(): void
    {
        $bengkel = $this->createBengkel('TKJ', 'Teknik Komputer Jaringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        // 1. Submit RAB baru dengan item baru tanpa jenis_barang -> Gagal validasi
        $responseSubmitNoType = $this->actingAs($toolman)->post(route('toolman.pengadaan.store'), [
            'judul' => 'Pengadaan Tanpa Jenis Barang',
            'action' => 'submit',
            'items' => [
                [
                    'barang_id' => null,
                    'nama' => 'Router Board RB750Gr3',
                    'spesifikasi' => '5 Port Gigabit',
                    'jumlah' => 2,
                    'satuan' => 'Unit',
                    'harga_satuan' => 850000,
                    'jenis_barang' => '', // Kosong
                    'minimum_stok' => 1,
                ],
            ],
        ]);

        $responseSubmitNoType->assertStatus(302);
        $responseSubmitNoType->assertSessionHasErrors(['items.0.jenis_barang']);

        // 2. Draft RAB baru dengan item baru tanpa jenis_barang -> Berhasil disimpan sebagai draft
        $responseDraft = $this->actingAs($toolman)->post(route('toolman.pengadaan.store'), [
            'judul' => 'Draft Pengadaan Awal',
            'action' => 'draft',
            'items' => [
                [
                    'barang_id' => null,
                    'nama' => 'Router Board RB750Gr3',
                    'spesifikasi' => '5 Port Gigabit',
                    'jumlah' => 2,
                    'satuan' => 'Unit',
                    'harga_satuan' => 850000,
                    'jenis_barang' => null,
                    'minimum_stok' => null,
                ],
            ],
        ]);

        $responseDraft->assertStatus(302);
        $responseDraft->assertSessionHas('success');
        $pengadaan = Pengadaan::where('judul', 'Draft Pengadaan Awal')->first();
        $this->assertNotNull($pengadaan);
        $this->assertEquals('draft', $pengadaan->status);

        // 3. Update RAB dari draft menjadi submit dengan mengisi jenis_barang -> Berhasil
        $responseUpdate = $this->actingAs($toolman)->put(route('toolman.pengadaan.update', $pengadaan->id), [
            'judul' => 'Draft Pengadaan Siap Ajukan',
            'action' => 'submit',
            'items' => [
                [
                    'barang_id' => null,
                    'nama' => 'Router Board RB750Gr3',
                    'spesifikasi' => '5 Port Gigabit',
                    'jumlah' => 2,
                    'satuan' => 'Unit',
                    'harga_satuan' => 850000,
                    'jenis_barang' => 'inventaris',
                    'minimum_stok' => 1,
                ],
            ],
        ]);

        $responseUpdate->assertStatus(302);
        $responseUpdate->assertSessionHas('success');
        $pengadaan->refresh();
        $this->assertEquals('pending', $pengadaan->status);
        $detail = $pengadaan->detailPengadaans->first();
        $this->assertEquals('inventaris', $detail->jenis_barang);
    }
}
