<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Konsumsi Bahan Habis Pakai - SIBENKA SMKN 3 Yogyakarta</title>
    <style>
        body, table { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 10pt; color: #1e293b; }
        .main-title { font-size: 15pt; font-weight: bold; color: #1e3a8a; text-align: center; }
        .sub-title { font-size: 12pt; font-weight: bold; color: #047857; text-align: center; }
        .school-title { font-size: 9.5pt; color: #64748b; text-align: center; }
        .section-bar { font-size: 10pt; font-weight: bold; background-color: #065f46; color: #ffffff; padding: 6px 10px; }
        .meta-label { font-weight: bold; color: #334155; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px 8px; }
        .meta-val { color: #0f172a; border: 1px solid #cbd5e1; padding: 5px 8px; }
        
        /* KPI Cards */
        .kpi-title { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569; text-align: center; }
        .kpi-val { font-size: 13pt; font-weight: bold; text-align: center; }
        .kpi-card-log { background-color: #f8fafc; border: 1px solid #94a3b8; }
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
        .signature-box { border: 1px dashed #cbd5e1; padding: 12px; }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
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

            <!-- FOOTER TOTAL -->
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
</body>
</html>

