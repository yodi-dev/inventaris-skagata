<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis'); // stok_masuk, peminjaman, pengembalian_baik, pengembalian_rusak, barang_hilang, bhp_keluar, perbaikan, penyesuaian
            $table->integer('jumlah');
            $table->string('referensi_tipe')->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
