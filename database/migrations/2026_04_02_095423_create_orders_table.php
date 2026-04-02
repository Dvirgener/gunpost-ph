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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('package');
            $table->bigInteger('quantity')->default(1);
            $table->bigInteger('credits')->default(0);
            $table->string('status')->default('pending');
            $table->string('ticket_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->bigInteger('amount')->default(0);
            $table->foreignId('confirmed_by_id')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
