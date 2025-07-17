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
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('value', 10, 2);
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_until')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['code', 'active']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};