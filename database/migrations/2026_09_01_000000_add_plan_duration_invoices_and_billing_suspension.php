<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_value')->default(1)->after('price');
            $table->string('duration_unit', 10)->default('months')->after('duration_value');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_number', 50)->nullable()->unique()->after('merchant_order_id');
            $table->timestamp('invoice_issued_at')->nullable()->after('invoice_number');
            $table->unsignedSmallInteger('duration_value')->default(1)->after('duration_months');
            $table->string('duration_unit', 10)->default('months')->after('duration_value');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->timestamp('subscription_reminder_sent_at')->nullable()->after('subscription_ends_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_billing_suspended')->default(false)->after('approval_status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_billing_suspended');
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('subscription_reminder_sent_at');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropColumn(['invoice_number', 'invoice_issued_at', 'duration_value', 'duration_unit']);
        });
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['duration_value', 'duration_unit']);
        });
    }
};
