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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->unique()->constrained('posts')->cascadeOnDelete();

            // Identity
            $table->string('category')->nullable(); // optic, holster, light, sling, bag, etc.
            $table->string('brand')->nullable();
            $table->string('model')->nullable();

            // Compatibility / fitment
            $table->string('compatible_with')->nullable(); // Glock 19, Picatinny, M-LOK, etc.
            $table->string('mount_type')->nullable();      // picatinny/mlok/keymod/none
            $table->string('size')->nullable();           // S/M/L or dimensions
            $table->string('color')->nullable();
            $table->string('material')->nullable();

            // Commercial identifiers (optional)
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();

            // Package & condition
            $table->text('package_includes')->nullable();
            $table->enum('condition', ['new','like_new','used','for_parts'])->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['category', 'brand']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
