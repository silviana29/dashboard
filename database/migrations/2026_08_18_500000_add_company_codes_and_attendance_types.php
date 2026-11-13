<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('company_code', 16)->nullable()->unique()->after('slug');
        });

        foreach (DB::table('organizations')->orderBy('id')->get() as $organization) {
            DB::table('organizations')->where('id', $organization->id)->update([
                'company_code' => $this->uniqueCompanyCode(),
            ]);
        }

        Schema::create('attendance_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name', 80);
            $table->string('mode', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id', 'name']);
        });

        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->foreignId('attendance_type_id')->nullable()->after('gedung_id')->constrained('attendance_types')->nullOnDelete();
        });
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE siswa_profiles MODIFY gedung_id BIGINT UNSIGNED NULL');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE siswa_profiles ALTER COLUMN gedung_id DROP NOT NULL');
        }

        Schema::table('absensis', function (Blueprint $table) {
            $table->foreignId('attendance_type_id')->nullable()->after('siswa_id')->constrained('attendance_types')->nullOnDelete();
            $table->string('attendance_type_name', 80)->nullable()->after('attendance_type_id');
            $table->string('attendance_mode', 20)->nullable()->after('attendance_type_name');
            $table->timestamp('report_at')->nullable()->after('attendance_mode');
        });

        foreach (DB::table('organizations')->orderBy('id')->get() as $organization) {
            $typeIds = [];
            foreach ([['WFO', 'wfo'], ['WFH', 'wfh'], ['Lapor Harian', 'report']] as [$name, $mode]) {
                $typeIds[$mode] = DB::table('attendance_types')->insertGetId([
                    'organization_id' => $organization->id,
                    'name' => $name,
                    'mode' => $mode,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('siswa_profiles')
                ->whereIn('user_id', DB::table('users')->where('organization_id', $organization->id)->select('id'))
                ->update(['attendance_type_id' => $typeIds['wfo']]);
        }
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_type_id');
            $table->dropColumn(['attendance_type_name', 'attendance_mode', 'report_at']);
        });
        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_type_id');
        });
        Schema::dropIfExists('attendance_types');
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['company_code']);
            $table->dropColumn('company_code');
        });
    }

    private function uniqueCompanyCode(): string
    {
        do {
            $code = 'MM-' . Str::upper(Str::random(8));
        } while (DB::table('organizations')->where('company_code', $code)->exists());

        return $code;
    }
};
