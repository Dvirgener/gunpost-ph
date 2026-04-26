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
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->uuid('initiator_id')->nullable()->constrained()->cascadeOnDelete();
            $table->uuid('post_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            // Fast lookups
            $table->index(['post_id', 'created_at'], 'conv_post_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
