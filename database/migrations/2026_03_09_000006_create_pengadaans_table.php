<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengadaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bengkel_id')->constrained('bengkels')->cascadeOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('status')->default('draft'); // draft, pending, revisi, approved, rejected
            $table->text('catatan')->nullable();
            $table->text('catatan_review')->nullable();
            $table->dateTime('diajukan_pada')->nullable();
            $table->foreignId('direview_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('direview_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengadaans');
    }
};
