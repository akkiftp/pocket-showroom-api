<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->uuid('visitor_token');
            $table->string('customer_name', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('source', 40)->default('web');
            $table->string('referrer', 1000)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('events_count')->default(0);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'visitor_token']);
            $table->index(['business_id', 'last_seen_at']);
            $table->index(['business_id', 'phone']);
        });

        Schema::create('activity_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_session_id')->nullable()->constrained('visitor_sessions')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 50);
            $table->string('source', 40)->default('web');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'event_type', 'created_at']);
            $table->index(['product_id', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_events');
        Schema::dropIfExists('visitor_sessions');
    }
};
