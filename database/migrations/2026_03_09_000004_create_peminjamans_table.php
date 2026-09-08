<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bengkel_id')->constrained('bengkels')->cascadeOnDelete();
            $table->dateTime('tanggal_pinjam');
            $table->dateTime('batas_kembali')->nullable();
            $table->text('keperluan');
            $table->string('status')->default('pending'); // pending, active, terlambat, menunggu_pengecekan, selesai, ditolak
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diproses_pada')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
