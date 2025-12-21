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
        Schema::table('local_rates', function (Blueprint $table) {
            $table->renameColumn('rate', 'home_rate');
        });
        Schema::table('local_rates', function (Blueprint $table) {
            $table->decimal('locker_rate', 10, 2)->after('home_rate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('local_rates', function (Blueprint $table) {
            $table->dropColumn('locker_rate');
        });
        Schema::table('local_rates', function (Blueprint $table) {
            $table->renameColumn('home_rate', 'rate');
        });
    }
};
