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
        Schema::create('ammunitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->unique()->constrained('posts')->cascadeOnDelete();

            // Identity
            $table->string('brand')->nullable();
            $table->string('product_line')->nullable();
            $table->string('caliber')->nullable();
            $table->string('bullet_type')->nullable();       // FMJ, JHP, etc (string for flexibility)
            $table->string('grain')->nullable();             // keep string: "115gr"
            $table->string('case_material')->nullable();     // brass/steel/aluminum
            $table->string('primer_type')->nullable();       // boxer/berdan (optional)
            $table->boolean('corrosive')->default(false);

            // Packaging / quantity
            $table->unsignedInteger('total_rounds')->nullable();
            $table->unsignedInteger('boxes')->nullable();
            $table->unsignedInteger('rounds_per_box')->nullable();

            // Lot / SKU metadata (optional)
            $table->string('lot_number')->nullable();
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();

            // Condition / origin
            $table->enum('condition', ['factory_new','sealed','opened','mixed','other'])->nullable();
            $table->boolean('reloads')->default(false);
            $table->text('notes')->nullable();

            $table->text('p_1')->nullable();
            $table->text('p_2')->nullable();
            $table->text('p_3')->nullable();
            $table->text('p_4')->nullable();
            $table->text('p_5')->nullable();
            $table->text('p_6')->nullable();
            $table->text('p_7')->nullable();
            $table->text('p_8')->nullable();
            $table->text('p_9')->nullable();
            $table->text('p_10')->nullable();

            $table->timestamps();

            $table->index(['brand', 'caliber']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ammunitions');
    }
};
