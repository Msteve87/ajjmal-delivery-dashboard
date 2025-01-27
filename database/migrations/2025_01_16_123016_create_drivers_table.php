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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->enum('driver_type', ['employee', 'independent']);
            $table->date('dob')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('criminal_case')->nullable();
            $table->string('national_no')->nullable();
            $table->enum('delivery_status', ['avilable', 'not_available']);
            $table->enum('status', ['pending', 'processing ', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('driver_password_reset_tokens', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('token')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('driver_password_reset_tokens');
    }
};
