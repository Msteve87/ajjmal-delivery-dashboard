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
            $table->string('reference')->unique();
            $table->string('payment_method');
            $table->enum('status', ['accepted', 'in_progress', 'dileverd', 'canceled']);
            $table->decimal('total_paid', 10, 2);
            $table->decimal('total_shipping', 10, 2);
            $table->string('customer_name')->nullable();
            $table->string('address');
            $table->string('customer_phone');
            $table->integer('items');
            $table->timestamps();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('location_id')->constrained();
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
