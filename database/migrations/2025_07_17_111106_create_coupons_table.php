<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('value', 10, 2);
            $table->decimal('minimum_value', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('active');
            $table->timestamps();
            
            $table->index('code');
            $table->index(['active', 'valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
