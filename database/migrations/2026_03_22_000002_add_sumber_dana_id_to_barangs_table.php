<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('barangs') && !Schema::hasColumn('barangs', 'sumber_dana_id')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->foreignId('sumber_dana_id')
                    ->nullable()
                    ->after('lokasi_penyimpanan_id')
                    ->constrained('sumber_danas')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('barangs') && Schema::hasColumn('barangs', 'sumber_dana_id')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->dropForeign(['sumber_dana_id']);
                $table->dropColumn('sumber_dana_id');
            });
        }
    }
};
