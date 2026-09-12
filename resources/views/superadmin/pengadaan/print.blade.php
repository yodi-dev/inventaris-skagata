<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Lembar RAB #{{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }} - {{ $pengadaan->bengkel->nama ?? 'Bengkel' }} - SIBENKA SMKN 3 Yogyakarta</title>
    <style>
        /* === RESET & PAGE SETUP === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Ukuran Kertas Cetak A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        html, body {
            min-width: 210mm;
        }

        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 8.5pt;
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

        /* Lembar Dokumen Fisik A4 Portrait */
        .page-container {
            width: 210mm;
            min-height: 297mm;
            height: auto;
            margin: 16px auto 30px auto;
            background: #ffffff;
            padding: 14mm 16mm 16mm 16mm;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
            border: 1px solid #94a3b8;
            border-radius: 4px;
            box-sizing: border-box;
            position: relative;
        }

        /* === TOOLBAR INTERAKTIF (TIDAK TERCETAK) === */
        .no-print-toolbar {
            width: 210mm;
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
            font-family: monospace;
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
            line-height: 1.2;
            vertical-align: middle;
        }
        .kop-instansi {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.8px;
        }
        .kop-sekolah {
            font-size: 14pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 1px;
            letter-spacing: 0.5px;
        }
        .kop-alamat {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 3px;
        }
        .kop-divider {
            border-top: 2.5px solid #0f172a;
            border-bottom: 0.8px solid #0f172a;
            height: 3px;
            margin: 5px 0 12px 0;
        }

        /* === JUDUL DOKUMEN === */
        .document-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .document-title {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            text-decoration: underline;
        }
        .document-number {
            font-size: 8pt;
            font-weight: 600;
            color: #475569;
            margin-top: 3px;
            font-family: monospace;
        }

        /* === METADATA RAB GRID === */
        .meta-container {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 12px;
            background: #f8fafc;
            margin-bottom: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 16px;
            font-size: 8pt;
        }
        .meta-row {
            display: flex;
            gap: 6px;
        }
        .meta-label {
            font-weight: 600;
            color: #475569;
            min-width: 110px;
        }
        .meta-colon {
            color: #475569;
        }
        .meta-val {
            font-weight: bold;
            color: #0f172a;
        }

        /* Status Badge for print */
        .status-tag {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .status-revisi {
            background: #ffedd5;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* === TABEL RINCIAN BARANG === */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8pt;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            border: 1px solid #94a3b8;
            padding: 6px 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .items-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: top;
        }
        .items-table tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
            font-family: monospace;
        }
        .total-row td {
            background-color: #ecfdf5 !important;
            font-weight: bold;
            border-top: 2px solid #059669;
            color: #064e3b;
        }

        /* Terbilang Box */
        .terbilang-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 7.5pt;
            font-style: italic;
            margin-bottom: 12px;
            color: #334155;
        }

        /* Catatan & Disposisi */
        .notes-section {
            margin-bottom: 16px;
            font-size: 7.5pt;
        }
        .note-card {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 6px;
            background: #fafbfc;
        }
        .note-title {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 2px;
        }

        /* === BLOK TANDA TANGAN / PENGESAHAN === */
        .signature-section {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-date {
            text-align: right;
            font-size: 8pt;
            margin-bottom: 8px;
            color: #334155;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            text-align: center;
            font-size: 8pt;
        }
        .sig-role {
            font-weight: 600;
            color: #475569;
            line-height: 1.25;
            height: 32px;
        }
        .sig-space {
            height: 55px;
        }
        .sig-name {
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .sig-nip {
            font-size: 7pt;
            color: #64748b;
            margin-top: 1px;
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .preview-wrapper {
                padding: 0;
            }
            .page-container {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR INTERAKTIF -->
    <div class="no-print-toolbar">
        <div class="toolbar-title">
            <span>Pratinjau Lembar Usulan RAB</span>
            <span class="toolbar-badge">#RAB-{{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="toolbar-actions">
            <button type="button" onclick="window.print()" class="btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                <span>Cetak / Simpan PDF (Ctrl + P)</span>
            </button>
            <a href="{{ route('superadmin.pengadaan.show', $pengadaan->id) }}" class="btn-back">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Detail</span>
            </a>
        </div>
    </div>

    <!-- WRAPPER PRATINJAU DOKUMEN -->
    <div class="preview-wrapper">
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
                        <div class="kop-alamat">
                            Jl. R.W. Monginsidi No. 2, Cokrodiningratan, Kec. Jetis, Kota Yogyakarta, D.I. Yogyakarta 55233<br>
                            Telepon: (0274) 513503 | Faksimile: (0274) 557588 | Laman: https://smkn3jogja.sch.id
                        </div>
                    </td>
                </tr>
            </table>
            <div class="kop-divider"></div>

            <!-- JUDUL DOKUMEN -->
            <div class="document-header">
                <div class="document-title">RENCANA ANGGARAN BIAYA (RAB) PENGADAAN BARANG</div>
                <div class="document-number">NOMOR: {{ str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) }}/RAB-SIBENKA/SMK3/{{ date('Y') }}</div>
            </div>

            <!-- METADATA RAB -->
            <div class="meta-container">
                <div class="meta-row">
                    <span class="meta-label">Judul Usulan RAB</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $pengadaan->judul }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Tanggal Usulan</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $pengadaan->diajukan_pada ? $pengadaan->diajukan_pada->translatedFormat('d F Y') : $pengadaan->created_at->translatedFormat('d F Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Bengkel / Unit</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $pengadaan->bengkel->nama ?? '-' }} ({{ $pengadaan->bengkel->kode ?? '-' }})</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Status Usulan</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">
                        <span class="status-tag status-{{ $pengadaan->status }}">
                            {{ ucfirst($pengadaan->status) }}
                        </span>
                    </span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Toolman Pengusul</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $pengadaan->dibuatOleh->name ?? '-' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Peninjau (Waka)</span>
                    <span class="meta-colon">:</span>
                    <span class="meta-val">{{ $pengadaan->direviewOleh->name ?? 'Belum Ditinjau' }}</span>
                </div>
            </div>

            <!-- TABEL RINCIAN ITEM -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 25px; text-align: center;">No</th>
                        <th>Nama Barang &amp; Spesifikasi Teknis</th>
                        <th style="width: 55px; text-align: center;">Qty</th>
                        <th style="width: 50px; text-align: center;">Satuan</th>
                        <th style="width: 100px; text-align: right;">Harga Satuan</th>
                        <th style="width: 110px; text-align: right;">Subtotal Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengadaan->detailPengadaans as $index => $item)
                        @php
                            $subtotal = $item->jumlah * $item->harga_satuan;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->nama_barang }}</strong>
                                @if ($item->spesifikasi)
                                    <br><span style="color: #475569; font-size: 7.5pt;">Spesifikasi: {{ $item->spesifikasi }}</span>
                                @endif
                                @if ($item->barang)
                                    <br><span style="color: #059669; font-size: 7pt; font-family: monospace;">[Katalog: {{ $item->barang->kode_barang }}]</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td class="text-center">{{ $item->satuan }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 14px; color: #94a3b8;">
                                Tidak ada rincian barang yang dicantumkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; text-transform: uppercase; font-size: 7.5pt;">
                            Total Estimasi Anggaran Pengadaan:
                        </td>
                        <td class="text-center">{{ $totalItems }}</td>
                        <td class="text-center">Unit</td>
                        <td colspan="2" class="text-right" style="font-size: 9pt;">
                            Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- TERBILANG -->
            <div class="terbilang-box">
                <strong>Terbilang:</strong> {{ $terbilang }}
            </div>

            <!-- JUSTIFIKASI & CATATAN -->
            <div class="notes-section">
                @if ($pengadaan->catatan)
                    <div class="note-card">
                        <div class="note-title">Catatan / Justifikasi Pemohon (Toolman):</div>
                        <div>{{ $pengadaan->catatan }}</div>
                    </div>
                @endif

                @if ($pengadaan->catatan_review)
                    <div class="note-card" style="background: #f0fdf4; border-color: #bbf7d0;">
                        <div class="note-title" style="color: #166534;">Catatan / Disposisi Waka Sarana &amp; Prasarana:</div>
                        <div style="color: #14532d;">{{ $pengadaan->catatan_review }}</div>
                    </div>
                @endif
            </div>

            <!-- BLOK LEMBAR PENGESAHAN (TANDA TANGAN) -->
            <div class="signature-section">
                <div class="signature-date">
                    Yogyakarta, {{ $pengadaan->direview_pada ? $pengadaan->direview_pada->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                </div>
                <div class="signature-grid">
                    <!-- Kolom 1: Pengusul -->
                    <div>
                        <div class="sig-role">Pengusul / Toolman Bengkel,</div>
                        <div class="sig-space"></div>
                        <div class="sig-name">{{ $pengadaan->dibuatOleh->name ?? 'Toolman Bengkel' }}</div>
                        <div class="sig-nip">NIP/NUPTK: {{ $pengadaan->dibuatOleh->nomor_identitas ?? '-' }}</div>
                    </div>

                    <!-- Kolom 2: Mengetahui -->
                    <div>
                        <div class="sig-role">Mengetahui,<br>Kepala Program Keahlian,</div>
                        <div class="sig-space"></div>
                        <div class="sig-name">( ................................................. )</div>
                        <div class="sig-nip">NIP: ....................................................</div>
                    </div>

                    <!-- Kolom 3: Menyetujui -->
                    <div>
                        <div class="sig-role">Menyetujui,<br>Waka Bidang Sarana &amp; Prasarana,</div>
                        <div class="sig-space"></div>
                        <div class="sig-name">{{ $pengadaan->direviewOleh->name ?? 'Waka Sarpras SMKN 3 Yogyakarta' }}</div>
                        <div class="sig-nip">NIP: {{ $pengadaan->direviewOleh->nomor_identitas ?? '19750512 200212 1 004' }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

