<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('phone', 30);
            $table->string('email', 150)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'phone']);
            $table->index(['business_id', 'last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_contacts');
    }
};
