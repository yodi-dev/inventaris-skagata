<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('bengkel_id')->nullable()->after('id')->constrained('bengkels')->nullOnDelete();
            $table->string('role')->default('peminjam')->after('password');
            $table->string('jenis_peminjam')->nullable()->after('role'); // siswa, guru
            $table->string('nomor_identitas')->nullable()->after('jenis_peminjam'); // NIS/NIP
            $table->string('nomor_wa')->nullable()->after('nomor_identitas');
            $table->string('status')->default('aktif')->after('nomor_wa'); // menunggu_acc, aktif, suspend
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bengkel_id']);
            $table->dropColumn(['bengkel_id', 'role', 'jenis_peminjam', 'nomor_identitas', 'nomor_wa', 'status']);
        });
    }
};
