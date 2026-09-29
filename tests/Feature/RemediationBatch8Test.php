<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use App\Models\Satuan;
use App\Models\StockMovement;
use App\Models\SumberDana;
use App\Models\User;
use App\Support\SpreadsheetSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class RemediationBatch8Test extends TestCase
{
    use RefreshDatabase;

    private function createBengkel(string $kode = 'TKJ', string $nama = 'Teknik Komputer dan Jaringan'): Bengkel
    {
        return Bengkel::create([
            'kode' => $kode,
            'nama' => $nama,
            'kepala_bengkel' => 'Bambang Sudarmono, S.Kom.',
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

    private function createValidXlsxFile(string $filename, array $headers, array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'test_xlsx_') . '.xlsx';
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

        $sheetRows = '';
        $rNum = 1;

        // Header row
        $sheetRows .= "<row r=\"{$rNum}\">";
        foreach ($headers as $cIdx => $hVal) {
            $colLetter = chr(65 + $cIdx);
            $sheetRows .= "<c r=\"{$colLetter}{$rNum}\" t=\"inlineStr\"><is><t>" . htmlspecialchars($hVal) . "</t></is></c>";
        }
        $sheetRows .= "</row>";

        // Data rows
        foreach ($rows as $row) {
            $rNum++;
            $sheetRows .= "<row r=\"{$rNum}\">";
            foreach ($row as $cIdx => $cVal) {
                $colLetter = chr(65 + $cIdx);
                $sheetRows .= "<c r=\"{$colLetter}{$rNum}\" t=\"inlineStr\"><is><t>" . htmlspecialchars((string) $cVal) . "</t></is></c>";
            }
            $sheetRows .= "</row>";
        }

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . $sheetRows . '</sheetData></worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return new UploadedFile($path, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function test_spreadsheet_sanitizer_escapes_formula_characters_and_preserves_legitimate_values(): void
    {
        // 1. Karakter formula di awal harus di-prefix tanda petik tunggal
        $this->assertEquals("'=1+1", SpreadsheetSanitizer::escape('=1+1'));
        $this->assertEquals("'+cmd|' /C calc'!A0", SpreadsheetSanitizer::escape("+cmd|' /C calc'!A0"));
        $this->assertEquals("'-2+3", SpreadsheetSanitizer::escape('-2+3'));
        $this->assertEquals("'@SUM(1,2)", SpreadsheetSanitizer::escape('@SUM(1,2)'));

        // 2. Tab dan line break di awal harus di-escape
        $this->assertEquals("'\tTAB_VALUE", SpreadsheetSanitizer::escape("\tTAB_VALUE"));
        $this->assertEquals("'\n=FORMULA_AFTER_LF", SpreadsheetSanitizer::escape("\n=FORMULA_AFTER_LF"));
        $this->assertEquals("'\r\n@FORMULA_AFTER_CRLF", SpreadsheetSanitizer::escape("\r\n@FORMULA_AFTER_CRLF"));

        // 3. String normal tidak diubah
        $this->assertEquals('Laptop ASUS ExpertBook', SpreadsheetSanitizer::escape('Laptop ASUS ExpertBook'));
        $this->assertEquals('INV-TKJ-0001', SpreadsheetSanitizer::escape('INV-TKJ-0001'));
        $this->assertEquals('', SpreadsheetSanitizer::escape(''));
        $this->assertEquals('', SpreadsheetSanitizer::escape(null));
    }

    public function test_toolman_mutasi_export_escapes_cells_and_preserves_numeric_quantities(): void
    {
        $bengkel = $this->createBengkel('TAV', 'Teknik Audio Video');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TAV-1',
            'nama' => '=LokasiFormula',
        ]);

        $barang1 = Barang::create([
            'bengkel_id' => $bengkel->id,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'kode_barang' => '-KODE-MINUS',
            'nama' => '=HYPERLINK("http://malicious.com","Klik")',
            'jenis_barang' => 'inventaris',
            'satuan' => "\tUnitTab",
            'harga' => 1500000,
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 2,
        ]);

        $barang2 = Barang::create([
            'bengkel_id' => $bengkel->id,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'kode_barang' => '+KODE-PLUS',
            'nama' => '@SUM(A1:B1)',
            'jenis_barang' => 'bhp',
            'satuan' => 'Pcs',
            'harga' => 50000,
            'stok_total' => 20,
            'stok_tersedia' => 20,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 5,
        ]);

        // Catat mutasi stok masuk (+10) dan sirkulasi keluar (-5)
        StockMovement::create([
            'barang_id' => $barang1->id,
            'user_id' => $toolman->id,
            'jenis' => 'stok_masuk',
            'jumlah' => 10,
            'keterangan' => "\n=KeteranganFormulaLineBreak",
            'created_at' => now(),
        ]);

        StockMovement::create([
            'barang_id' => $barang2->id,
            'user_id' => $toolman->id,
            'jenis' => 'bhp_keluar',
            'jumlah' => 5,
            'keterangan' => "+PemakaianPraktikSiswa",
            'created_at' => now(),
        ]);

        $response = $this->actingAs($toolman)->get(route('toolman.mutasi.export-excel'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        $content = $response->getContent();

        $assertEscaped = function (string $needle) use ($content) {
            $escapedNeedle = htmlspecialchars($needle, ENT_QUOTES, 'UTF-8');
            $this->assertTrue(
                str_contains($content, $needle) || str_contains($content, $escapedNeedle),
                "Expected export to contain either '{$needle}' or '{$escapedNeedle}'"
            );
        };

        // 1. Verifikasi sel teks user-controlled di-escape dengan tanda petik
        $assertEscaped("'-KODE-MINUS");
        $assertEscaped("'+KODE-PLUS");
        $assertEscaped("'=HYPERLINK");
        $assertEscaped("'@SUM");
        $assertEscaped("'\tUnitTab");
        $assertEscaped("'=LokasiFormula");
        $assertEscaped("'\n=KeteranganFormulaLineBreak");
        $assertEscaped("'+PemakaianPraktikSiswa");

        // 2. Verifikasi kuantitas sah numerik (+10 dan -5) dan harga (Rp 1.500.000) TIDAK dirusak
        $this->assertStringContainsString('+10', $content);
        $this->assertStringContainsString('-5', $content);
        $this->assertStringContainsString('Rp 1.500.000', $content);
    }

    public function test_superadmin_mutasi_and_konsumsi_exports_escape_formula_cells_safely(): void
    {
        $waka = $this->createUser(['role' => 'waka']);
        $bengkel = $this->createBengkel('DPIB', 'Desain Pemodelan dan Informasi Bangunan');
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-DPIB-1',
            'nama' => 'Studio Gambar',
        ]);
        $sumberDana = SumberDana::create([
            'kode' => 'BOS',
            'nama' => '@BOSReguler',
        ]);

        $barang = Barang::create([
            'bengkel_id' => $bengkel->id,
            'lokasi_penyimpanan_id' => $lokasi->id,
            'sumber_dana_id' => $sumberDana->id,
            'kode_barang' => '=KODE-SA',
            'nama' => '+Kertas Kalkir A3',
            'jenis_barang' => 'bhp',
            'satuan' => 'Rim',
            'harga' => 125000,
            'stok_total' => 15,
            'stok_tersedia' => 12,
            'stok_dipinjam' => 0,
            'stok_rusak' => 0,
            'minimum_stok' => 2,
        ]);

        StockMovement::create([
            'barang_id' => $barang->id,
            'user_id' => $waka->id,
            'jenis' => 'bhp_keluar',
            'jumlah' => 3,
            'keterangan' => "=PenggunaanPraktik",
            'created_at' => now(),
        ]);

        // 1. Ekspor Mutasi Waka / Superadmin
        $mutasiResp = $this->actingAs($waka)->get(route('superadmin.laporan.mutasi.excel', ['download' => 1]));
        $mutasiResp->assertOk();
        $mutasiContent = $mutasiResp->getContent();

        $assertMutasi = function (string $needle) use ($mutasiContent) {
            $escapedNeedle = htmlspecialchars($needle, ENT_QUOTES, 'UTF-8');
            $this->assertTrue(
                str_contains($mutasiContent, $needle) || str_contains($mutasiContent, $escapedNeedle),
                "Expected mutasi export to contain '{$needle}'"
            );
        };

        $assertMutasi("'=KODE-SA");
        $assertMutasi("'+Kertas Kalkir A3");
        $assertMutasi("'@BOSReguler");
        $assertMutasi("'=PenggunaanPraktik");
        $this->assertStringContainsString('-3', $mutasiContent);

        // 2. Ekspor Konsumsi BHP Waka / Superadmin
        $konsumsiResp = $this->actingAs($waka)->get(route('superadmin.laporan.konsumsi.excel', ['download' => 1]));
        $konsumsiResp->assertOk();
        $konsumsiContent = $konsumsiResp->getContent();

        $assertKonsumsi = function (string $needle) use ($konsumsiContent) {
            $escapedNeedle = htmlspecialchars($needle, ENT_QUOTES, 'UTF-8');
            $this->assertTrue(
                str_contains($konsumsiContent, $needle) || str_contains($konsumsiContent, $escapedNeedle),
                "Expected konsumsi export to contain '{$needle}'"
            );
        };

        $assertKonsumsi("'=KODE-SA");
        $assertKonsumsi("'+Kertas Kalkir A3");
        $assertKonsumsi("'@BOSReguler");
        $this->assertStringContainsString('-3', $konsumsiContent);
    }

    public function test_import_rejects_corrupted_archive_with_informative_error(): void
    {
        $bengkel = $this->createBengkel('TITL', 'Teknik Instalasi Tenaga Listrik');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $fakeCorruptPath = tempnam(sys_get_temp_dir(), 'corrupt_') . '.xlsx';
        $z = new ZipArchive();
        $z->open($fakeCorruptPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $z->addFromString('test.txt', 'hello world');
        $z->close();
        // Truncate file sehingga End of Central Directory hilang dan arsip corrupt
        $raw = file_get_contents($fakeCorruptPath);
        file_put_contents($fakeCorruptPath, substr($raw, 0, strlen($raw) - 25));

        $file = new UploadedFile($fakeCorruptPath, 'corrupt.xlsx', 'application/zip', null, true);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertTrue(
            str_contains($errorMsg, 'bukan arsip spreadsheet Excel')
            || str_contains($errorMsg, 'Gagal membuka file Excel')
            || str_contains($errorMsg, 'tidak konsisten atau rusak')
            || str_contains($errorMsg, 'tidak valid')
        );
    }

    public function test_import_rejects_malformed_xml_with_informative_error(): void
    {
        $bengkel = $this->createBengkel('TITL2', 'Teknik Instalasi Tenaga Listrik Dua');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $path = tempnam(sys_get_temp_dir(), 'malformed_xml_') . '.xlsx';
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

        // File XML sengaja dibuat rusak (unclosed tags)
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0"?><worksheet><sheetData><row><c><is><t>Unclosed');
        $zip->close();

        $file = new UploadedFile($path, 'malformed.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertStringContainsString('Format XML pada file spreadsheet rusak atau tidak dapat dibaca', $errorMsg);
    }

    public function test_import_rejects_archive_exceeding_entry_limit(): void
    {
        $bengkel = $this->createBengkel('TITL3', 'Teknik Listrik Tiga');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $path = tempnam(sys_get_temp_dir(), 'excess_zip_') . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Buat 105 entri di dalam arsip (batas adalah 100 entri)
        for ($i = 1; $i <= 105; $i++) {
            $zip->addFromString("file_{$i}.txt", "test content {$i}");
        }
        $zip->close();

        $file = new UploadedFile($path, 'excessive_entries.xlsx', 'application/zip', null, true);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertStringContainsString('terlalu banyak entri', $errorMsg);
    }

    public function test_import_rejects_file_exceeding_row_limit(): void
    {
        $bengkel = $this->createBengkel('TP', 'Teknik Pemesinan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TP-1',
            'nama' => 'Lab Bubut',
        ]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [];
        // Buat 2005 baris (batas adalah 2000)
        for ($i = 1; $i <= 2005; $i++) {
            $rows[] = ["TP-ITEM-{$i}", "Pahat Bubut {$i}", 'inventaris', 'Unit', $lokasi->nama];
        }

        $file = $this->createCsvFile('huge_rows.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertStringContainsString('melebihi batas maksimal 2000 baris', $errorMsg);
    }

    public function test_import_xlsx_valid_works_end_to_end_and_creates_stock_movement(): void
    {
        $bengkel = $this->createBengkel('TKR', 'Teknik Kendaraan Ringan');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TKR-1',
            'nama' => 'Bengkel Otomotif',
        ]);
        $satuan = Satuan::create(['nama' => 'Unit', 'singkatan' => 'unt']);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['TKR-XLSX-01', 'Kunci Momen 1/2 Inch', 'inventaris', 'Unit', $lokasi->nama],
            ['TKR-XLSX-02', 'Scanner OBD2 Bluetooth', 'inventaris', 'Unit', $lokasi->nama],
        ];

        $file = $this->createValidXlsxFile('valid_items.xlsx', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('toolman.barang.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('barangs', [
            'bengkel_id' => $bengkel->id,
            'kode_barang' => 'TKR-XLSX-01',
            'nama' => 'Kunci Momen 1/2 Inch',
        ]);

        $this->assertDatabaseHas('barangs', [
            'bengkel_id' => $bengkel->id,
            'kode_barang' => 'TKR-XLSX-02',
            'nama' => 'Scanner OBD2 Bluetooth',
        ]);

        $this->assertEquals(2, StockMovement::where('keterangan', 'like', '%Import Excel%')->count());
    }

    public function test_import_xml_parsing_prevents_xxe_external_entity_fetching(): void
    {
        $bengkel = $this->createBengkel('TAV3', 'Teknik Audio Video Tiga');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);

        $path = tempnam(sys_get_temp_dir(), 'xxe_test_') . '.xlsx';
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

        // Injeksi XXE payload dengan DOCTYPE dan ENTITY eksternal
        $xxeSheetXml = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE worksheet [
  <!ENTITY xxe SYSTEM "http://127.0.0.1:54321/xxe_probe">
]>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="inlineStr"><is><t>&xxe;</t></is></c>
      <c r="B1" t="inlineStr"><is><t>Nama Barang</t></is></c>
    </row>
  </sheetData>
</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $xxeSheetXml);
        $zip->close();

        $file = new UploadedFile($path, 'xxe.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        // Request harus redirect dengan error (karena entity eksternal diblokir LIBXML_NONET atau gagal resolve)
        $response->assertRedirect();
        $this->assertDatabaseMissing('barangs', ['nama' => 'Nama Barang']);
    }

    public function test_import_preserves_all_or_nothing_transaction_behavior(): void
    {
        $bengkel = $this->createBengkel('TKJ2', 'Teknik Komputer Jaringan Dua');
        $toolman = $this->createUser(['role' => 'toolman', 'bengkel_id' => $bengkel->id]);
        $lokasi = LokasiPenyimpanan::create([
            'bengkel_id' => $bengkel->id,
            'kode' => 'LOK-TKJ2-1',
            'nama' => 'Lab Server',
        ]);

        $headers = ['Kode Barang (Opsional)', 'Nama Barang', 'Tipe (inventaris/bhp)', 'Satuan', 'Lokasi Penyimpanan'];
        $rows = [
            ['TKJ-ALL-01', 'Router MikroTik CCR', 'inventaris', 'Unit', $lokasi->nama],
            ['TKJ-ALL-02', 'Switch Cisco Catalyst', 'inventaris', 'Unit', 'Lokasi Tidak Ada'],
        ];

        $file = $this->createCsvFile('all_or_nothing.csv', $headers, $rows);

        $response = $this->actingAs($toolman)->post(route('toolman.barang.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertStringContainsString('Import dibatalkan', $errorMsg);
        $this->assertStringContainsString('Lokasi \'Lokasi Tidak Ada\' tidak ditemukan', $errorMsg);

        // Pastikan baris 1 (TKJ-ALL-01) tidak tersimpan sama sekali di database (atomik rollback)
        $this->assertDatabaseMissing('barangs', ['kode_barang' => 'TKJ-ALL-01']);
        $this->assertDatabaseMissing('barangs', ['kode_barang' => 'TKJ-ALL-02']);
    }
}
