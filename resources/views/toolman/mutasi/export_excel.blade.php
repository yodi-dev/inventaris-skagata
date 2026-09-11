<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
        .kpi-card-log { background-color: #f8fafc; border: 1px solid #94a3b8; }
        .kpi-card-masuk { background-color: #ecfdf5; border: 1px solid #10b981; }
        .kpi-card-keluar { background-color: #fef2f2; border: 1px solid #ef4444; }
        .kpi-card-rusak { background-color: #fffbeb; border: 1px solid #f59e0b; }
        
        /* Data Table */
        .th-col { background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #1d4ed8; padding: 8px 6px; }
        .td-cell { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
        .td-center { text-align: center; }
        .td-right { text-align: right; }
        .row-alt { background-color: #f8fafc; }
        
        /* Badges */
        .badge-masuk { background-color: #dcfce7; color: #15803d; font-weight: bold; }
        .badge-keluar { background-color: #fee2e2; color: #b91c1c; font-weight: bold; }
        .badge-pinjam { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
        .badge-bhp { background-color: #fef3c7; color: #b45309; font-weight: bold; }
        .badge-rusak { background-color: #fee2e2; color: #991b1b; font-weight: bold; }
        
        .footer-total { background-color: #e2e8f0; font-weight: bold; border-top: 2px solid #0f172a; border-bottom: 2px double #0f172a; }
        .signature-box { border: 1px dashed #cbd5e1; padding: 12px; }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- KOP LAPORAN -->
        <tr>
            <td colspan="15" class="main-title">SISTEM INVENTARIS BENGKEL (SIBENKA) - SMKN 3 YOGYAKARTA</td>
        </tr>
        <tr>
            <td colspan="15" class="sub-title">LAPORAN RESMI AUDIT &amp; RIWAYAT MUTASI SIRKULASI STOK</td>
        </tr>
        <tr>
            <td colspan="15" class="school-title">Manajemen Inventaris, Peredaran Alat &amp; Bahan Habis Pakai (PRD v1.1)</td>
        </tr>
        <tr><td colspan="15">&nbsp;</td></tr>

        <!-- PARAMETER INFORMASI DOKUMEN -->
        <tr>
            <td colspan="15" class="section-bar">A. INFORMASI DOKUMEN &amp; PARAMETER FILTER</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Nama Bengkel / Laboratorium</td>
            <td colspan="5" class="meta-val">{{ $bengkel->nama ?? 'Semua Bengkel' }}</td>
            <td colspan="2" class="meta-label">Waktu Cetak / Unduh</td>
            <td colspan="6" class="meta-val">{{ date('d/m/Y H:i:s') }} WIB</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Kode Bengkel</td>
            <td colspan="5" class="meta-val">{{ $bengkel->kode ?? '-' }}</td>
            <td colspan="2" class="meta-label">Petugas Operator / Toolman</td>
            <td colspan="6" class="meta-val">{{ $user->name }} ({{ ucfirst($user->role) }})</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Periode Data</td>
            <td colspan="5" class="meta-val">{{ $filters['period'] }}</td>
            <td colspan="2" class="meta-label">Filter Kategori Mutasi</td>
            <td colspan="6" class="meta-val">{{ $filters['jenis'] }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Filter Tipe Aset</td>
            <td colspan="5" class="meta-val">{{ $filters['tipe'] }}</td>
            <td colspan="2" class="meta-label">Kata Kunci Pencarian</td>
            <td colspan="6" class="meta-val">{{ $filters['search'] }}</td>
        </tr>
        <tr><td colspan="15">&nbsp;</td></tr>

        <!-- RINGKASAN EKSEKUTIF METRIK MUTASI -->
        <tr>
            <td colspan="15" class="section-bar">B. RINGKASAN EKSEKUTIF AUDIT (DATA TERFILTER)</td>
        </tr>
        <tr>
            <td colspan="3" class="kpi-card-log">
                <div class="kpi-title">Total Catatan Log</div>
                <div class="kpi-val" style="color: #0f172a;">{{ number_format($totalRecords) }} Log</div>
            </td>
            <td colspan="4" class="kpi-card-masuk">
                <div class="kpi-title">Total Stok Masuk</div>
                <div class="kpi-val" style="color: #15803d;">+{{ number_format($totalMasuk) }} Item</div>
            </td>
            <td colspan="4" class="kpi-card-keluar">
                <div class="kpi-title">Sirkulasi Keluar (Pinjam &amp; BHP)</div>
                <div class="kpi-val" style="color: #b91c1c;">-{{ number_format($totalKeluar) }} Item</div>
            </td>
            <td colspan="4" class="kpi-card-rusak">
                <div class="kpi-title">Total Rusak &amp; Hilang</div>
                <div class="kpi-val" style="color: #b45309;">{{ number_format($totalMasalah) }} Item</div>
            </td>
        </tr>
        <tr><td colspan="15">&nbsp;</td></tr>

        <!-- TABEL DATA AUDIT -->
        <tr>
            <td colspan="15" class="section-bar">C. TABEL RINCIAN AUDIT MUTASI STOK</td>
        </tr>
        <tr>
            <th class="th-col" width="40">No</th>
            <th class="th-col" width="110">ID Audit Log</th>
            <th class="th-col" width="90">Tanggal</th>
            <th class="th-col" width="80">Waktu</th>
            <th class="th-col" width="110">Kode Barang</th>
            <th class="th-col" width="220">Nama Barang</th>
            <th class="th-col" width="120">Tipe Aset</th>
            <th class="th-col" width="130">Lokasi Simpan</th>
            <th class="th-col" width="180">Jenis Mutasi</th>
            <th class="th-col" width="90">Perubahan</th>
            <th class="th-col" width="70">Satuan</th>
            <th class="th-col" width="130">Stok Saat Ini</th>
            <th class="th-col" width="160">Petugas Pemroses</th>
            <th class="th-col" width="140">No. Referensi</th>
            <th class="th-col" width="260">Keterangan / Catatan</th>
        </tr>

        @php $no = 1; @endphp
        @forelse($movements as $m)
            @php
                $isAlt = $no % 2 === 0;
                $sign = match ($m->jenis) {
                    'stok_masuk', 'pengembalian_baik' => '+',
                    'peminjaman', 'bhp_keluar', 'barang_hilang' => '-',
                    default => '',
                };

                $jenisClass = match ($m->jenis) {
                    'stok_masuk', 'pengembalian_baik' => 'badge-masuk',
                    'peminjaman' => 'badge-pinjam',
                    'bhp_keluar' => 'badge-bhp',
                    'pengembalian_rusak', 'barang_hilang' => 'badge-rusak',
                    default => '',
                };

                $jenisLabel = match ($m->jenis) {
                    'stok_masuk' => 'Stok Masuk (Tambah)',
                    'peminjaman' => 'Peminjaman Aset',
                    'pengembalian_baik' => 'Kembali (Kondisi Baik)',
                    'pengembalian_rusak' => 'Kembali (Kondisi Rusak)',
                    'barang_hilang' => 'Barang Hilang (Penyusutan)',
                    'bhp_keluar' => 'BHP Keluar (Konsumsi)',
                    'perbaikan' => 'Perbaikan Alat',
                    'penyesuaian' => 'Penyesuaian (Opname)',
                    default => ucfirst(str_replace('_', ' ', $m->jenis)),
                };

                $refTipe = strtolower($m->referensi_tipe ?? '');
                $refText = match (true) {
                    str_contains($refTipe, 'peminjam') => 'Peminjaman #' . str_pad($m->referensi_id, 4, '0', STR_PAD_LEFT),
                    str_contains($refTipe, 'pengadaan') => 'Pengadaan RAB #' . str_pad($m->referensi_id, 4, '0', STR_PAD_LEFT),
                    !empty($m->referensi_tipe) => ucfirst(rtrim($m->referensi_tipe, 's')) . " #{$m->referensi_id}",
                    default => '-',
                };

                $stokText = $m->barang
                    ? ($m->barang->stok_tersedia . ' / ' . $m->barang->stok_total . ' ' . ($m->barang->satuan ?? 'unit'))
                    : '-';
            @endphp
            <tr class="{{ $isAlt ? 'row-alt' : '' }}">
                <td class="td-cell td-center">{{ $no++ }}</td>
                <td class="td-cell td-center font-bold" style="font-weight: bold; color: #2563eb;">#LOG-{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="td-cell td-center">{{ $m->created_at->format('d/m/Y') }}</td>
                <td class="td-cell td-center">{{ $m->created_at->format('H:i') }} WIB</td>
                <td class="td-cell td-center" style="font-family: monospace; font-weight: bold;">{{ $m->barang->kode_barang ?? '-' }}</td>
                <td class="td-cell font-bold" style="font-weight: 600;">{{ $m->barang->nama ?? 'Barang Terhapus' }}</td>
                <td class="td-cell td-center">{{ ($m->barang && $m->barang->jenis_barang === 'bhp') ? 'BHP' : 'Inventaris' }}</td>
                <td class="td-cell">{{ $m->barang?->lokasiPenyimpanan?->nama ?? 'Bengkel' }}</td>
                <td class="td-cell td-center {{ $jenisClass }}">{{ $jenisLabel }}</td>
                <td class="td-cell td-right" style="font-weight: bold; color: {{ $sign === '+' ? '#15803d' : ($sign === '-' ? '#b91c1c' : '#334155') }};">
                    {{ $sign }}{{ $m->jumlah }}
                </td>
                <td class="td-cell td-center">{{ $m->barang->satuan ?? 'unit' }}</td>
                <td class="td-cell td-center" style="font-size: 9pt;">{{ $stokText }}</td>
                <td class="td-cell">{{ $m->user->name ?? 'Sistem Otomatis' }}</td>
                <td class="td-cell td-center" style="font-size: 9pt;">{{ $refText }}</td>
                <td class="td-cell" style="font-size: 9pt; color: #475569;">{{ $m->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="15" class="td-cell td-center" style="padding: 20px; color: #64748b;">
                    Tidak ada catatan mutasi stok yang sesuai dengan kriteria filter.
                </td>
            </tr>
        @endforelse

        <!-- FOOTER TOTAL REKAPITULASI -->
        <tr>
            <td colspan="2" class="td-cell footer-total td-center">TOTAL REKAPITULASI</td>
            <td colspan="7" class="td-cell footer-total">Total {{ number_format($totalRecords) }} Transaksi Audit Log Terfilter</td>
            <td class="td-cell footer-total td-right" style="color: #1e3a8a;">
                +{{ number_format($totalMasuk) }} / -{{ number_format($totalKeluar) }}
            </td>
            <td colspan="2" class="td-cell footer-total td-center">Total Pergerakan Fisik</td>
            <td colspan="3" class="td-cell footer-total" style="color: #b45309;">
                Rusak &amp; Hilang: {{ number_format($totalMasalah) }} Item
            </td>
        </tr>
        <tr><td colspan="15">&nbsp;</td></tr>

        <!-- LEMBAR PENGESAHAN DOKUMEN -->
        <tr>
            <td colspan="15" class="section-bar">D. LEMBAR PENGESAHAN DOKUMEN</td>
        </tr>
        <tr>
            <td colspan="6" class="td-cell signature-box">
                <div style="font-weight: bold; color: #334155;">Mengetahui,</div>
                <div style="font-size: 9pt; color: #64748b;">Kepala Program Keahlian / Kepala Bengkel</div>
                <br><br><br><br>
                <div style="font-weight: bold; text-decoration: underline;">( ............................................................ )</div>
                <div style="font-size: 9pt; color: #64748b;">NIP. .........................................................</div>
            </td>
            <td colspan="3">&nbsp;</td>
            <td colspan="6" class="td-cell signature-box">
                <div style="font-weight: bold; color: #334155;">Yogyakarta, {{ date('d F Y') }}</div>
                <div style="font-size: 9pt; color: #64748b;">Petugas Pengelola Bengkel (Toolman)</div>
                <br><br><br><br>
                <div style="font-weight: bold; text-decoration: underline;">( {{ $user->name }} )</div>
                <div style="font-size: 9pt; color: #64748b;">NIP / ID Petugas: #{{ $user->id }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
