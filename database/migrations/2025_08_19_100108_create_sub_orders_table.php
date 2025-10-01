<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracking_id')->unique();
            $table->decimal('base_price');
            $table->decimal('total');
            $table->decimal('shipping_price');
            $table->decimal('total_discounts')->default(0);
            $table->json('products')->nullable();
            $table->foreignId('sub_order_status_id')->constrained('sub_order_statuses');
            $table->foreignId('order_id')->constrained('orders');
            $table->timestamp('date_add')->nullable();
            $table->timestamp('date_upd')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('picked_up_by')->nullable();
            $table->foreign('picked_up_by')
                ->references('id')
                ->on('drivers');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_orders');
    }
};
