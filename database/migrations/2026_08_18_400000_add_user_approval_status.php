<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status')->default('approved')->after('role')->index();
            $table->timestamp('requested_at')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'requested_at', 'approved_at']);
        });
    }
};
