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
            $table->integer('food_id');
            $table->string('name');
            $table->unsignedTinyInteger('usage_unit');
            $table->decimal('amount', 6, 2);
            $table->decimal('calory', 8, 1);
            $table->decimal('protein', 6, 2);
            $table->decimal('fat', 6, 2);
            $table->decimal('carbs', 6, 2);
            $table->decimal('salt', 6, 2);
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
