<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon Pinjam Alat & Bahan #TRX-{{ str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }} -
        {{ $bengkel->nama ?? 'Bengkel' }} - SMKN 3 Yogyakarta</title>
    <style>
        /* === RESET & PAGE SETUP === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Ukuran Kertas Cetak Resmi A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 12mm 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif, Arial, sans-serif;
            font-size: 11pt;
            color: #000000;
            background-color: #f1f5f9;
            line-height: 1.4;
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
            width: 210mm;
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
            font-family: Arial, sans-serif;
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

        /* Lembar Fisik Dokumen Cetak */
        .sheet-container {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 15mm 18mm 18mm 18mm;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
            border: 1px solid #cbd5e1;
            box-sizing: border-box;
            position: relative;
        }

        /* KOP SURAT RESMI */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .kop-logo {
            width: 80px;
            vertical-align: middle;
            text-align: center;
            padding-right: 12px;
        }

        .kop-logo img {
            width: 70px;
            height: auto;
            max-height: 75px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .kop-text {
            vertical-align: middle;
            text-align: center;
        }

        .kop-instansi-1 {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
        }

        .kop-instansi-2 {
            font-size: 11.5pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
        }

        .kop-sekolah {
            font-size: 15pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 1px;
            margin: 2px 0;
        }

        .kop-bengkel {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-alamat {
            font-size: 8.5pt;
            color: #222222;
            margin-top: 3px;
            font-family: Arial, sans-serif;
        }

        /* Garis Pemisah Kop Ganda */
        .kop-divider {
            border-top: 2.5px solid #000000;
            border-bottom: 0.8px solid #000000;
            height: 3px;
            margin: 5px 0 16px 0;
        }

        /* JUDUL DOKUMEN */
        .doc-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-number {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 2px;
            font-family: Arial, sans-serif;
        }

        /* FORM IDENTITAS */
        .section-intro {
            font-size: 11pt;
            margin-bottom: 10px;
        }

        .identitas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .identitas-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10.5pt;
        }

        .identitas-label {
            width: 175px;
            font-weight: normal;
        }

        .identitas-separator {
            width: 18px;
            text-align: center;
        }

        .identitas-value {
            font-weight: 600;
        }

        /* TABEL BARANG / ALAT */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 16px 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 10pt;
        }

        .items-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5pt;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }

        /* PERNYATAAN KESANGGUPAN */
        .statement-box {
            margin: 16px 0 24px 0;
            font-size: 11pt;
            line-height: 1.5;
            text-align: justify;
        }

        /* TANDA TANGAN */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 10.5pt;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-title {
            font-size: 10pt;
            margin-top: 2px;
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

            .items-table {
                page-break-inside: auto;
            }

            .items-table thead {
                display: table-header-group;
            }

            .items-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .items-table th {
                background-color: #e5e7eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .signature-table,
            .statement-box,
            .kop-table,
            .identitas-table {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>

<body>

    <div class="preview-wrapper">

        <!-- TOOLBAR PRATINJAU DOKUMEN (TIDAK TERCETAK) -->
        <div class="no-print-toolbar">
            <div class="toolbar-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Pratinjau Bon Pinjam Alat / Bahan Resmi</span>
                <span class="toolbar-badge">A4 Portrait</span>
            </div>
            <div class="toolbar-actions">
                <a href="{{ auth()->check() && auth()->user()->role === 'peminjam' ? route('peminjam.tiket.show', $peminjaman->id) : route('toolman.pengembalian.index') }}"
                    class="btn-toolbar btn-back">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
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

        <!-- LEMBAR FISIK DOKUMEN CETAK -->
        <div class="sheet-container">

            <!-- KOP SURAT DINAS RESMI -->
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
                        <div class="kop-alamat">Jalan R.W. Monginsidi No. 2 Yogyakarta 55233 &bull; Telepon: (0274)
                            513507 &bull; Laman: smkn3jogja.sch.id</div>
                    </td>
                </tr>
            </table>

            <div class="kop-divider"></div>

            <!-- JUDUL DOKUMEN & NOMOR -->
            <div class="doc-header">
                <div class="doc-title">BON PINJAM ALAT / BAHAN</div>
                <div class="doc-number">No. : #TRX-{{ str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>

            <!-- PERNYATAAN & IDENTITAS PEMINJAM -->
            <div class="section-intro">Yang bertanda tangan di bawah ini saya :</div>

            <table class="identitas-table">
                <tr>
                    <td class="identitas-label">Nama Peminjam</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">{{ strtoupper($peminjaman->user->name ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="identitas-label">Profesi / Status</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">
                        @if ($peminjaman->user && $peminjaman->user->isGuru())
                            Guru / Tenaga Pendidik
                        @else
                            Siswa
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="identitas-label">No. Identitas (NIS/NIP)</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">{{ $peminjaman->user->nomor_identitas ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="identitas-label">No. Kontak / WhatsApp</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">{{ $peminjaman->user->nomor_wa ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="identitas-label">Tanggal Pinjam</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">
                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('l, d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="identitas-label">Batas Pengembalian</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">
                        @if ($peminjaman->batas_kembali)
                            {{ \Carbon\Carbon::parse($peminjaman->batas_kembali)->locale('id')->translatedFormat('l, d F Y, H:i') }}
                            WIB
                        @else
                            <span style="color: #047857; font-weight: normal; font-style: italic;">Barang Habis Pakai
                                (Tidak Perlu Dikembalikan)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="identitas-label">Keperluan</td>
                    <td class="identitas-separator">:</td>
                    <td class="identitas-value">{{ $peminjaman->keperluan ?? 'Praktikum Kejuruan' }}</td>
                </tr>
            </table>

            <!-- DAFTAR RINCIAN BARANG / ALAT / BAHAN -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">No.</th>
                        <th style="width: 105px;">Kode Barang</th>
                        <th>Nama Alat / Bahan</th>
                        <th style="width: 70px;">Jenis</th>
                        <th style="width: 80px;">Jumlah</th>
                        <th style="width: 120px;">Lokasi Simpan</th>
                        <th style="width: 95px;">Kondisi Awal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjaman->detailPeminjamans as $index => $detail)
                        @php
                            $isBhp = $detail->barang?->jenis_barang === 'bhp';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}.</td>
                            <td class="font-mono text-center font-bold">{{ $detail->barang?->kode_barang ?? '-' }}</td>
                            <td>
                                <strong>{{ $detail->barang?->nama ?? 'Barang Terhapus' }}</strong>
                                @if ($detail->barang && $detail->barang->spesifikasi)
                                    <div style="font-size: 8.5pt; color: #444444; font-family: Arial, sans-serif;">
                                        {{ $detail->barang->spesifikasi }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($isBhp)
                                    <span
                                        style="font-size: 8.5pt; font-weight: bold; color: #b45309; background: #fef3c7; padding: 2px 6px; border-radius: 3px; border: 1px solid #fde68a;">BHP</span>
                                @else
                                    <span
                                        style="font-size: 8.5pt; font-weight: bold; color: #047857; background: #ecfdf5; padding: 2px 6px; border-radius: 3px; border: 1px solid #a7f3d0;">Alat</span>
                                @endif
                            </td>
                            <td class="text-center font-bold">
                                {{ $detail->jumlah }} {{ $detail->barang?->satuan ?? 'Unit' }}
                            </td>
                            <td style="font-size: 9pt;">
                                {{ $detail->barang?->lokasiPenyimpanan?->nama ?? 'Gudang Utama' }}
                            </td>
                            <td class="text-center" style="font-size: 9pt;">
                                {{ $isBhp ? 'Siap Pakai' : 'Baik / Lengkap' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 15px;">Tidak ada rincian alat atau
                                bahan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- PERNYATAAN KESANGGUPAN -->
            <div class="statement-box">
                Meminjam alat dan/atau bahan seperti tersebut di atas untuk keperluan praktik, dan sanggup memenuhi
                seluruh tata tertib bengkel serta bertanggung jawab penuh atas keutuhan dan pengembalian alat tepat
                waktu.
            </div>

            <!-- TANDA TANGAN (SESUAI ATURAN RESMI) -->
            @php
                $toolmanName = null;
                $toolmanNip = '.........................................';

                if ($peminjaman->diprosesOleh) {
                    $toolmanName = $peminjaman->diprosesOleh->name;
                    $toolmanNip = $peminjaman->diprosesOleh->nomor_identitas ?? $toolmanNip;
                } elseif (auth()->check() && (auth()->user()->isToolman() || auth()->user()->isWaka())) {
                    $toolmanName = auth()->user()->name;
                    $toolmanNip = auth()->user()->nomor_identitas ?? $toolmanNip;
                }
            @endphp

            <table class="signature-table">
                <tr>
                    <td style="text-align: left; padding-left: 20px;">
                        <div>Mengetahui / Menyetujui:</div>
                        <div style="font-weight: bold; margin-top: 2px;">Kepala Laboratorium / Toolman,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">
                            {{ $toolmanName ? strtoupper($toolmanName) : '( ......................................... )' }}
                        </div>
                        <div class="signature-title">NIP. {{ $toolmanNip }}</div>
                    </td>
                    <td style="text-align: right; padding-right: 20px;">
                        <div>Yogyakarta,
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('d F Y') }}
                        </div>
                        <div style="font-weight: bold; margin-top: 2px;">Peminjam,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">{{ strtoupper($peminjaman->user->name ?? 'Peminjam') }}</div>
                        <div class="signature-title">NIS/NIP.
                            {{ $peminjaman->user->nomor_identitas ?? '.........................................' }}
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>
