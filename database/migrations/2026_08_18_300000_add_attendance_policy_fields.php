<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->text('keterangan_telat')->nullable()->after('aktivitas_hari_ini');
            $table->text('keterangan_pulang_cepat')->nullable()->after('keterangan_telat');
            $table->string('status_pulang')->nullable()->after('keterangan_pulang_cepat');
            $table->time('izin_dari_jam')->nullable()->after('keterangan_izin');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['keterangan_telat', 'keterangan_pulang_cepat', 'status_pulang', 'izin_dari_jam']);
        });
    }
};
