<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa', 'pegawai', 'admin', 'guru', 'superadmin'))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('siswa', 'pegawai', 'admin', 'guru', 'superadmin') NOT NULL");
        }
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa', 'pegawai', 'admin', 'guru'))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('siswa', 'pegawai', 'admin', 'guru') NOT NULL");
        }
    }
};
