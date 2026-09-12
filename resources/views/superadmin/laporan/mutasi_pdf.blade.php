<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Aset - {{ $selectedBengkel->nama ?? 'Semua Bengkel' }} - SIBENKA SMKN 3 Yogyakarta</title>
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
            font-size: 7.5pt;
            color: #0f172a;
            background-color: #e2e8f0;
            line-height: 1.25;
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
            padding: 9mm 11mm 11mm 11mm;
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
            background: #2563eb;
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
            background: #2563eb;
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
            background: #1d4ed8;
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
            margin: 4px 0 8px 0;
        }

        /* === JUDUL LAPORAN === */
        .report-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .report-title {
            font-size: 11pt;
            font-weight: 800;
            color: #1e3a8a;
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
            gap: 10px;
            margin-bottom: 8px;
        }

        .meta-box {
            flex: 1.5;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 5px 8px;
            background: #f8fafc;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 8px;
            font-size: 7.2pt;
        }
        .meta-item {
            display: flex;
            gap: 4px;
        }
        .meta-label {
            font-weight: 600;
            color: #475569;
            min-width: 90px;
        }
        .meta-value {
            font-weight: bold;
            color: #0f172a;
        }

        .kpi-box {
            flex: 1.5;
            display: flex;
            gap: 5px;
        }
        .kpi-card {
            flex: 1;
            border-radius: 4px;
            padding: 4px 5px;
            text-align: center;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .kpi-num {
            font-size: 10pt;
            font-weight: 800;
            line-height: 1;
        }
        .kpi-desc {
            font-size: 6pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 2px;
        }
        .kpi-slate { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }
        .kpi-emerald { background: #ecfdf5; border-color: #10b981; color: #047857; }
        .kpi-rose { background: #fff1f2; border-color: #f43f5e; color: #be123c; }
        .kpi-amber { background: #fffbeb; border-color: #f59e0b; color: #b45309; }

        /* === TABEL DATA === */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
            margin-bottom: 10px;
        }
        .data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            padding: 5px 4px;
            border: 1px solid #172554;
            font-size: 7.2pt;
        }
        .data-table td {
            padding: 3.5px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: Consolas, monospace; font-size: 6.8pt; }
        .font-bold { font-weight: bold; }
        
        .badge-masuk { color: #15803d; font-weight: 700; }
        .badge-keluar { color: #b91c1c; font-weight: 700; }
        .badge-rusak { color: #b45309; font-weight: 700; }

        /* === LEMBAR PENGESAHAN === */
        .signature-container {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .signature-block {
            width: 240px;
            text-align: center;
            font-size: 7.5pt;
            line-height: 1.3;
        }
        .sign-space {
            height: 46px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sign-nip {
            font-size: 7pt;
            color: #475569;
        }

        /* === PRINT MEDIA RULES === */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 7.2pt;
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
                background-color: #1e3a8a !important;
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
                <span>Pratinjau Dokumen Cetak / PDF Laporan Mutasi Aset</span>
                <span class="toolbar-badge">A4 Landscape</span>
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('superadmin.laporan.mutasi', request()->query()) }}" class="btn-back">
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
                <div class="report-title">LAPORAN AUDIT &amp; REKAPITULASI MUTASI SIRKULASI STOK</div>
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
                        <span class="meta-label">Kategori Mutasi:</span>
                        <span class="meta-value">{{ $filters['jenis'] }}</span>
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
                        <div class="kpi-desc">Total Log Mutasi</div>
                    </div>
                    <div class="kpi-card kpi-emerald">
                        <div class="kpi-num">+{{ number_format($totalMasuk, 0, ',', '.') }}</div>
                        <div class="kpi-desc">Total Stok Masuk</div>
                    </div>
                    <div class="kpi-card kpi-rose">
                        <div class="kpi-num">-{{ number_format($totalKeluar, 0, ',', '.') }}</div>
                        <div class="kpi-desc">Sirkulasi Keluar</div>
                    </div>
                    <div class="kpi-card kpi-amber">
                        <div class="kpi-num">{{ number_format($totalMasalah, 0, ',', '.') }}</div>
                        <div class="kpi-desc">Rusak / Hilang</div>
                    </div>
                </div>
            </div>

            <!-- TABEL DATA RINCIAN -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="11%">Waktu Transaksi</th>
                        <th width="11%">Kode Barang</th>
                        <th width="18%">Nama Barang / Aset</th>
                        <th width="13%">Unit Bengkel</th>
                        <th width="11%">Lokasi Simpan</th>
                        <th width="13%">Kategori Mutasi</th>
                        <th width="7%">Perubahan</th>
                        <th width="5%">Satuan</th>
                        <th width="11%">Petugas / Staf</th>
                        <th width="16%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $jenisMap = [
                            'stok_masuk' => ['label' => 'Barang Masuk Baru', 'sign' => '+', 'class' => 'badge-masuk'],
                            'peminjaman' => ['label' => 'Peminjaman', 'sign' => '-', 'class' => 'badge-keluar'],
                            'pengembalian_baik' => ['label' => 'Kembali (Baik)', 'sign' => '+', 'class' => 'badge-masuk'],
                            'pengembalian_rusak' => ['label' => 'Kembali (Rusak)', 'sign' => '+', 'class' => 'badge-rusak'],
                            'barang_hilang' => ['label' => 'Barang Hilang', 'sign' => '-', 'class' => 'badge-keluar'],
                            'bhp_keluar' => ['label' => 'BHP Digunakan', 'sign' => '-', 'class' => 'badge-keluar'],
                            'perbaikan' => ['label' => 'Perbaikan/Servis', 'sign' => '0', 'class' => 'badge-rusak'],
                            'penyesuaian' => ['label' => 'Penyesuaian Stok', 'sign' => '', 'class' => ''],
                        ];
                    @endphp

                    @forelse ($movements as $idx => $m)
                        @php
                            $tipe = $jenisMap[$m->jenis] ?? [
                                'label' => ucwords(str_replace('_', ' ', $m->jenis)),
                                'sign' => '',
                                'class' => '',
                            ];
                            $sign = $tipe['sign'];
                            $qtyDisplay = ($sign ? $sign : '') . abs($m->jumlah);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="text-center font-mono">{{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-center font-mono">{{ $m->barang->kode_barang ?? '-' }}</td>
                            <td class="font-bold">{{ $m->barang->nama ?? 'Aset Dihapus' }}</td>
                            <td>{{ $m->barang->bengkel->nama ?? '-' }}</td>
                            <td>{{ $m->barang->lokasiPenyimpanan->nama_lokasi ?? '-' }}</td>
                            <td class="text-center">{{ $tipe['label'] }}</td>
                            <td class="text-right {{ $tipe['class'] }}">{{ $qtyDisplay }}</td>
                            <td class="text-center">{{ $m->barang->satuan ?? 'unit' }}</td>
                            <td>{{ $m->user->name ?? 'Sistem' }}</td>
                            <td>{{ $m->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 16px; color: #64748b;">
                                <em>Tidak ada data riwayat mutasi stok pada periode dan kriteria filter ini.</em>
                            </td>
                        </tr>
                    @endforelse
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

