<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('share_links')) {
            Schema::create('share_links', function (Blueprint $table) {
                $table->id();
                $table->string('code', 32)->unique();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('business_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('platform', 40)->default('whatsapp');
                $table->string('source', 40)->default('whatsapp_share');
                $table->unsignedBigInteger('click_count')->default(0);
                $table->timestamps();
                $table->index(['business_id', 'code']);
            });
        }

        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 100);
                $table->string('entity_type', 100);
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['entity_type', 'entity_id']);
                $table->index(['actor_user_id', 'created_at']);
            });
        }

        if (Schema::hasTable('activity_events')) {
            Schema::table('activity_events', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_events', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('business_id')->constrained()->nullOnDelete();
                }
                if (!Schema::hasColumn('activity_events', 'visitor_uuid')) {
                    $table->string('visitor_uuid', 64)->nullable()->after('user_id')->index();
                }
                if (!Schema::hasColumn('activity_events', 'share_id')) {
                    $table->foreignId('share_id')->nullable()->after('product_id')->constrained('share_links')->nullOnDelete();
                }
                if (!Schema::hasColumn('activity_events', 'platform')) {
                    $table->string('platform', 40)->nullable()->after('source');
                }
                if (!Schema::hasColumn('activity_events', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('metadata');
                }
                if (!Schema::hasColumn('activity_events', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('share_links');
        Schema::dropIfExists('audit_logs');
    }
};
