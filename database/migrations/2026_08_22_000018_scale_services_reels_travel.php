<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Existing releases used 1 owner = 1 business. Remove that restriction.
        try { Schema::table('businesses', fn(Blueprint $t) => $t->dropUnique('businesses_user_id_unique')); } catch (\Throwable $e) {}

        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses','business_mode')) $table->string('business_mode',30)->default('product')->after('business_type');
            if (!Schema::hasColumn('businesses','service_mode')) $table->string('service_mode',30)->nullable()->after('business_mode');
            if (!Schema::hasColumn('businesses','service_radius_km')) $table->decimal('service_radius_km',8,2)->nullable()->after('service_mode');
        });

        Schema::create('business_members', function (Blueprint $table) {
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role',30)->default('staff'); $table->boolean('is_active')->default(true); $table->timestamps();
            $table->unique(['business_id','user_id']); $table->index(['user_id','is_active']);
        });
        DB::table('businesses')->select('id','user_id')->whereNotNull('user_id')->orderBy('id')->chunk(500,function($rows){
            foreach($rows as $row) DB::table('business_members')->updateOrInsert(['business_id'=>$row->id,'user_id'=>$row->user_id],['role'=>'owner','is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
        });

        Schema::create('business_marketplace_category', function(Blueprint $table){
            $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('marketplace_category_id')->constrained('marketplace_categories')->cascadeOnDelete();
            $table->primary(['business_id','marketplace_category_id']);
        });
        DB::table('businesses')->whereNotNull('marketplace_category_id')->select('id','marketplace_category_id')->orderBy('id')->chunk(500,function($rows){
            foreach($rows as $row) DB::table('business_marketplace_category')->updateOrInsert(['business_id'=>$row->id,'marketplace_category_id'=>$row->marketplace_category_id]);
        });

        Schema::create('services', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('slug');
            $table->text('description')->nullable(); $table->decimal('price',12,2)->nullable(); $table->string('price_type',30)->default('fixed');
            $table->unsignedInteger('duration_minutes')->nullable(); $table->string('image_url',700)->nullable(); $table->boolean('home_service')->default(true);
            $table->boolean('at_shop')->default(false); $table->boolean('is_active')->default(true); $table->unsignedInteger('sort_order')->default(0); $table->timestamps(); $table->softDeletes();
            $table->unique(['business_id','slug']); $table->index(['business_id','is_active','sort_order']); $table->index('name');
        });
        Schema::create('service_bookings', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->string('customer_name'); $table->string('customer_phone',25); $table->string('customer_email')->nullable();
            $table->text('address'); $table->string('city')->nullable(); $table->string('locality')->nullable(); $table->string('pincode',12)->nullable();
            $table->decimal('latitude',10,7)->nullable(); $table->decimal('longitude',10,7)->nullable(); $table->date('booking_date'); $table->time('booking_time')->nullable();
            $table->text('problem_description')->nullable(); $table->string('status',30)->default('pending'); $table->decimal('quoted_amount',12,2)->nullable(); $table->text('business_notes')->nullable(); $table->timestamps();
            $table->index(['business_id','status','booking_date']); $table->index(['customer_phone','created_at']);
        });

        Schema::create('business_reels', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete(); $table->string('video_url',1000); $table->string('thumbnail_url',1000)->nullable(); $table->string('caption',1000)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable(); $table->boolean('is_active')->default(true); $table->boolean('is_promoted')->default(false);
            $table->unsignedBigInteger('views_count')->default(0); $table->unsignedBigInteger('likes_count')->default(0); $table->unsignedBigInteger('shares_count')->default(0); $table->timestamps();
            $table->index(['is_active','is_promoted','created_at']); $table->index(['business_id','is_active','created_at']);
        });

        Schema::create('travel_vehicles', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('vehicle_type',40); $table->string('registration_number')->nullable();
            $table->unsignedInteger('seats')->nullable(); $table->boolean('ac')->default(false); $table->decimal('price_per_km',12,2)->nullable(); $table->decimal('price_per_day',12,2)->nullable();
            $table->string('image_url',700)->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); $table->index(['business_id','vehicle_type','is_active']);
        });
        Schema::create('travel_routes', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('vehicle_id')->nullable()->constrained('travel_vehicles')->nullOnDelete();
            $table->string('from_city'); $table->string('to_city'); $table->time('departure_time')->nullable(); $table->time('arrival_time')->nullable(); $table->decimal('fare',12,2)->nullable();
            $table->string('days_of_week')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); $table->index(['from_city','to_city','is_active']);
        });
        Schema::create('travel_bookings', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('vehicle_id')->nullable()->constrained('travel_vehicles')->nullOnDelete(); $table->foreignId('route_id')->nullable()->constrained('travel_routes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->string('customer_name'); $table->string('customer_phone',25); $table->string('pickup'); $table->string('destination');
            $table->date('travel_date'); $table->time('travel_time')->nullable(); $table->unsignedInteger('passengers')->default(1); $table->string('status',30)->default('pending'); $table->decimal('quoted_amount',12,2)->nullable(); $table->text('notes')->nullable(); $table->timestamps();
            $table->index(['business_id','status','travel_date']); $table->index(['pickup','destination','travel_date']);
        });
        Schema::create('business_followers', function(Blueprint $table){
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->timestamps(); $table->unique(['business_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_followers'); Schema::dropIfExists('travel_bookings'); Schema::dropIfExists('travel_routes'); Schema::dropIfExists('travel_vehicles'); Schema::dropIfExists('business_reels'); Schema::dropIfExists('service_bookings'); Schema::dropIfExists('services'); Schema::dropIfExists('business_marketplace_category'); Schema::dropIfExists('business_members');
        Schema::table('businesses', fn(Blueprint $t) => $t->dropColumn(['business_mode','service_mode','service_radius_km']));
    }
};
