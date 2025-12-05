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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('regular_id');
            $table->integer('food_id');
            $table->string('name');
            $table->decimal('amount', 6, 2);
            $table->string('unit');
            $table->decimal('calory', 6, 1);
            $table->decimal('protein', 6, 2);
            $table->decimal('fat', 6, 2);
            $table->decimal('carbs', 6, 2);
            $table->decimal('salt', 6, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
