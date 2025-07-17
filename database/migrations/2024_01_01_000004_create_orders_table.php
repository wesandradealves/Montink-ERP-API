<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->json('shipping_address');
            $table->string('zip_code', 8);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])
                ->default('pending');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('order_number');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};