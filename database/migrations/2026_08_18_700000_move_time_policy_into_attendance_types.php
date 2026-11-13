<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_types', function (Blueprint $table) {
            $table->time('jam_telat')->nullable()->after('mode');
            $table->time('jam_pulang_minimal')->nullable()->after('jam_telat');
        });

        foreach (DB::table('organizations')->get() as $organization) {
            $settings = DB::table('settings')
                ->where('organization_id', $organization->id)
                ->whereIn('key', ['jam_telat', 'jam_pulang_minimal'])
                ->pluck('value', 'key');

            DB::table('attendance_types')
                ->where('organization_id', $organization->id)
                ->where('mode', 'wfo')
                ->update([
                    'mode' => 'scheduled',
                    'jam_telat' => $settings->get('jam_telat', '08:15'),
                    'jam_pulang_minimal' => $settings->get('jam_pulang_minimal', '17:00'),
                ]);

            DB::table('attendance_types')
                ->where('organization_id', $organization->id)
                ->where('mode', 'wfh')
                ->update(['mode' => 'flexible']);

            DB::table('attendance_types')->where('organization_id', $organization->id)->where('name', 'WFO')->update(['name' => 'Terjadwal']);
            DB::table('attendance_types')->where('organization_id', $organization->id)->where('name', 'WFH')->update(['name' => 'Fleksibel']);
        }

        DB::table('absensis')->where('attendance_mode', 'wfo')->update(['attendance_mode' => 'scheduled']);
        DB::table('absensis')->where('attendance_mode', 'wfh')->update(['attendance_mode' => 'flexible']);
    }

    public function down(): void
    {
        DB::table('attendance_types')->where('mode', 'scheduled')->update(['mode' => 'wfo']);
        DB::table('attendance_types')->where('mode', 'flexible')->update(['mode' => 'wfh']);
        Schema::table('attendance_types', function (Blueprint $table) {
            $table->dropColumn(['jam_telat', 'jam_pulang_minimal']);
        });
    }
};
