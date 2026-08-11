<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('source', 30)->default('showroom');
            $table->timestamps();

            $table->index(['business_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
