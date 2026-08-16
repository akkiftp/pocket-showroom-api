<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('shop_owner')->after('is_admin')->index();
            }
            if (!Schema::hasColumn('users', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('role')->constrained('businesses')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('business_id');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('permissions');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'created_by')) $table->dropConstrainedForeignId('created_by');
            if (Schema::hasColumn('users', 'business_id')) $table->dropConstrainedForeignId('business_id');
            foreach (['role','permissions','is_active'] as $column) {
                if (Schema::hasColumn('users', $column)) $table->dropColumn($column);
            }
        });
    }
};
