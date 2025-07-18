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
            $table->string('customer_cpf')->nullable();
            $table->string('customer_cep');
            $table->string('customer_address');
            $table->string('customer_complement')->nullable();
            $table->string('customer_neighborhood');
            $table->string('customer_city');
            $table->string('customer_state', 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
            $table->string('coupon_code')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();
            
            $table->index('order_number');
            $table->index('customer_email');
            $table->index('status');
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
