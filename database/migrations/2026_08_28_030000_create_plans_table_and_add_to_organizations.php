<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 14, 2);
            $table->integer('max_users');
            $table->integer('retention_months')->nullable();
            $table->boolean('has_audit_log')->default(false);
            $table->boolean('has_custom_suffix')->default(false);
            $table->timestamps();
        });

        // Seed default plans
        DB::table('plans')->insert([
            [
                'name' => 'Free',
                'price' => 0,
                'max_users' => 10,
                'retention_months' => 3,
                'has_audit_log' => false,
                'has_custom_suffix' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'price' => 150000,
                'max_users' => 100,
                'retention_months' => null,
                'has_audit_log' => true,
                'has_custom_suffix' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'price' => 500000,
                'max_users' => 1000,
                'retention_months' => null,
                'has_audit_log' => true,
                'has_custom_suffix' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
        });

        // Migrate old text plans to new plan_ids
        $freeId = DB::table('plans')->where('name', 'Free')->value('id');
        $proId = DB::table('plans')->where('name', 'Pro')->value('id');
        $enterpriseId = DB::table('plans')->where('name', 'Enterprise')->value('id');

        DB::table('organizations')->where('plan', 'trial')->update(['plan_id' => $freeId, 'plan' => 'Free']);
        DB::table('organizations')->whereIn('plan', ['starter', 'business'])->update(['plan_id' => $proId, 'plan' => 'Pro']);
        DB::table('organizations')->where('plan', 'enterprise')->update(['plan_id' => $enterpriseId, 'plan' => 'Enterprise']);
        // Fallback for any other custom plan to Free
        DB::table('organizations')->whereNull('plan_id')->update(['plan_id' => $freeId, 'plan' => 'Free']);
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
        Schema::dropIfExists('plans');
    }
};
