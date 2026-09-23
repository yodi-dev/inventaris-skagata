<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('satuans')) {
            Schema::create('satuans', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('singkatan')->nullable();
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};
