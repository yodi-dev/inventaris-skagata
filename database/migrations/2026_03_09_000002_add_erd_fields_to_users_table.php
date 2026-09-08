<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bengkel_id')) {
                $table->foreignId('bengkel_id')->nullable()->after('id')->constrained('bengkels')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('peminjam')->after('password');
            }
            if (!Schema::hasColumn('users', 'jenis_peminjam')) {
                $table->string('jenis_peminjam')->nullable()->after('role'); // siswa, guru
            }
            if (!Schema::hasColumn('users', 'nomor_identitas')) {
                $table->string('nomor_identitas')->nullable()->after('jenis_peminjam'); // NIS/NIP
            }
            if (!Schema::hasColumn('users', 'nomor_wa')) {
                $table->string('nomor_wa')->nullable()->after('nomor_identitas');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('aktif')->after('nomor_wa'); // menunggu_acc, aktif, suspend
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'bengkel_id')) {
                $table->dropForeign(['bengkel_id']);
                $table->dropColumn('bengkel_id');
            }
            $columnsToDrop = array_filter(['role', 'jenis_peminjam', 'nomor_identitas', 'nomor_wa', 'status'], function ($col) {
                return Schema::hasColumn('users', $col);
            });
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
