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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Public UUID (exposed in URLs)
            $table->uuid('uuid')->unique();

            // Owner
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Category
            $table->enum('category', [
                'gun',
                'ammunition',
                'airsoft',
                'accessory',
                'other',
            ]);

            // Buy or Sell
            $table->enum('listing_type', [
                'buy',
                'sell'
            ]);

            // Basic Listing Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');

            // Pricing
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('is_negotiable')->default(false);

            // Item Details
            $table->string('condition')->nullable();
            // new, used, refurbished, etc.

            // Location
            $table->string('location')->nullable();

            // Moderation & Status
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'expired'
            ])->default('pending');

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('rejection_reason')->nullable();

            // Visibility
            $table->boolean('is_featured')->default(false);

            // Tracking
            $table->unsignedBigInteger('views')->default(0);

            // Expiration
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
