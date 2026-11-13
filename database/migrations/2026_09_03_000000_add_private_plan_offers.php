<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('has_custom_suffix')->index();
            $table->softDeletes();
        });

        Schema::create('plan_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->foreignId('support_thread_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('offered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('offered')->index();
            $table->timestamp('offered_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'plan_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('plan_offer_id')->nullable()->after('plan_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_offer_id');
        });
        Schema::dropIfExists('plan_offers');
        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex(['is_private']);
            $table->dropColumn(['is_private', 'deleted_at']);
        });
    }
};
