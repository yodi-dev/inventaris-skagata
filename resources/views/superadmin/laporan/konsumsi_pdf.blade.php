<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konsumsi BHP - {{ $selectedBengkel->nama ?? 'Semua Bengkel' }} - SIBENKA SMKN 3 Yogyakarta</title>
    <style>
        /* === RESET & PAGE SETUP === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Ukuran Kertas Cetak A4 Landscape */
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        html, body {
            min-width: 297mm;
        }

        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 8pt;
            color: #0f172a;
            background-color: #e2e8f0;
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

        /* Lembar Dokumen Fisik */
        .page-container {
            width: 297mm;
            min-height: 210mm;
            height: auto;
            margin: 16px auto 30px auto;
            background: #ffffff;
            padding: 10mm 12mm 12mm 12mm;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
            border: 1px solid #94a3b8;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* === TOOLBAR INTERAKTIF (TIDAK TERCETAK) === */
        .no-print-toolbar {
            width: 297mm;
            margin: 14px auto 0 auto;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            font-size: 11pt;
            z-index: 100;
        }

        .toolbar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            font-size: 11pt;
        }

        .toolbar-badge {
            background: #059669;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background: #059669;
            color: #ffffff;
            border: none;
            padding: 7px 16px;
            font-size: 9.5pt;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: #047857;
        }

        .btn-back {
            background: #334155;
            color: #e2e8f0;
            border: none;
            padding: 7px 14px;
            font-size: 9.5pt;
            font-weight: 500;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: #475569;
            color: #ffffff;
        }

        /* === KOP SURAT DINAS RESMI === */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .kop-logo {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-text {
            text-align: center;
            line-height: 1.15;
            vertical-align: middle;
        }
        .kop-instansi {
            font-size: 9pt;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.8px;
        }
        .kop-sekolah {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 1px;
            letter-spacing: 0.5px;
        }
        .kop-alamat {
            font-size: 7pt;
            color: #475569;
            margin-top: 2px;
        }
        .kop-divider {
            border-top: 2.5px solid #0f172a;
            border-bottom: 0.8px solid #0f172a;
            height: 3px;
            margin: 4px 0 10px 0;
        }

        /* === JUDUL LAPORAN === */
        .report-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 11pt;
            font-weight: 800;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-subtitle {
            font-size: 8pt;
            font-weight: 600;
            color: #475569;
            margin-top: 1px;
        }

        /* === METADATA DOKUMEN & KPI === */
        .meta-kpi-container {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
        }

        .meta-box {
            flex: 1.8;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            background: #f8fafc;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 12px;
            font-size: 7.5pt;
        }
        .meta-item {
            display: flex;
            gap: 4px;
        }
        .meta-label {
            font-weight: 600;
            color: #475569;
            min-width: 95px;
        }
        .meta-value {
            font-weight: bold;
            color: #0f172a;
        }

        .kpi-box {
            flex: 1.2;
            display: flex;
            gap: 6px;
        }
        .kpi-card {
            flex: 1;
            border-radius: 4px;
            padding: 5px 6px;
            text-align: center;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .kpi-num {
            font-size: 11pt;
            font-weight: 800;
            line-height: 1;
        }
        .kpi-desc {
            font-size: 6.5pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 2px;
        }
        .kpi-emerald { background: #ecfdf5; border-color: #10b981; color: #047857; }
        .kpi-amber { background: #fffbeb; border-color: #f59e0b; color: #b45309; }
        .kpi-slate { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }

        /* === TABEL DATA === */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #065f46;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            padding: 5px 5px;
            border: 1px solid #047857;
            font-size: 7.5pt;
        }
        .data-table td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: Consolas, monospace; font-size: 7pt; }
        .font-bold { font-weight: bold; }
        .qty-badge {
            color: #b45309;
            font-weight: 700;
        }

        .table-footer-total {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 1.5px solid #065f46;
        }

        /* === LEMBAR PENGESAHAN === */
        .signature-container {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .signature-block {
            width: 250px;
            text-align: center;
            font-size: 8pt;
            line-height: 1.35;
        }
        .sign-space {
            height: 52px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sign-nip {
            font-size: 7.5pt;
            color: #475569;
        }

        /* === PRINT MEDIA RULES === */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 7.5pt;
            }
            .preview-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .page-container {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: #ffffff !important;
            }
            .data-table th {
                background-color: #065f46 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .data-table tr:nth-child(even) {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="preview-wrapper">

        <!-- TOOLBAR PRATINJAU DOKUMEN (TIDAK TERCETAK) -->
        <div class="no-print-toolbar">
            <div class="toolbar-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Pratinjau Dokumen Cetak / PDF</span>
                <span class="toolbar-badge">A4 Landscape</span>
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('superadmin.laporan.konsumsi', request()->query()) }}" class="btn-back">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Laporan
                </a>
                <button type="button" onclick="window.print()" class="btn-print">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak / Simpan PDF (Ctrl + P)
                </button>
            </div>
        </div>

        <!-- LEMBAR DOKUMEN RESMI -->
        <div class="page-container">

            <!-- KOP SURAT RESMI -->
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        <img src="{{ asset('logo.png') }}" alt="Logo SMKN 3 Yogyakarta" style="width: 58px; height: auto; max-height: 65px; object-fit: contain; margin: 0 auto; display: block;">
                    </td>
                    <td class="kop-text">
                        <div class="kop-instansi">PEMERINTAH DAERAH ISTIMEWA YOGYAKARTA</div>
                        <div class="kop-instansi">DINAS PENDIDIKAN, PEMUDA, DAN OLAHRAGA</div>
                        <div class="kop-sekolah">SMK NEGERI 3 YOGYAKARTA</div>
                        <div class="kop-alamat">Jalan R.W. Monginsidi No. 2 Yogyakarta 55233 | Telepon (0274) 513507 | Laman: smkn3jogja.sch.id</div>
                    </td>
                </tr>
            </table>
            <div class="kop-divider"></div>

            <!-- JUDUL LAPORAN -->
            <div class="report-header">
                <div class="report-title">LAPORAN REKAPITULASI KONSUMSI BAHAN HABIS PAKAI (BHP)</div>
                <div class="report-subtitle">Sistem Informasi Inventaris Bengkel (SIBENKA) - Waka Bidang Sarana &amp; Prasarana</div>
            </div>

            <!-- METADATA DOKUMEN & KPI CARDS -->
            <div class="meta-kpi-container">
                <div class="meta-box">
                    <div class="meta-item">
                        <span class="meta-label">Unit Bengkel:</span>
                        <span class="meta-value">{{ $filters['bengkel'] }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Waktu Unduh:</span>
                        <span class="meta-value">{{ date('d/m/Y H:i') }} WIB</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Periode Waktu:</span>
                        <span class="meta-value">{{ $filters['periode'] }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Penanggung Jawab:</span>
                        <span class="meta-value">{{ $user->name ?? 'Waka Sarpras' }} (Superadmin)</span>
                    </div>
                    @if (!empty($filters['search']))
                    <div class="meta-item" style="grid-column: span 2;">
                        <span class="meta-label">Kata Kunci:</span>
                        <span class="meta-value">"{{ $filters['search'] }}"</span>
                    </div>
                    @endif
                </div>

                <div class="kpi-box">
                    <div class="kpi-card kpi-slate">
                        <div class="kpi-num">{{ number_format($totalRecords, 0, ',', '.') }}</div>
                        <div class="kpi-desc">Total Transaksi</div>
                    </div>
                    <div class="kpi-card kpi-amber">
                        <div class="kpi-num">{{ number_format($totalQuantity, 0, ',', '.') }}</div>
                        <div class="kpi-desc">Total Kuantitas Terpakai</div>
                    </div>
                    <div class="kpi-card kpi-emerald">
                        <div class="kpi-num">{{ number_format($totalBarangVarian, 0, ',', '.') }}</div>
                        <div class="kpi-desc">Varian BHP Digunakan</div>
                    </div>
                </div>
            </div>

            <!-- TABEL DATA RINCIAN -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="12%">Waktu Transaksi</th>
                        <th width="12%">Kode Barang</th>
                        <th width="20%">Nama Barang BHP</th>
                        <th width="15%">Unit Bengkel</th>
                        <th width="11%">Lokasi Simpan</th>
                        <th width="9%">Dipakai</th>
                        <th width="6%">Satuan</th>
                        <th width="11%">Petugas / Staf</th>
                        <th width="16%">Keterangan / Keperluan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $idx => $m)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="text-center font-mono">{{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-center font-mono">{{ $m->barang->kode_barang ?? '-' }}</td>
                            <td class="font-bold">{{ $m->barang->nama ?? 'Barang Dihapus' }}</td>
                            <td>{{ $m->barang->bengkel->nama ?? '-' }}</td>
                            <td>{{ $m->barang->lokasiPenyimpanan->nama_lokasi ?? '-' }}</td>
                            <td class="text-right qty-badge">-{{ abs($m->jumlah) }}</td>
                            <td class="text-center">{{ $m->barang->satuan ?? 'unit' }}</td>
                            <td>{{ $m->user->name ?? 'Sistem' }}</td>
                            <td>{{ $m->keterangan ?? 'Penggunaan bahan praktik' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center" style="padding: 18px; color: #64748b;">
                                <em>Tidak ada data riwayat konsumsi bahan habis pakai (BHP) pada periode dan filter ini.</em>
                            </td>
                        </tr>
                    @endforelse

                    @if ($movements->isNotEmpty())
                        <tr class="table-footer-total">
                            <td colspan="6" class="text-right font-bold" style="padding-right: 10px;">TOTAL AKUMULASI KUANTITAS TERPAKAI:</td>
                            <td class="text-right qty-badge" style="font-size: 8.5pt;">{{ number_format($totalQuantity, 0, ',', '.') }}</td>
                            <td colspan="3" style="color: #475569; font-style: italic;">Unit / Satuan Terdistribusi</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- LEMBAR PENGESAHAN -->
            <div class="signature-container">
                <div class="signature-block">
                    <div>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                    <div style="font-weight: bold; margin-top: 2px;">Wakil Kepala Sekolah Bidang Sarpras</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $user->name ?? 'Waka Sarpras SMKN 3 Yogyakarta' }}</div>
                    <div class="sign-nip">NIP. {{ $user->nomor_identitas ?? '19750812 200003 1 002' }}</div>
                </div>
            </div>

        </div>

    </div>

    <script>
        // Auto-print jika terdapat parameter ?print=1 di URL
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                window.print();
            }
        });
    </script>
</body>
</html>

