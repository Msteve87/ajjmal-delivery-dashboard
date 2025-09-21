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
        Schema::table('sub_orders', function (Blueprint $table) {
            Schema::table('sub_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('picked_up_by')->nullable()->after('driver_id');
                $table->foreign('picked_up_by')
                    ->references('id')
                    ->on('drivers');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_orders', function (Blueprint $table) {
            $table->dropForeign(['picked_up_by']);
            $table->dropColumn('picked_up_by');
        });
    }
};
