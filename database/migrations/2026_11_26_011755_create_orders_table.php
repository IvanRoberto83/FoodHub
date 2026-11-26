<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();      

            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('restaurant_id')->constrained()->restrictOnDelete();

            $table->string('status', 20)->default('pending')->index();

            $table->unsignedInteger('total_price');
            $table->text('delivery_address');
            $table->text('notes')->nullable();
            $table->string('payment_method', 20)->default('cod');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
