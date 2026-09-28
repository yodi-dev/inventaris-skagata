<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah foreign key beruntun (cascade) menjadi restrict pada tabel
     * peminjaman, mutasi stok, pengadaan, dan barang agar rekam jejak historis
     * tidak terhapus saat user, barang, atau bengkel dihapus.
     */
    public function up(): void
    {
        // 1. Peminjamans: user_id & bengkel_id
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['bengkel_id']);
        });

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('bengkel_id')->references('id')->on('bengkels')->restrictOnDelete();
        });

        // 2. Detail Peminjamans: barang_id
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barangs')->restrictOnDelete();
        });

        // 3. Stock Movements: barang_id & user_id
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barangs')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        // 4. Pengadaans: bengkel_id & dibuat_oleh
        Schema::table('pengadaans', function (Blueprint $table) {
            $table->dropForeign(['bengkel_id']);
            $table->dropForeign(['dibuat_oleh']);
        });

        Schema::table('pengadaans', function (Blueprint $table) {
            $table->foreign('bengkel_id')->references('id')->on('bengkels')->restrictOnDelete();
            $table->foreign('dibuat_oleh')->references('id')->on('users')->restrictOnDelete();
        });

        // 5. Barangs: bengkel_id
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['bengkel_id']);
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->foreign('bengkel_id')->references('id')->on('bengkels')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 5. Barangs: bengkel_id
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['bengkel_id']);
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->foreign('bengkel_id')->references('id')->on('bengkels')->cascadeOnDelete();
        });

        // 4. Pengadaans: bengkel_id & dibuat_oleh
        Schema::table('pengadaans', function (Blueprint $table) {
            $table->dropForeign(['bengkel_id']);
            $table->dropForeign(['dibuat_oleh']);
        });

        Schema::table('pengadaans', function (Blueprint $table) {
            $table->foreign('bengkel_id')->references('id')->on('bengkels')->cascadeOnDelete();
            $table->foreign('dibuat_oleh')->references('id')->on('users')->cascadeOnDelete();
        });

        // 3. Stock Movements: barang_id & user_id
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barangs')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // 2. Detail Peminjamans: barang_id
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barangs')->cascadeOnDelete();
        });

        // 1. Peminjamans: user_id & bengkel_id
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['bengkel_id']);
        });

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('bengkel_id')->references('id')->on('bengkels')->cascadeOnDelete();
        });
    }
};
