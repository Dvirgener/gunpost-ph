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
        Schema::create('corporate_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

            // Business identity
            $table->string('company_name', 150);
            $table->string('business_type', 80)->nullable(); // gun store|dealer|distributor|etc

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('country', 80)->nullable()->default('Philippines');

            // Contact / presence
            $table->string('business_email')->nullable();
            $table->string('business_phone', 30)->nullable();
            $table->string('website')->nullable();

            // Document / picture Paths
            $table->string('logo_path')->nullable();
            $table->string('dti_sec_reg_path', 255)->nullable(); // DTI/SEC Path
            $table->string('business_permit_path', 255)->nullable(); // Business Permit Path

            $table->index(['company_name']);
            $table->index(['province', 'city']);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_profiles');
    }
};
