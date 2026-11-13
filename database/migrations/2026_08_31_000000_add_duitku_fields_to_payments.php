<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('organization_id')->constrained('plans')->nullOnDelete();
            $table->string('gateway')->default('manual')->after('status');
            $table->string('merchant_order_id', 50)->nullable()->unique()->after('gateway');
            $table->string('gateway_reference')->nullable()->index()->after('reference');
            $table->text('payment_url')->nullable()->after('gateway_reference');
            $table->unsignedSmallInteger('duration_months')->default(1)->after('payment_url');
            $table->timestamp('expires_at')->nullable()->after('duration_months');
            $table->string('customer_email')->nullable()->after('expires_at');
            $table->string('customer_phone', 30)->nullable()->after('customer_email');
            $table->json('gateway_payload')->nullable()->after('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropUnique(['merchant_order_id']);
            $table->dropIndex(['gateway_reference']);
            $table->dropColumn([
                'plan_id',
                'gateway',
                'merchant_order_id',
                'gateway_reference',
                'payment_url',
                'duration_months',
                'expires_at',
                'customer_email',
                'customer_phone',
                'gateway_payload',
            ]);
        });
    }
};
