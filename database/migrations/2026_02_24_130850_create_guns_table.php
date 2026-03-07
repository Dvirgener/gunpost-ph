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
        Schema::create('guns', function (Blueprint $table) {
            $table->id();

            // Link to listing wrapper
            $table->foreignId('post_id')->unique()->constrained('posts')->cascadeOnDelete();

            /**
             * Identification / naming
             */
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('variant')->nullable();          // Gen/Mark/version/trim
            $table->string('series')->nullable();
            $table->string('country_of_origin')->nullable();

            /**
             * Classification
             */
            $table->enum('platform', ['handgun','rifle','shotgun','pcc','smg','sniper','other'])->nullable();
            $table->string('type')->nullable();            // more specific: 1911, AR-15, etc.
            $table->string('action')->nullable();          // semi-auto, bolt, pump, lever, etc.

            /**
             * Core specs
             */
            $table->string('caliber')->nullable();         // keep flexible
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->decimal('barrel_length', 8, 2)->nullable();
            $table->decimal('overall_length', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('weight', 10, 3)->nullable();  // kg/lb; decide unit convention
            $table->string('weight_unit')->default('kg');  // kg / lb

            /**
             * Materials / finish / ergonomics
             */
            $table->string('frame_material')->nullable();
            $table->string('slide_material')->nullable();
            $table->string('barrel_material')->nullable();
            $table->string('finish')->nullable();
            $table->string('color')->nullable();
            $table->string('grip_type')->nullable();
            $table->string('stock_type')->nullable();      // for rifles/shotguns
            $table->string('handguard_type')->nullable();
            $table->string('rail_type')->nullable();       // picatinny/mlok/keymod/none

            /**
             * Sights / optics
             */
            $table->string('sight_type')->nullable();      // iron, fiber, night sights, etc.
            $table->boolean('optic_ready')->default(false);
            $table->string('optic_mount_pattern')->nullable(); // RMR, MOS, etc.

            /**
             * Barrel / muzzle
             */
            $table->boolean('threaded_barrel')->default(false);
            $table->string('thread_pitch')->nullable();
            $table->boolean('muzzle_device_included')->default(false);
            $table->string('muzzle_device_type')->nullable(); // compensator, flash hider, etc.

            /**
             * Safety / trigger
             */
            $table->string('trigger_type')->nullable();
            $table->decimal('trigger_pull', 6, 2)->nullable();
            $table->string('trigger_pull_unit')->default('lb'); // lb/kg
            $table->boolean('has_manual_safety')->default(false);
            $table->boolean('has_firing_pin_safety')->default(false);

            /**
             * Manufacturer SKU / UPC (optional)
             */
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();

            /**
             * Ownership / condition metadata (keep non-sensitive)
             */
            $table->enum('condition', ['new','like_new','used','refurbished','for_parts'])->nullable();
            $table->unsignedInteger('round_count_estimate')->nullable();
            $table->boolean('has_box')->default(false);
            $table->boolean('has_receipt')->default(false);
            $table->boolean('has_documents')->default(false); // high-level flag only
            $table->text('document_notes')->nullable();

            /**
             * Included items
             */
            $table->unsignedSmallInteger('included_magazines')->nullable();
            $table->text('included_accessories')->nullable();  // case, sling, optic, etc.

            /**
             * Extra notes
             */
            $table->text('notes')->nullable();




            $table->timestamps();
            $table->softDeletes();

            $table->index(['manufacturer', 'model']);
            $table->index(['platform', 'caliber']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guns');
    }
};
