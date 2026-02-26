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
        Schema::create('airsofts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->unique()->constrained('posts')->cascadeOnDelete();

            // Identity
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('series')->nullable();

            // Classification
            $table->enum('platform', ['pistol','rifle','smg','sniper','shotgun','lmg','other'])->nullable();
            $table->enum('power_source', ['aeg','gbb','spring','hpa','co2'])->nullable();
            $table->string('compatibility_platform')->nullable(); // M4/AK/Glock etc.
            $table->string('gearbox_version')->nullable();        // for AEG (optional)

            // Performance (optional)
            $table->unsignedSmallInteger('fps')->nullable();
            $table->decimal('joule', 6, 2)->nullable();

            // Build
            $table->string('color')->nullable();
            $table->string('body_material')->nullable(); // polymer/metal
            $table->boolean('metal_body')->default(false);
            $table->boolean('blowback')->default(false);

            // Battery / gas info (optional)
            $table->string('battery_type')->nullable(); // LiPo/NiMH
            $table->string('battery_connector')->nullable(); // Tamiya/Deans
            $table->string('gas_type')->nullable(); // green gas/CO2 etc.

            // Magazine
            $table->boolean('includes_magazines')->default(false);
            $table->unsignedSmallInteger('magazine_count')->nullable();
            $table->string('magazine_type')->nullable(); // midcap/hicap/realcap

            // Package
            $table->text('package_includes')->nullable();

            // Condition
            $table->enum('condition', ['new','like_new','used','for_parts'])->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['brand', 'model']);
            $table->index(['platform', 'power_source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airsofts');
    }
};
