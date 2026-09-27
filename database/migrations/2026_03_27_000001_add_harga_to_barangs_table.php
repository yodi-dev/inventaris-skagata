<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('barangs') && !Schema::hasColumn('barangs', 'harga')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->decimal('harga', 15, 2)->default(0)->after('satuan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('barangs') && Schema::hasColumn('barangs', 'harga')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->dropColumn('harga');
            });
        }
    }
};
