<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status', 20)->default('trial')->after('phone');
            }
            if (!Schema::hasColumn('users', 'trial_expires_at')) {
                $table->timestamp('trial_expires_at')->nullable()->after('subscription_status');
            }
            if (!Schema::hasColumn('users', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('trial_expires_at');
            }
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('subscription_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_status', 'trial_expires_at', 'subscription_expires_at', 'is_admin']);
        });
    }
};
