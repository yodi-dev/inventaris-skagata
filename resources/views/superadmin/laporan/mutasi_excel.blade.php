<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Mutasi Sirkulasi Stok - SIBENKA SMKN 3 Yogyakarta</title>
    <style>
        body, table { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 10pt; color: #1e293b; }
        .main-title { font-size: 15pt; font-weight: bold; color: #1e3a8a; text-align: center; }
        .sub-title { font-size: 12pt; font-weight: bold; color: #2563eb; text-align: center; }
        .school-title { font-size: 9.5pt; color: #64748b; text-align: center; }
        .section-bar { font-size: 10pt; font-weight: bold; background-color: #0f172a; color: #ffffff; padding: 6px 10px; }
        .meta-label { font-weight: bold; color: #334155; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px 8px; }
        .meta-val { color: #0f172a; border: 1px solid #cbd5e1; padding: 5px 8px; }
        
        /* KPI Cards */
        .kpi-title { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569; text-align: center; }
        .kpi-val { font-size: 13pt; font-weight: bold; text-align: center; }
        .kpi-card-log { background-color: #f8fafc; border: 1px solid #94a3b8; color: #0f172a; }
        .kpi-card-masuk { background-color: #ecfdf5; border: 1px solid #10b981; color: #15803d; }
        .kpi-card-keluar { background-color: #fef2f2; border: 1px solid #ef4444; color: #b91c1c; }
        .kpi-card-rusak { background-color: #fffbeb; border: 1px solid #f59e0b; color: #b45309; }
        
        /* Data Table */
        .th-col { background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #1d4ed8; padding: 8px 6px; }
        .td-cell { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
        .td-center { text-align: center; }
        .td-right { text-align: right; }
        .row-alt { background-color: #f8fafc; }
        
        /* Badges */
        .badge-masuk { color: #15803d; font-weight: bold; }
        .badge-keluar { color: #b91c1c; font-weight: bold; }
        .badge-rusak { color: #b45309; font-weight: bold; }
        .footer-total { background-color: #e2e8f0; font-weight: bold; border-top: 2px solid #0f172a; border-bottom: 2px double #0f172a; }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- KOP LAPORAN RESMI -->
        <tr>
            <td colspan="11" class="main-title">SISTEM INVENTARIS BENGKEL (SIBENKA) - SMKN 3 YOGYAKARTA</td>
        </tr>
        <tr>
            <td colspan="11" class="sub-title">LAPORAN RESMI AUDIT &amp; RIWAYAT MUTASI SIRKULASI STOK</td>
        </tr>
        <tr>
            <td colspan="11" class="school-title">Manajemen Inventaris, Peredaran Alat &amp; Bahan Habis Pakai - Waka Sarpras (PRD v1.1)</td>
        </tr>
        <tr><td colspan="11">&nbsp;</td></tr>

        <!-- PARAMETER INFORMASI DOKUMEN -->
        <tr>
            <td colspan="11" class="section-bar">A. INFORMASI DOKUMEN &amp; PARAMETER FILTER</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Unit Bengkel / Kejuruan</td>
            <td colspan="4" class="meta-val">{{ $filters['bengkel'] }}</td>
            <td colspan="2" class="meta-label">Waktu Unduh / Cetak</td>
            <td colspan="3" class="meta-val">{{ date('d/m/Y H:i:s') }} WIB</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Periode Tanggal Mutasi</td>
            <td colspan="4" class="meta-val">{{ $filters['periode'] }}</td>
            <td colspan="2" class="meta-label">Diunduh Oleh (Akun)</td>
            <td colspan="3" class="meta-val">{{ $user->name ?? 'Waka Sarpras' }} (Superadmin)</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Kategori Mutasi</td>
            <td colspan="4" class="meta-val">{{ $filters['jenis'] }}</td>
            <td colspan="2" class="meta-label">Kata Kunci Pencarian</td>
            <td colspan="3" class="meta-val">{{ $filters['search'] ?? 'Semua Data' }}</td>
        </tr>
        <tr><td colspan="11">&nbsp;</td></tr>

        <!-- RINGKASAN METRIK KPI -->
        <tr>
            <td colspan="11" class="section-bar">B. RINGKASAN EKSEKUTIF METRIK MUTASI</td>
        </tr>
        <tr>
            <td colspan="3" class="kpi-card-log">
                <div class="kpi-title">TOTAL CATATAN LOG</div>
                <div class="kpi-val">{{ number_format($totalRecords, 0, ',', '.') }} Log</div>
            </td>
            <td colspan="3" class="kpi-card-masuk">
                <div class="kpi-title">TOTAL STOK MASUK</div>
                <div class="kpi-val">+{{ number_format($totalMasuk, 0, ',', '.') }} Item</div>
            </td>
            <td colspan="3" class="kpi-card-keluar">
                <div class="kpi-title">SIRKULASI KELUAR</div>
                <div class="kpi-val">-{{ number_format($totalKeluar, 0, ',', '.') }} Item</div>
            </td>
            <td colspan="2" class="kpi-card-rusak">
                <div class="kpi-title">RUSAK &amp; HILANG</div>
                <div class="kpi-val">{{ number_format($totalMasalah, 0, ',', '.') }} Item</div>
            </td>
        </tr>
        <tr><td colspan="11">&nbsp;</td></tr>

        <!-- TABEL DATA MUTASI -->
        <tr>
            <td colspan="11" class="section-bar">C. TABEL RINCIAN TRANSAKSI MUTASI STOK</td>
        </tr>
        <thead>
            <tr>
                <th class="th-col" width="4%">No</th>
                <th class="th-col" width="11%">Waktu Transaksi</th>
                <th class="th-col" width="11%">Kode Barang</th>
                <th class="th-col" width="18%">Nama Barang / Aset</th>
                <th class="th-col" width="13%">Unit Bengkel</th>
                <th class="th-col" width="11%">Lokasi Simpan</th>
                <th class="th-col" width="14%">Jenis Mutasi</th>
                <th class="th-col" width="8%">Perubahan</th>
                <th class="th-col" width="6%">Satuan</th>
                <th class="th-col" width="12%">Petugas / Staf</th>
                <th class="th-col" width="16%">Keterangan / Ref</th>
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
                <tr class="{{ $idx % 2 == 1 ? 'row-alt' : '' }}">
                    <td class="td-cell td-center">{{ $idx + 1 }}</td>
                    <td class="td-cell td-center">{{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="td-cell td-center font-mono">{{ $m->barang->kode_barang ?? '-' }}</td>
                    <td class="td-cell"><strong>{{ $m->barang->nama ?? 'Aset Dihapus' }}</strong></td>
                    <td class="td-cell">{{ $m->barang->bengkel->nama ?? '-' }}</td>
                    <td class="td-cell">{{ $m->barang->lokasiPenyimpanan->nama_lokasi ?? '-' }}</td>
                    <td class="td-cell td-center">{{ $tipe['label'] }}</td>
                    <td class="td-cell td-right {{ $tipe['class'] }}">{{ $qtyDisplay }}</td>
                    <td class="td-cell td-center">{{ $m->barang->satuan ?? 'unit' }}</td>
                    <td class="td-cell">{{ $m->user->name ?? 'Sistem' }}</td>
                    <td class="td-cell">{{ $m->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="td-cell td-center" style="padding: 20px; color: #64748b;">
                        <em>Tidak ada riwayat pergerakan / mutasi stok pada kriteria filter ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tr><td colspan="11">&nbsp;</td></tr>
        <tr><td colspan="11">&nbsp;</td></tr>

        <!-- LEMBAR PENGESAHAN DOKUMEN -->
        <tr>
            <td colspan="7"></td>
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

