<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Barang - {{ $barang->nama }} ({{ $barang->kode_barang }}) - {{ $bengkel->nama ?? 'Bengkel' }} - SMKN 3 Yogyakarta</title>
    <style>
        /* === RESET & PAGE SETUP === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Ukuran Kertas Cetak A4 Landscape Standar Dinas */
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #000000;
            background-color: #f1f5f9;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Wrapper Pratinjau Layar */
        .preview-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 16px 40px 16px;
            box-sizing: border-box;
        }

        /* Toolbar Pratinjau (Hanya Layar) */
        .no-print-toolbar {
            width: 297mm;
            max-width: 100%;
            background: #1e293b;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            margin: 16px auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            font-size: 13px;
        }

        .toolbar-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .toolbar-badge {
            background: #059669;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: none;
        }

        .btn-back {
            background: #334155;
            color: #f8fafc;
        }

        .btn-back:hover {
            background: #475569;
        }

        .btn-print {
            background: #059669;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #047857;
        }

        /* Lembar Fisik Dokumen Cetak (A4 Landscape 297mm x 210mm) */
        .sheet-container {
            width: 297mm;
            min-height: 210mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 10mm 12mm 10mm 12mm;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
            border: 1px solid #cbd5e1;
            box-sizing: border-box;
            position: relative;
        }

        /* KOP SURAT DINAS */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-logo {
            width: 65px;
            vertical-align: middle;
            text-align: center;
            padding-right: 12px;
        }

        .kop-logo img {
            width: 55px;
            height: auto;
            max-height: 60px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .kop-text {
            vertical-align: middle;
            text-align: center;
        }

        .kop-instansi-1 {
            font-size: 9.5pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
        }

        .kop-instansi-2 {
            font-size: 10pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
        }

        .kop-sekolah {
            font-size: 13pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 1px;
            margin: 1px 0;
        }

        .kop-bengkel {
            font-size: 10.5pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 7.5pt;
            color: #222222;
            margin-top: 2px;
        }

        /* Garis Pemisah Kop Ganda */
        .kop-divider {
            border-top: 2px solid #000000;
            border-bottom: 0.5px solid #000000;
            height: 2px;
            margin: 4px 0 10px 0;
        }

        /* JUDUL DOKUMEN */
        .doc-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* METADATA KARTU BARANG (2 KOLOM SESUAI KARTU BARANG.MD) */
        .meta-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }

        .meta-col {
            flex: 1;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 130px;
            color: #111111;
        }

        .meta-sep {
            width: 15px;
            text-align: center;
        }

        .meta-val {
            font-weight: bold;
            color: #000000;
        }

        /* TABEL MUTASI KARTU BARANG (SESUAI KARTU BARANG.MD) */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .ledger-table th, 
        .ledger-table td {
            border: 1px solid #000000;
            padding: 4px 5px;
            font-size: 8pt;
        }

        .ledger-table thead th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        /* TANDA TANGAN */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .signature-space {
            height: 48px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* CETAK (@media print) */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .preview-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .sheet-container {
                width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .ledger-table thead th {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="preview-wrapper">

        <!-- TOOLBAR PRATINJAU (TIDAK TERCETAK) -->
        <div class="no-print-toolbar">
            <div class="toolbar-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Pratinjau Kartu Barang Resmi - {{ $barang->nama }}</span>
                <span class="toolbar-badge">A4 Landscape</span>
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('toolman.barang.index') }}" class="btn-toolbar btn-back">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Katalog
                </a>
                <button type="button" onclick="window.print()" class="btn-toolbar btn-print">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak / Simpan PDF (Ctrl + P)
                </button>
            </div>
        </div>

        <!-- LEMBAR FISIK KARTU BARANG (A4 LANDSCAPE) -->
        <div class="sheet-container">

            <!-- KOP SURAT RESMI -->
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        <img src="{{ asset('logo.png') }}" alt="Logo SMKN 3 Yogyakarta">
                    </td>
                    <td class="kop-text">
                        <div class="kop-instansi-1">PEMERINTAH DAERAH ISTIMEWA YOGYAKARTA</div>
                        <div class="kop-instansi-2">DINAS PENDIDIKAN, PEMUDA, DAN OLAHRAGA</div>
                        <div class="kop-sekolah">SMK NEGERI 3 YOGYAKARTA</div>
                        <div class="kop-bengkel">{{ strtoupper($bengkel->nama ?? 'BENGKEL PRAKTIK KEJURUAN') }}</div>
                        <div class="kop-alamat">Jalan R.W. Monginsidi No. 2 Yogyakarta 55233 &bull; Telepon: (0274) 513507 &bull; Laman: smkn3jogja.sch.id</div>
                    </td>
                </tr>
            </table>

            <div class="kop-divider"></div>

            <!-- JUDUL DOKUMEN -->
            <div class="doc-header">
                <div class="doc-title">KARTU BARANG</div>
            </div>

            <!-- METADATA KARTU BARANG (SESUAI KARTU BARANG.MD) -->
            <div class="meta-container">
                <!-- Kolom Kiri -->
                <div class="meta-col">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Nomor Kartu</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">#KB-{{ $barang->kode_barang }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Golongan</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">
                                {{ $barang->jenis_barang === 'bhp' ? 'Bahan Habis Pakai (BHP)' : 'Barang Inventaris (Alat Praktik)' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="meta-label">Nomor Induk</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">{{ $barang->kode_barang }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Nama Barang</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">{{ strtoupper($barang->nama) }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Spesifikasi Barang</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">{{ $barang->deskripsi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Kolom Kanan -->
                <div class="meta-col">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Merk</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">-</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Type / Satuan</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">{{ $barang->satuan ?? 'Unit' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Kode Barang</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val font-mono">{{ $barang->kode_barang }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Lokasi Penyimpanan</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val"></td>
                        </tr>
                        <tr>
                            <td class="meta-label" style="padding-left: 15px;">&bull; Gudang</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">{{ $bengkel->nama ?? 'Gudang Utama Bengkel' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label" style="padding-left: 15px;">&bull; Lemari / Rak</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">{{ $barang->lokasiPenyimpanan->nama ?? '-' }} {{ $barang->lokasiPenyimpanan ? '(' . $barang->lokasiPenyimpanan->kode . ')' : '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- TABEL MUTASI KARTU BARANG (SESUAI FORMAT KARTU BARANG.MD) -->
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 75px;">Tanggal</th>
                        <th colspan="2" style="width: 100px;">Masuk</th>
                        <th colspan="2" style="width: 100px;">Keluar</th>
                        <th colspan="2" style="width: 110px;">Persediaan</th>
                        <th rowspan="2" style="width: 70px;">Paraf Petugas</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th style="width: 50px;">Baik</th>
                        <th style="width: 50px;">Rusak</th>
                        <th style="width: 50px;">Baik</th>
                        <th style="width: 50px;">Rusak</th>
                        <th style="width: 55px;">Baik</th>
                        <th style="width: 55px;">Rusak</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rowCount = count($movementRows);
                        $minRows = 12; // Jumlah minimum baris agar kartu tercetak penuh dan rapi
                    @endphp

                    @foreach ($movementRows as $row)
                        <tr>
                            <td class="text-center font-mono" style="font-size: 7.5pt;">{{ $row['tanggal'] }}</td>
                            <td class="text-center font-bold">{{ $row['masuk_baik'] }}</td>
                            <td class="text-center font-bold" style="color: #b91c1c;">{{ $row['masuk_rusak'] }}</td>
                            <td class="text-center font-bold">{{ $row['keluar_baik'] }}</td>
                            <td class="text-center font-bold" style="color: #b91c1c;">{{ $row['keluar_rusak'] }}</td>
                            <td class="text-center font-bold" style="background-color: #f8fafc;">{{ $row['sisa_baik'] }}</td>
                            <td class="text-center font-bold" style="background-color: #f8fafc; color: #b91c1c;">{{ $row['sisa_rusak'] }}</td>
                            <td class="text-center font-mono" style="font-size: 7pt;"></td>
                            <td style="font-size: 7.5pt;">{{ $row['keterangan'] }}</td>
                        </tr>
                    @endforeach

                    <!-- Baris Kosong Cadangan Pengisian Manual Fisik -->
                    @for ($i = $rowCount; $i < $minRows; $i++)
                        <tr>
                            <td style="height: 19px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- TANDA TANGAN PENGESAHAN -->
            <table class="signature-table">
                <tr>
                    <td style="text-align: left; padding-left: 30px;">
                        <div>Mengetahui,</div>
                        <div style="font-weight: bold; margin-top: 1px;">Kepala Bengkel / Laboratorium</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">( ............................................................ )</div>
                        <div style="font-size: 8pt; color: #333333; margin-top: 1px;">NIP. .......................................................</div>
                    </td>
                    <td style="text-align: right; padding-right: 30px;">
                        <div>Yogyakarta, {{ now()->translatedFormat('d F Y') }}</div>
                        <div style="font-weight: bold; margin-top: 1px;">Pengurus Barang / Toolman</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">{{ strtoupper(auth()->user()->name ?? 'Toolman Bengkel') }}</div>
                        <div style="font-size: 8pt; color: #333333; margin-top: 1px;">NIP/ID. {{ auth()->user()->nomor_identitas ?? '.......................................................' }}</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>
</html>

