<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('marketplace_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['parent_id','is_active','sort_order']);
        });

        Schema::create('marketplace_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['city','town','village'])->default('city');
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 12)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['state','district','type','is_active']);
            $table->index('pincode');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignId('marketplace_category_id')->nullable()->after('business_type')->constrained('marketplace_categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->after('city')->constrained('marketplace_locations')->nullOnDelete();
            $table->string('locality')->nullable()->after('location_id');
            $table->string('pincode', 12)->nullable()->after('locality');
            $table->decimal('latitude', 10, 7)->nullable()->after('pincode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('opening_time', 5)->nullable()->after('longitude');
            $table->string('closing_time', 5)->nullable()->after('opening_time');
            $table->boolean('delivery_available')->default(false)->after('closing_time');
            $table->boolean('accepts_orders')->default(true)->after('delivery_available');
            $table->boolean('is_verified')->default(false)->after('accepts_orders');
            $table->boolean('is_featured')->default(false)->after('is_verified');
            $table->unsignedBigInteger('marketplace_views')->default(0)->after('is_featured');
            $table->index(['marketplace_category_id','location_id','is_active']);
            $table->index(['city','pincode','is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('marketplace_category_id');
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn(['locality','pincode','latitude','longitude','opening_time','closing_time','delivery_available','accepts_orders','is_verified','is_featured','marketplace_views']);
        });
        Schema::dropIfExists('marketplace_locations');
        Schema::dropIfExists('marketplace_categories');
    }
};
