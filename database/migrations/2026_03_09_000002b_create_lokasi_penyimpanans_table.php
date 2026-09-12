<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_penyimpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bengkel_id')->constrained('bengkels')->cascadeOnDelete();
            $table->string('kode');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['bengkel_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_penyimpanans');
    }
};
