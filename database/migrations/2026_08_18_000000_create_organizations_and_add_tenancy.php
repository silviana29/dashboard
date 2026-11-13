<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('company');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('primary_color', 7)->default('#4f46e5');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $organizationId = DB::table('organizations')->insertGetId([
            'name' => 'Masimasa Workspace',
            'slug' => 'masimasa',
            'type' => 'company',
            'timezone' => 'Asia/Jakarta',
            'primary_color' => '#4f46e5',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['users', 'gedungs', 'settings', 'jenis_izin', 'log_activities'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('organization_id')->nullable()->index();
            });
            DB::table($tableName)->update(['organization_id' => $organizationId]);
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->unique(['organization_id', 'username']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_key_unique');
            $table->unique(['organization_id', 'key']);
        });

        Schema::table('jenis_izin', function (Blueprint $table) {
            $table->unique(['organization_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::table('jenis_izin', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'nama']);
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'key']);
            $table->unique('key');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'username']);
            $table->unique('username');
        });

        foreach (['users', 'gedungs', 'settings', 'jenis_izin', 'log_activities'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            });
        }

        Schema::dropIfExists('organizations');
    }
};
