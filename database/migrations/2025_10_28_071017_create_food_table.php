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
        Schema::create('food', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('name');
            $table->string('alias_names')->nullable();
            $table->unsignedTinyInteger('food_type')->comment('1: ingredient, 2: products, 3: sets');
            $table->unsignedTinyInteger('food_unit')->comment('1: gram, 2: ml, 3: serving');
            $table->unsignedSmallInteger('food_amount');
            $table->unsignedSmallInteger('calory');
            $table->decimal('protein', 6, 1)->nullable();
            $table->decimal('fat', 6, 1)->nullable();
            $table->decimal('carbs', 6, 1)->nullable();
            $table->decimal('salt', 6, 2)->nullable();
            $table->unsignedTinyInteger('usage_type')
                ->comment('bitwise flags: 1: weight, 2: spoon, 4: volume, 8: package');
            $table->decimal('gram_per_food_unit', 6, 1)->nullable();
            $table->decimal('gram_per_tablespoon', 6, 1)->nullable();
            $table->decimal('ml_per_food_unit', 6, 1)->nullable();
            $table->decimal('amount_per_package', 6, 1)->nullable();
            $table->string('package_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
