<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('sku')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('offer_price', 14, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->boolean('featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'slug']);
            $table->index(['business_id', 'is_active', 'featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
