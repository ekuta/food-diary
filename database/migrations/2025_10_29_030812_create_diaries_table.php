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
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->date('date')->index();
            $table->unsignedTinyInteger('meal_type')->comment('1: breakfirst, 2: lunch, 3: dinner, 4: snack');
            $table->integer('recipe_id');
            $table->integer('food_id')->comment('0: recipe, max int: temporary food');
            $table->decimal('servings', 6, 1)->comment('for recipie');
            $table->string('name');
            $table->decimal('amount', 6, 2);
            $table->unsignedTinyInteger('unit');
            $table->decimal('calory', 6, 1);
            $table->decimal('protein', 6, 2);
            $table->decimal('fat', 6, 2);
            $table->decimal('carbs', 6, 2);
            $table->decimal('salt', 6, 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};
