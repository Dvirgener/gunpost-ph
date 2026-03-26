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
        Schema::create('others', function (Blueprint $table) {
            $table->id();

            // Link to listing wrapper (one-to-one)
            $table->foreignId('post_id')
                ->unique()
                ->constrained('posts')
                ->cascadeOnDelete();

            /**
             * Classification
             */
            $table->string('weapon_type')->nullable(); // e.g., karambit, bowie, katana, kukri, balisong (if applicable)

            $table->string('subcategory')->nullable(); // e.g., karambit, bowie, katana, kukri, balisong (if applicable)
            $table->string('intended_use')->nullable(); // utility, training, display, collection, outdoors, etc.

            /**
             * Identity
             */
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('variant')->nullable();
            $table->string('country_of_origin')->nullable();

            /**
             * Blade / Head specs (nullable for non-bladed items like batons)
             */
            $table->string('blade_type')->nullable();       // fixed, folding, serrated, tanto, drop point, etc.
            $table->string('edge_type')->nullable();        // plain, serrated, combo
            $table->string('steel_type')->nullable();       // 440C, D2, VG-10, carbon steel, etc.
            $table->string('finish')->nullable();           // stonewash, satin, coated, etc.
            $table->boolean('full_tang')->nullable();       // null if unknown / not applicable

            /**
             * Dimensions (store as decimals; keep unit columns for clarity)
             */
            $table->decimal('overall_length', 8, 2)->nullable();
            $table->decimal('blade_length', 8, 2)->nullable();   // knife/sword/machete
            $table->decimal('head_length', 8, 2)->nullable();    // axe/tomahawk head length
            $table->decimal('handle_length', 8, 2)->nullable();
            $table->string('length_unit')->default('cm');        // cm or in

            /**
             * Weight
             */
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('weight_unit')->default('kg');        // kg or lb

            /**
             * Handle / grip
             */
            $table->string('handle_material')->nullable(); // g10, micarta, wood, rubber, polymer, etc.
            $table->string('handle_color')->nullable();
            $table->string('grip_texture')->nullable();    // smooth, checkered, wrapped, etc.

            /**
             * Mechanism & lock (for folding knives)
             */
            $table->boolean('is_folding')->default(false);
            $table->string('opening_mechanism')->nullable(); // manual, assisted, etc.
            $table->string('lock_type')->nullable();         // liner lock, frame lock, back lock, etc.

            /**
             * Sheath / scabbard / holster
             */
            $table->boolean('includes_sheath')->default(false);
            $table->string('sheath_type')->nullable();       // kydex, leather, nylon, scabbard, etc.
            $table->string('carry_type')->nullable();        // belt, molle, pocket clip, etc.

            /**
             * Condition & packaging
             */
            $table->enum('condition', ['new', 'like_new', 'used', 'refurbished', 'for_parts'])
                ->nullable();

            $table->boolean('has_box')->default(false);
            $table->boolean('has_receipt')->default(false);

            /**
             * Included items / notes
             */
            $table->text('package_includes')->nullable(); // sharpening tool, extra sheath, etc.
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['weapon_type', 'brand']);
            $table->index(['is_folding', 'condition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('others');
    }
};
