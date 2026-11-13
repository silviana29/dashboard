<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE siswa_profiles ALTER COLUMN gedung_id DROP NOT NULL');
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE siswa_profiles MODIFY gedung_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        // Lokasi lama tidak dipaksa kembali karena pengguna WFH/laporan dapat tidak memiliki gedung.
    }
};
