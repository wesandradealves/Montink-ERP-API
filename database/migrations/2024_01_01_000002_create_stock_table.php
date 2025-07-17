<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('variation_key')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('available')->storedAs('quantity - reserved');
            $table->timestamps();
            
            $table->unique(['product_id', 'variation_key']);
            $table->index(['product_id', 'available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};