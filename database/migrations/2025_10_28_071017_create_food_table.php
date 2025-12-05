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
            $table->unsignedTinyInteger('food_type')->comment('1: products, 2: menu, 3: others');
            $table->string('maker')->nullable();
            $table->string('food_unit')->comment('g, ml, 単位');
            $table->unsignedSmallInteger('food_amount');
            $table->decimal('calory', 6, 1)->nullable();
            $table->decimal('protein', 6, 1)->nullable();
            $table->decimal('fat', 6, 1)->nullable();
            $table->decimal('carbs', 6, 1)->nullable();
            $table->decimal('salt', 6, 2)->nullable();
            $table->unsignedTinyInteger('usage_type')
                ->comment('bitwise flags: 1: weight, 2: spoon, 4: volume, 8: package');
            $table->decimal('gram_per_food_unit', 6, 1)->nullable();
            $table->decimal('amount_per_tablespoon', 6, 1)->nullable();
            $table->decimal('ml_per_food_unit', 6, 1)->nullable();
            $table->decimal('amount_per_package', 6, 1)->nullable();
            $table->string('package_unit');
            $table->string('search_string');
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
