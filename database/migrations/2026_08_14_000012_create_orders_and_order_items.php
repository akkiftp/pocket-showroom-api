<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_session_id')->nullable()->constrained('visitor_sessions')->nullOnDelete();
            $table->string('customer_name', 120);
            $table->string('phone', 30);
            $table->text('address')->nullable();
            $table->decimal('total', 14, 2)->default(0);
            $table->string('status', 30)->default('new');
            $table->string('source', 40)->default('showroom');
            $table->timestamps();
            $table->index(['business_id', 'status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name', 180);
            $table->decimal('price', 14, 2);
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
