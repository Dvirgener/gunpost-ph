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
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

            $table->foreignId('reviewed_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            // KYC
            $table->string('kyc_status', 20)->default('pending'); // pending|approved|rejected
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('kyc_notes')->nullable();

            // Identity docs (personal or representative)
            $table->string('government_id_type', 50)->nullable();
            $table->string('government_id_number', 80)->nullable(); // consider encrypting at model level
            $table->string('government_id_front_path')->nullable();
            $table->string('government_id_back_path')->nullable();
            $table->string('selfie_with_id_path')->nullable();

            $table->timestamps();

            $table->index(['kyc_status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
