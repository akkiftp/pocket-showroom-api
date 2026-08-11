<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('business_type')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('about')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('theme_primary', 20)->default('#6D4AFF');
            $table->string('theme_secondary', 20)->default('#EC4F8D');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
