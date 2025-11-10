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
            $table->integer('user_id')->indexi();
            $table->string('name');
            $table->string('alias_names');
            $table->decimal('total_weight', 6, 2);
            $table->unsignedTinyInteger('food_type')->comment('1: ingredient, 2: products, 3: sets');
            $table->unsignedTinyInteger('usage_unit')->comment('1: package, 2: serving, 3: gram');
            $table->unsignedTinyInteger('unit_type')->comment('1: package, 2: serving, 3: gram');
            $table->decimal('tablespoon', 6, 2)->comment('grams per tablespoon');
            $table->decimal('calory', 8, 1);
            $table->decimal('protein', 6, 2);
            $table->decimal('fat', 6, 2);
            $table->decimal('carbs', 6, 2);
            $table->decimal('salt', 6, 2);
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
