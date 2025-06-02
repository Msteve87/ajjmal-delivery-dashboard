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
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->string('license')->nullable();
            $table->string('passport')->nullable();
            $table->string('criminal_case')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->string('vehicle_insurance')->nullable();
            $table->string('vehicle_license')->nullable();
            $table->foreignId('driver_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};
