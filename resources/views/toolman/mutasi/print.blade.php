<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Stok - {{ $bengkel->nama ?? 'Bengkel' }} - SMKN 3 Yogyakarta (F4 Landscape)</title>
    <style>
        /* === RESET & PAGE SETUP === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Definisi Fisik Ukuran Kertas F4 (Folio) Landscape: 330mm x 215mm */
        @page {
            size: 330mm 215mm;
            margin: 5mm 7mm 7mm 7mm;
        }

        html, body {
            min-width: 330mm;
        }

        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 7.5pt;
            color: #000000;
            background-color: #cbd5e1;
            line-height: 1.25;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Wrapper Pratinjau di Layar */
        .preview-wrapper {
            width: 100%;
            min-width: 330mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 16px 40px 16px;
            box-sizing: border-box;
        }

        /* Container Dokumen di Layar (Fisik F4 Landscape 330mm dengan Tinggi Otomatis Menampung Semua Baris) */
        .page-container {
            width: 330mm;
            min-height: 215mm;
            height: auto;
            margin: 14px auto 30px auto;
            background: #ffffff;
            padding: 7mm 8mm 10mm 8mm;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.22);
            border: 1px solid #94a3b8;
            border-radius: 2px;
            box-sizing: border-box;
        }

        /* === TOOLBAR PRATINJAU (TIDAK TERCETAK) === */
        .no-print-bar {
            width: 330mm;
            margin: 12px auto 0 auto;
            padding: 8px 14px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 8.5pt;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
        }

        .no-print-guide {
            width: 330mm;
            margin: 0 auto;
            padding: 8px 14px;
            background: #1e293b;
            color: #e2e8f0;
            font-size: 7.5pt;
            border-top: 1px solid #334155;
            border-radius: 0 0 6px 6px;
            line-height: 1.4;
            box-sizing: border-box;
        }

        .no-print-bar .badge-f4 {
            display: inline-block;
            background: #059669;
            color: #ffffff;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            margin-left: 8px;
            letter-spacing: 0.5px;
        }

        .no-print-bar .btn {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 8.5pt;
            font-weight: bold;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity 0.15s;
        }

        .btn-print {
            background-color: #2563eb;
            color: #ffffff;
            margin-right: 6px;
        }

        .btn-print:hover {
            opacity: 0.9;
        }

        .btn-close {
            background-color: #475569;
            color: #ffffff;
        }

        .btn-close:hover {
            opacity: 0.9;
        }

        /* === KOP DOKUMEN RESMI PEMDA DIY & SMKN 3 YOGYAKARTA === */
        .kop-surat {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 4px;
            margin-bottom: 1.5px;
        }

        .kop-subline {
            border-bottom: 0.75px solid #000000;
            margin-bottom: 8px;
        }

        .kop-instansi {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #000000;
        }

        .kop-sekolah {
            font-size: 11pt;
            font-weight: 800;
            letter-spacing: 0.6px;
            margin: 1px 0;
            text-transform: uppercase;
            color: #000000;
        }

        .kop-alamat {
            font-size: 7pt;
            color: #1f2937;
        }

        /* === JUDUL LAPORAN === */
        .judul-laporan {
            text-align: center;
            margin-bottom: 8px;
        }

        .judul-laporan h1 {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-decoration: underline;
        }

        .judul-laporan p {
            font-size: 7.5pt;
            color: #374151;
            margin-top: 1px;
        }

        /* === TABEL METADATA INFORMASI DOKUMEN === */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7.5pt;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .meta-label {
            width: 16%;
            font-weight: bold;
            color: #1f2937;
        }

        .meta-colon {
            width: 2%;
            text-align: center;
        }

        .meta-value {
            width: 32%;
            color: #000000;
        }

        /* === RINGKASAN REKAPITULASI (KOMPAK & HEMAT TINTA) === */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: 1px solid #000000;
            font-size: 7.5pt;
        }

        .kpi-table th {
            background-color: #f3f4f6;
            border: 1px solid #000000;
            padding: 3px 4px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7pt;
            color: #1f2937;
        }

        .kpi-table td {
            border: 1px solid #000000;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8.5pt;
        }

        /* === TABEL DATA TRANSAKSI UTAMA (FIXED LAYOUT F4 LANDSCAPE) === */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            table-layout: fixed;
            margin-bottom: 10px;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background-color: #f3f4f6;
            color: #000000;
            font-weight: bold;
            border: 1px solid #000000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            text-transform: uppercase;
            font-size: 6.8pt;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .data-table td {
            border: 1px solid #000000;
            padding: 3px 3px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        /* Alternating row background sangat halus */
        .data-table tbody tr:nth-child(even) td {
            background-color: #fcfcfc;
        }

        /* Baris Total */
        .total-row td {
            background-color: #f3f4f6 !important;
            font-weight: bold;
            border-top: 1.5px solid #000000;
            border-bottom: 1.5px solid #000000;
            font-size: 7.2pt;
        }

        /* === LEMBAR PENGESAHAN TANDA TANGAN === */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 7.5pt;
            page-break-inside: avoid;
        }

        .ttd-table td {
            padding: 2px;
            vertical-align: top;
        }

        .ttd-space {
            height: 42px;
        }

        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* === MEDIA PRINT KHUSUS F4 LANDSCAPE (330mm x 215mm) === */
        @media print {
            @page {
                size: 330mm 215mm;
                margin: 5mm 7mm 7mm 7mm;
            }

            *, *:before, *:after {
                box-sizing: border-box !important;
            }

            html, body {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
            }

            .preview-wrapper {
                width: 100% !important;
                min-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }

            .no-print-bar,
            .no-print-guide {
                display: none !important;
            }

            .page-container {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .data-table {
                width: 100% !important;
                max-width: 100% !important;
                table-layout: fixed !important;
            }

            .data-table th, .kpi-table th, .total-row td {
                background-color: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr {
                page-break-inside: avoid;
            }

            .ttd-table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="preview-wrapper">
        <!-- Bar Navigasi Pratinjau (Hanya terlihat di layar) -->
        <div class="no-print-bar">
        <div>
            <strong>Pratinjau Cetak Log Mutasi</strong> &bull; {{ $bengkel->nama ?? 'Bengkel' }} ({{ $totalRecords }} data)
            <span class="badge-f4">Kertas F4 / Folio Landscape (330 &times; 215 mm)</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-close">
                ✕ Tutup
            </button>
        </div>
    </div>
    <div class="no-print-guide">
        💡 <strong>Petunjuk Agar Cetakan Pas Lembaran F4 Landscape &amp; Tidak Mengotak (Margin Browser):</strong>
        <ul style="margin-top: 4px; margin-left: 18px; line-height: 1.5;">
            <li>Buka <strong>More settings (Setelan lainnya)</strong> pada jendela pratinjau cetak browser.</li>
            <li><strong>Paper size (Ukuran kertas):</strong> Pilih <strong>Folio / F4</strong> (atau gunakan <em>Legal</em> jika Folio tidak tersedia di daftar printer Anda).</li>
            <li><strong>Margins (Margin):</strong> Ubah dari <em>Default</em> menjadi <strong>None (Tidak ada)</strong> atau <strong>Minimum</strong> agar area atas-bawah-kiri-kanan tidak tertekan menjadi kotak.</li>
            <li><strong>Options (Opsi):</strong> Centang <strong>Background graphics</strong> agar arsiran header tabel tercetak dengan jelas.</li>
        </ul>
    </div>

    <!-- Lembar Dokumen F4 Landscape -->
    <div class="page-container">
        
        <!-- KOP RESMI DINAS PENDIDIKAN & SMKN 3 YOGYAKARTA -->
        <div class="kop-surat">
            <div class="kop-instansi">Pemerintah Daerah Daerah Istimewa Yogyakarta</div>
            <div class="kop-instansi">Dinas Pendidikan, Pemuda, dan Olahraga</div>
            <div class="kop-sekolah">SMK NEGERI 3 YOGYAKARTA</div>
            <div class="kop-alamat">
                Jl. R.W. Monginsidi No. 2, Cokrodiningratan, Jetis, Kota Yogyakarta, D.I. Yogyakarta 55233 &bull; Telepon: (0274) 513503<br>
                Laman: www.smkn3jogja.sch.id &bull; Pos-el: smkn3jogja@gmail.com
            </div>
        </div>
        <div class="kop-subline"></div>

        <!-- JUDUL LAPORAN -->
        <div class="judul-laporan">
            <h1>Laporan Riwayat Mutasi &amp; Audit Sirkulasi Stok Bengkel</h1>
            <p>Sistem Informasi Inventaris Bengkel (SIBENKA) &bull; Format Kertas F4 (Folio) Landscape</p>
        </div>

        <!-- INFORMASI DOKUMEN & PARAMETER FILTER -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">Unit Bengkel / Lab</td>
                <td class="meta-colon">:</td>
                <td class="meta-value"><strong>{{ $bengkel->nama ?? 'Semua Bengkel' }}</strong> ({{ $bengkel->kode ?? '-' }})</td>
                
                <td class="meta-label">Waktu Cetak</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ date('d/m/Y H:i:s') }} WIB</td>
            </tr>
            <tr>
                <td class="meta-label">Periode Data</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $filters['period'] }}</td>

                <td class="meta-label">Petugas Toolman</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $user->name }} (ID: #{{ $user->id }})</td>
            </tr>
            <tr>
                <td class="meta-label">Filter Kategori</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $filters['jenis'] }}</td>

                <td class="meta-label">Filter Tipe Aset</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $filters['tipe'] }}</td>
            </tr>
            <tr>
                <td class="meta-label">Kata Kunci Pencarian</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $filters['search'] }}</td>

                <td class="meta-label">Status Arsip</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">Dokumen Sah Terfilter ({{ $totalRecords }} Baris)</td>
            </tr>
        </table>

        <!-- RINGKASAN REKAPITULASI DOKUMEN (HEMAT TINTA) -->
        <table class="kpi-table">
            <thead>
                <tr>
                    <th width="25%">Total Catatan Log</th>
                    <th width="25%">Total Stok Masuk (+)</th>
                    <th width="25%">Sirkulasi Keluar (-)</th>
                    <th width="25%">Rusak &amp; Hilang</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($totalRecords) }} Transaksi</td>
                    <td style="color: #15803d;">+{{ number_format($totalMasuk) }} Item</td>
                    <td style="color: #b91c1c;">-{{ number_format($totalKeluar) }} Item</td>
                    <td style="color: #b45309;">{{ number_format($totalMasalah) }} Item</td>
                </tr>
            </tbody>
        </table>

        <!-- TABEL RINCIAN TRANSAKSI MUTASI (PERSENTASE LEBAR PRESISI F4) -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 2.8%;">No</th>
                    <th style="width: 8.5%;">Tgl &amp; Waktu</th>
                    <th style="width: 6.2%;">ID Log</th>
                    <th style="width: 7.5%;">Kode</th>
                    <th style="width: 16%;">Nama Barang</th>
                    <th style="width: 5.5%;">Tipe</th>
                    <th style="width: 8%;">Lokasi Simpan</th>
                    <th style="width: 9%;">Jenis Mutasi</th>
                    <th style="width: 4%;">Qty</th>
                    <th style="width: 4%;">Satuan</th>
                    <th style="width: 6.5%;">Stok Akhir</th>
                    <th style="width: 8%;">Petugas</th>
                    <th style="width: 6.5%;">No. Ref</th>
                    <th style="width: 7.5%;">Keterangan Audit</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($movements as $m)
                    @php
                        $sign = match ($m->jenis) {
                            'stok_masuk', 'pengembalian_baik' => '+',
                            'peminjaman', 'bhp_keluar', 'barang_hilang' => '-',
                            default => '',
                        };

                        $jenisLabel = match ($m->jenis) {
                            'stok_masuk' => 'Stok Masuk',
                            'peminjaman' => 'Peminjaman',
                            'pengembalian_baik' => 'Kembali (Baik)',
                            'pengembalian_rusak' => 'Kembali (Rusak)',
                            'barang_hilang' => 'Barang Hilang',
                            'bhp_keluar' => 'BHP Keluar',
                            'perbaikan' => 'Perbaikan Alat',
                            'penyesuaian' => 'Penyesuaian',
                            default => ucfirst(str_replace('_', ' ', $m->jenis)),
                        };

                        $refTipe = strtolower($m->referensi_tipe ?? '');
                        $refText = match (true) {
                            str_contains($refTipe, 'peminjam') => 'Pinjam #' . str_pad($m->referensi_id, 4, '0', STR_PAD_LEFT),
                            str_contains($refTipe, 'pengadaan') => 'RAB #' . str_pad($m->referensi_id, 4, '0', STR_PAD_LEFT),
                            !empty($m->referensi_tipe) => ucfirst(rtrim($m->referensi_tipe, 's')) . " #{$m->referensi_id}",
                            default => '-',
                        };

                        $stokText = $m->barang
                            ? ($m->barang->stok_tersedia . ' / ' . $m->barang->stok_total)
                            : '-';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center" style="font-size: 6.8pt;">
                            <div>{{ $m->created_at->format('d/m/Y') }}</div>
                            <div style="color: #4b5563;">{{ $m->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="text-center font-mono" style="font-size: 6.8pt;">#{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-mono text-center" style="font-size: 6.8pt;">{{ $m->barang->kode_barang ?? '-' }}</td>
                        <td><strong>{{ $m->barang->nama ?? 'Barang Terhapus' }}</strong></td>
                        <td class="text-center">{{ ($m->barang && $m->barang->jenis_barang === 'bhp') ? 'BHP' : 'Inventaris' }}</td>
                        <td>{{ $m->barang?->lokasiPenyimpanan?->nama ?? 'Bengkel' }}</td>
                        <td>{{ $jenisLabel }}</td>
                        <td class="text-right" style="font-weight: bold;">
                            {{ $sign }}{{ $m->jumlah }}
                        </td>
                        <td class="text-center">{{ $m->barang->satuan ?? 'unit' }}</td>
                        <td class="text-center" style="font-size: 6.8pt;">{{ $stokText }}</td>
                        <td style="font-size: 6.8pt;">{{ $m->user->name ?? 'Sistem' }}</td>
                        <td class="text-center font-mono" style="font-size: 6.8pt;">{{ $refText }}</td>
                        <td style="font-size: 6.8pt; color: #1f2937;">{{ $m->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center" style="padding: 14px;">
                            Tidak ada data transaksi mutasi stok yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse

                <!-- BARIS REKAPITULASI TOTAL -->
                <tr class="total-row">
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td colspan="6">Akumulasi {{ number_format($totalRecords) }} Transaksi Terfilter</td>
                    <td class="text-right">
                        +{{ number_format($totalMasuk) }} / -{{ number_format($totalKeluar) }}
                    </td>
                    <td colspan="2" class="text-center">Kuantitas Fisik</td>
                    <td colspan="3">Rusak &amp; Hilang: {{ number_format($totalMasalah) }} Item</td>
                </tr>
            </tbody>
        </table>

        <!-- LEMBAR PENGESAHAN TANDA TANGAN (KOMPAK AGAR TIDAK LEWAT HALAMAN) -->
        <table class="ttd-table">
            <tr>
                <td width="50%" style="padding-left: 20px;">
                    <div>Mengetahui,</div>
                    <div style="font-weight: bold;">Kepala Program Keahlian / Kepala Bengkel</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">( ............................................................ )</div>
                    <div>NIP. .........................................................</div>
                </td>
                <td width="15%"></td>
                <td width="35%" style="padding-left: 20px;">
                    <div>Yogyakarta, {{ date('d F Y') }}</div>
                    <div style="font-weight: bold;">Petugas Pengelola Bengkel (Toolman)</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">( {{ $user->name }} )</div>
                    <div>NIP / ID Petugas: #{{ $user->id }}</div>
                </td>
            </tr>
        </table>

    </div>
    </div>

</body>
</html>
