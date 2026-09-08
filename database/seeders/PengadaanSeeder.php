<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\Pengadaan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PengadaanSeeder extends Seeder
{
    public function run(): void
    {
        $tkj = Bengkel::where('kode', 'BGK-TKJ')->first();
        $tkr = Bengkel::where('kode', 'BGK-TKR')->first();

        $waka       = User::where('email', 'waka@skagata.sch.id')->first();
        $toolmanTkj = User::where('email', 'toolman.tkj@skagata.sch.id')->first();
        $toolmanTkr = User::where('email', 'toolman.tkr@skagata.sch.id')->first();

        $router   = Barang::where('kode_barang', 'INV-TKJ-001')->first();
        $switch   = Barang::where('kode_barang', 'INV-TKJ-002')->first();
        $kabel    = Barang::where('kode_barang', 'BHP-TKJ-001')->first();
        $konektor = Barang::where('kode_barang', 'BHP-TKJ-002')->first();
        $kunciPas = Barang::where('kode_barang', 'INV-TKR-001')->first();
        $oli      = Barang::where('kode_barang', 'BHP-TKR-001')->first();

        // ── 1. DRAFT — Toolman TKJ (belum diajukan) ───────────────────
        $rab1 = Pengadaan::create([
            'bengkel_id'     => $tkj->id,
            'dibuat_oleh'    => $toolmanTkj->id,
            'judul'          => 'Pengadaan Peralatan Jaringan Semester Gasal 2026',
            'status'         => 'draft',
            'catatan'        => 'Draft awal untuk memenuhi kebutuhan praktik jaringan semester gasal.',
            'catatan_review' => null,
            'diajukan_pada'  => null,
            'direview_oleh'  => null,
            'direview_pada'  => null,
        ]);
        $rab1->detailPengadaans()->createMany([
            [
                'barang_id'   => $router->id,
                'nama_barang' => 'Router Cisco 891',
                'spesifikasi' => 'Router seri 891, 2x FE WAN, 8x FE LAN, IOS 15.x',
                'jumlah'      => 2,
                'satuan'      => 'unit',
                'harga_satuan' => 3500000,
            ],
            [
                'barang_id'   => $konektor->id,
                'nama_barang' => 'Konektor RJ45 Cat6',
                'spesifikasi' => 'Konektor RJ45 standard Cat6, kemasan 100 pcs/box',
                'jumlah'      => 3,
                'satuan'      => 'box',
                'harga_satuan' => 85000,
            ],
        ]);

        // ── 2. PENDING — Toolman TKJ (sudah diajukan, menunggu review) ─
        $rab2 = Pengadaan::create([
            'bengkel_id'     => $tkj->id,
            'dibuat_oleh'    => $toolmanTkj->id,
            'judul'          => 'Pengadaan BHP Kabel UTP dan Tang Crimping',
            'status'         => 'pending',
            'catatan'        => 'Stok kabel UTP hampir habis dan 1 tang crimping rusak, perlu penggantian segera.',
            'catatan_review' => null,
            'diajukan_pada'  => Carbon::now()->subDays(3),
            'direview_oleh'  => null,
            'direview_pada'  => null,
        ]);
        $rab2->detailPengadaans()->createMany([
            [
                'barang_id'   => $kabel->id,
                'nama_barang' => 'Kabel UTP Cat6',
                'spesifikasi' => 'Kabel UTP Cat6, per gulung 305 meter, merek Belden / AMP',
                'jumlah'      => 1,
                'satuan'      => 'gulung',
                'harga_satuan' => 850000,
            ],
            [
                'barang_id'   => null, // Tang Crimping baru (belum ada di master barang)
                'nama_barang' => 'Tang Crimping RJ45 Pro',
                'spesifikasi' => 'Tang crimping profesional untuk RJ45 dan RJ11, merek Ratchet',
                'jumlah'      => 2,
                'satuan'      => 'buah',
                'harga_satuan' => 125000,
            ],
        ]);

        // ── 3. REVISI — Toolman TKR (dikembalikan Waka untuk direvisi) ─
        $rab3 = Pengadaan::create([
            'bengkel_id'     => $tkr->id,
            'dibuat_oleh'    => $toolmanTkr->id,
            'judul'          => 'Pengadaan Alat Tangan Otomotif 2026',
            'status'         => 'revisi',
            'catatan'        => 'Kunci pas dan ring set sudah aus, perlu penggantian untuk kelancaran praktik.',
            'catatan_review' => 'Harap lengkapi spesifikasi merek dan model alat. Tambahkan juga harga referensi dari minimal 2 supplier berbeda.',
            'diajukan_pada'  => Carbon::now()->subDays(7),
            'direview_oleh'  => $waka->id,
            'direview_pada'  => Carbon::now()->subDays(5),
        ]);
        $rab3->detailPengadaans()->createMany([
            [
                'barang_id'   => $kunciPas->id,
                'nama_barang' => 'Kunci Pas Set',
                'spesifikasi' => null, // perlu dilengkapi sesuai catatan revisi
                'jumlah'      => 3,
                'satuan'      => 'set',
                'harga_satuan' => 0, // belum diisi
            ],
            [
                'barang_id'   => null, // Kunci Momen (belum ada di master)
                'nama_barang' => 'Kunci Momen (Torque Wrench)',
                'spesifikasi' => null,
                'jumlah'      => 2,
                'satuan'      => 'buah',
                'harga_satuan' => 0,
            ],
        ]);

        // ── 4. APPROVED — Toolman TKJ (disetujui Waka) ────────────────
        $rab4 = Pengadaan::create([
            'bengkel_id'     => $tkj->id,
            'dibuat_oleh'    => $toolmanTkj->id,
            'judul'          => 'Pengadaan Switch Manageable dan Patch Panel',
            'status'         => 'approved',
            'catatan'        => 'Dibutuhkan untuk pembangunan lab jaringan baru ruang B.207.',
            'catatan_review' => 'Disetujui. Segera lakukan pembelian dan catat penerimaan barang di sistem.',
            'diajukan_pada'  => Carbon::now()->subDays(14),
            'direview_oleh'  => $waka->id,
            'direview_pada'  => Carbon::now()->subDays(10),
        ]);
        $rab4->detailPengadaans()->createMany([
            [
                'barang_id'   => $switch->id,
                'nama_barang' => 'Switch Cisco SG110-24',
                'spesifikasi' => 'Switch manageable 24 port 10/100/1000Mbps, rack mountable 1U',
                'jumlah'      => 2,
                'satuan'      => 'unit',
                'harga_satuan' => 2800000,
            ],
            [
                'barang_id'   => null, // Patch Panel (belum ada di master)
                'nama_barang' => 'Patch Panel 24 Port Cat6',
                'spesifikasi' => 'Patch panel 24 port Cat6 568B, rack mountable, merek AMP / Panduit',
                'jumlah'      => 2,
                'satuan'      => 'unit',
                'harga_satuan' => 650000,
            ],
        ]);

        // ── 5. REJECTED — Toolman TKJ (ditolak Waka) ──────────────────
        $rab5 = Pengadaan::create([
            'bengkel_id'     => $tkj->id,
            'dibuat_oleh'    => $toolmanTkj->id,
            'judul'          => 'Pengadaan Server Rack untuk Lab Cloud Computing',
            'status'         => 'rejected',
            'catatan'        => 'Rencana pengembangan lab cloud computing membutuhkan server dan rack khusus.',
            'catatan_review' => 'Ditolak. Pengadaan server tidak masuk dalam RKAS tahun ini. Ajukan kembali pada periode perencanaan anggaran tahun depan.',
            'diajukan_pada'  => Carbon::now()->subDays(20),
            'direview_oleh'  => $waka->id,
            'direview_pada'  => Carbon::now()->subDays(18),
        ]);
        $rab5->detailPengadaans()->createMany([
            [
                'barang_id'   => null, // Barang baru, belum ada di master
                'nama_barang' => 'Server Rack 2U PowerEdge',
                'spesifikasi' => 'Server Dell PowerEdge R350, Intel Xeon E-2336, RAM 16GB, HDD 1TB, OS Ubuntu Server 22.04',
                'jumlah'      => 1,
                'satuan'      => 'unit',
                'harga_satuan' => 28000000,
            ],
            [
                'barang_id'   => null,
                'nama_barang' => 'Rack Cabinet 12U',
                'spesifikasi' => 'Server rack cabinet open frame 12U, 600x600mm, dengan kunci dan roda',
                'jumlah'      => 1,
                'satuan'      => 'unit',
                'harga_satuan' => 3200000,
            ],
        ]);
    }
}
