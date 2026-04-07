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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversation_id')->constrained()->cascadeOnDelete();
            $table->uuid('sender_id')->constrained('users')->cascadeOnDelete();
            $table->longText('body')->nullable();
            $table->boolean('is_reply')->default(false);
            $table->uuid('message_id')->nullable();
            $table->json('read_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
