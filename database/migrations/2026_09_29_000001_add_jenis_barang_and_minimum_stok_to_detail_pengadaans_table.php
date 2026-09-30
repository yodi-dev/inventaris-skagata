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
        if (Schema::hasTable('detail_pengadaans')) {
            Schema::table('detail_pengadaans', function (Blueprint $table) {
                if (!Schema::hasColumn('detail_pengadaans', 'jenis_barang')) {
                    $table->enum('jenis_barang', ['inventaris', 'bhp'])->nullable()->after('satuan');
                }
                if (!Schema::hasColumn('detail_pengadaans', 'minimum_stok')) {
                    $table->integer('minimum_stok')->nullable()->after('jenis_barang');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('detail_pengadaans')) {
            Schema::table('detail_pengadaans', function (Blueprint $table) {
                if (Schema::hasColumn('detail_pengadaans', 'minimum_stok')) {
                    $table->dropColumn('minimum_stok');
                }
                if (Schema::hasColumn('detail_pengadaans', 'jenis_barang')) {
                    $table->dropColumn('jenis_barang');
                }
            });
        }
    }
};
