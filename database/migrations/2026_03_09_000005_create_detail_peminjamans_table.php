<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->integer('jumlah_baik')->default(0);
            $table->integer('jumlah_rusak')->default(0);
            $table->integer('jumlah_hilang')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjamans');
    }
};
