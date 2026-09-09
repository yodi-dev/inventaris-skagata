<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bengkel_id')->constrained('bengkels')->cascadeOnDelete();
            $table->foreignId('lokasi_penyimpanan_id')->nullable()->constrained('lokasi_penyimpanans')->nullOnDelete();
            $table->string('kode_barang');
            $table->string('nama');
            $table->enum('jenis_barang', ['inventaris', 'bhp']);
            $table->string('satuan');
            $table->integer('stok_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_dipinjam')->default(0);
            $table->integer('stok_rusak')->default(0);
            $table->integer('minimum_stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['bengkel_id', 'kode_barang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
