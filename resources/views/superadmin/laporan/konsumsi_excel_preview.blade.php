<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Spreadsheet Excel - Laporan Konsumsi BHP - SIBENKA SMKN 3 Yogyakarta</title>
    <style>
        /* === RESET & PAGE SETUP === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            background-color: #0f172a;
            line-height: 1.4;
        }

        /* === TOOLBAR PRATINJAU EXCEL === */
        .excel-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #064e3b;
            border-bottom: 2px solid #047857;
            color: #ffffff;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .excel-icon-box {
            width: 34px;
            height: 34px;
            background: #059669;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13pt;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .toolbar-meta h1 {
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .toolbar-meta p {
            font-size: 8pt;
            color: #a7f3d0;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-back {
            background: #065f46;
            color: #ecfdf5;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 9pt;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #059669;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: #047857;
            color: #ffffff;
        }

        .btn-download {
            background: #10b981;
            color: #ffffff;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 9.5pt;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
            transition: all 0.2s;
        }
        .btn-download:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5);
        }

        /* === WORKBOOK WRAPPER === */
        .workbook-container {
            max-width: 1400px;
            margin: 20px auto 40px auto;
            padding: 0 20px;
        }

        .sheet-tab-bar {
            background: #1e293b;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 8px 16px 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #334155;
            border-bottom: none;
        }

        .tab-active {
            background: #ffffff;
            color: #0f172a;
            padding: 7px 20px;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            font-weight: 700;
            font-size: 8.5pt;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #cbd5e1;
            border-bottom: none;
        }

        .tab-tag {
            background: #059669;
            color: #ffffff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
        }

        .tab-info {
            color: #94a3b8;
            font-size: 8pt;
        }

        /* === SHEET CANVAS === */
        .sheet-canvas {
            background: #ffffff;
            border: 1px solid #334155;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            padding: 28px 32px;
            overflow-x: auto;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
        }

        /* Spreadsheet Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
        }

        .main-title { font-size: 15pt; font-weight: bold; color: #1e3a8a; text-align: center; }
        .sub-title { font-size: 12pt; font-weight: bold; color: #047857; text-align: center; }
        .school-title { font-size: 9.5pt; color: #64748b; text-align: center; }
        .section-bar { font-size: 10pt; font-weight: bold; background-color: #065f46; color: #ffffff; padding: 6px 10px; }
        .meta-label { font-weight: bold; color: #334155; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 10px; }
        .meta-val { color: #0f172a; border: 1px solid #cbd5e1; padding: 6px 10px; }
        
        /* KPI Cards */
        .kpi-title { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569; text-align: center; }
        .kpi-val { font-size: 13pt; font-weight: bold; text-align: center; }
        .kpi-card-log { background-color: #f8fafc; border: 1px solid #94a3b8; color: #0f172a; }
        .kpi-card-qty { background-color: #fef3c7; border: 1px solid #f59e0b; color: #b45309; }
        .kpi-card-var { background-color: #ecfdf5; border: 1px solid #10b981; color: #047857; }
        
        /* Data Table */
        .th-col { background-color: #047857; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #065f46; padding: 8px 6px; }
        .td-cell { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
        .td-center { text-align: center; }
        .td-right { text-align: right; }
        .row-alt { background-color: #f8fafc; }
        
        /* Badges & Footers */
        .qty-badge { font-weight: bold; color: #b45309; }
        .footer-total { background-color: #e2e8f0; font-weight: bold; border-top: 2px solid #065f46; border-bottom: 2px double #065f46; }
    </style>
</head>
<body>

    <!-- TOOLBAR PRATINJAU EXCEL -->
    <div class="excel-toolbar">
        <div class="toolbar-left">
            <div class="excel-icon-box">X</div>
            <div class="toolbar-meta">
                <h1>Pratinjau Spreadsheet Excel: Laporan Konsumsi BHP</h1>
                <p>Format Microsoft Excel / XML Spreadsheet (.xls) &bull; SMKN 3 Yogyakarta</p>
            </div>
        </div>
        <div class="toolbar-right">
            <a href="{{ route('superadmin.laporan.konsumsi', request()->query()) }}" class="btn-back">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Laporan
            </a>
            <a href="{{ route('superadmin.laporan.konsumsi.excel', array_merge(request()->query(), ['download' => 1])) }}" class="btn-download">
                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Unduh File Excel (.xls)
            </a>
        </div>
    </div>

    <!-- WORKBOOK WRAPPER -->
    <div class="workbook-container">
        <!-- SHEET TAB BAR -->
        <div class="sheet-tab-bar">
            <div class="tab-active">
                <span class="tab-tag">SHEET 1</span>
                <span>Konsumsi_BHP</span>
            </div>
            <div class="tab-info">
                <span>Total: <strong>{{ number_format($totalRecords) }}</strong> Baris Data</span>
            </div>
        </div>

        <!-- CANVAS DOKUMEN EXCEL -->
        <div class="sheet-canvas">
            <table>
                <!-- KOP LAPORAN RESMI -->
                <tr>
                    <td colspan="10" class="main-title">SISTEM INVENTARIS BENGKEL (SIBENKA) - SMKN 3 YOGYAKARTA</td>
                </tr>
                <tr>
                    <td colspan="10" class="sub-title">LAPORAN REKAPITULASI KONSUMSI BAHAN HABIS PAKAI (BHP)</td>
                </tr>
                <tr>
                    <td colspan="10" class="school-title">Pengawasan Pemakaian Bahan Praktik Bengkel Kejuruan - Waka Sarpras (PRD v1.1)</td>
                </tr>
                <tr><td colspan="10">&nbsp;</td></tr>

                <!-- PARAMETER INFORMASI DOKUMEN -->
                <tr>
                    <td colspan="10" class="section-bar">A. INFORMASI DOKUMEN &amp; PARAMETER FILTER</td>
                </tr>
                <tr>
                    <td colspan="2" class="meta-label">Unit Bengkel / Kejuruan</td>
                    <td colspan="3" class="meta-val">{{ $filters['bengkel'] }}</td>
                    <td colspan="2" class="meta-label">Waktu Cetak / Unduh</td>
                    <td colspan="3" class="meta-val">{{ date('d/m/Y H:i:s') }} WIB</td>
                </tr>
                <tr>
                    <td colspan="2" class="meta-label">Periode Tanggal Laporan</td>
                    <td colspan="3" class="meta-val">{{ $filters['periode'] }}</td>
                    <td colspan="2" class="meta-label">Diunduh Oleh (Akun)</td>
                    <td colspan="3" class="meta-val">{{ $user->name ?? 'Waka Sarpras' }} (Superadmin)</td>
                </tr>
                @if (!empty($filters['search']))
                <tr>
                    <td colspan="2" class="meta-label">Kata Kunci Pencarian</td>
                    <td colspan="8" class="meta-val">"{{ $filters['search'] }}"</td>
                </tr>
                @endif
                <tr><td colspan="10">&nbsp;</td></tr>

                <!-- RINGKASAN METRIK KPI -->
                <tr>
                    <td colspan="10" class="section-bar">B. RINGKASAN METRIK KONSUMSI BAHAN</td>
                </tr>
                <tr>
                    <td colspan="3" class="kpi-card-log">
                        <div class="kpi-title">TOTAL TRANSAKSI PEMAKAIAN</div>
                        <div class="kpi-val">{{ number_format($totalRecords, 0, ',', '.') }} Transaksi</div>
                    </td>
                    <td colspan="4" class="kpi-card-qty">
                        <div class="kpi-title">TOTAL KUANTITAS TERPAKAI</div>
                        <div class="kpi-val">{{ number_format($totalQuantity, 0, ',', '.') }} Satuan Fisik</div>
                    </td>
                    <td colspan="3" class="kpi-card-var">
                        <div class="kpi-title">VARIASI JENIS BARANG BHP</div>
                        <div class="kpi-val">{{ number_format($totalBarangVarian, 0, ',', '.') }} Varian Barang</div>
                    </td>
                </tr>
                <tr><td colspan="10">&nbsp;</td></tr>

                <!-- TABEL DATA KONSUMSI -->
                <tr>
                    <td colspan="10" class="section-bar">C. DATA RINCIAN TRANSAKSI PENGELUARAN BHP</td>
                </tr>
                <thead>
                    <tr>
                        <th class="th-col" width="5%">No</th>
                        <th class="th-col" width="12%">Waktu Transaksi</th>
                        <th class="th-col" width="12%">Kode Barang</th>
                        <th class="th-col" width="20%">Nama Barang BHP</th>
                        <th class="th-col" width="15%">Unit Bengkel</th>
                        <th class="th-col" width="12%">Lokasi Simpan</th>
                        <th class="th-col" width="10%">Jumlah Dipakai</th>
                        <th class="th-col" width="8%">Satuan</th>
                        <th class="th-col" width="14%">Petugas / Staf</th>
                        <th class="th-col" width="20%">Keterangan / Keperluan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $idx => $m)
                        <tr class="{{ $idx % 2 == 1 ? 'row-alt' : '' }}">
                            <td class="td-cell td-center">{{ $idx + 1 }}</td>
                            <td class="td-cell td-center">{{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="td-cell td-center font-mono">{{ $m->barang->kode_barang ?? '-' }}</td>
                            <td class="td-cell"><strong>{{ $m->barang->nama ?? 'Barang Dihapus' }}</strong></td>
                            <td class="td-cell">{{ $m->barang->bengkel->nama ?? '-' }}</td>
                            <td class="td-cell">{{ $m->barang->lokasiPenyimpanan->nama_lokasi ?? '-' }}</td>
                            <td class="td-cell td-right qty-badge">-{{ abs($m->jumlah) }}</td>
                            <td class="td-cell td-center">{{ $m->barang->satuan ?? 'unit' }}</td>
                            <td class="td-cell">{{ $m->user->name ?? 'Sistem' }}</td>
                            <td class="td-cell">{{ $m->keterangan ?? 'Penggunaan bahan praktik' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="td-cell td-center" style="padding: 20px; color: #64748b;">
                                <em>Tidak ada riwayat pengeluaran atau konsumsi bahan habis pakai (BHP) pada kriteria filter ini.</em>
                            </td>
                        </tr>
                    @endforelse

                    @if ($movements->isNotEmpty())
                        <tr class="footer-total">
                            <td colspan="6" class="td-cell td-right">TOTAL AKUMULASI KUANTITAS BAHAN TERPAKAI:</td>
                            <td class="td-cell td-right qty-badge">{{ number_format($totalQuantity, 0, ',', '.') }}</td>
                            <td colspan="3" class="td-cell">Unit/Satuan Terdistribusi</td>
                        </tr>
                    @endif
                </tbody>
                <tr><td colspan="10">&nbsp;</td></tr>
                <tr><td colspan="10">&nbsp;</td></tr>

                <!-- LEMBAR PENGESAHAN DOKUMEN -->
                <tr>
                    <td colspan="6"></td>
                    <td colspan="4" class="td-center">
                        Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                        <strong>Mengetahui / Mengesahkan,</strong><br>
                        Wakil Kepala Sekolah Bidang Sarpras<br><br><br><br>
                        <strong><u>{{ $user->name ?? 'Waka Sarpras SMKN 3 Yogyakarta' }}</u></strong><br>
                        NIP. {{ $user->nomor_identitas ?? '19750812 200003 1 002' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>

