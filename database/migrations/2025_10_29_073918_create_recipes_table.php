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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('name');
            $table->decimal('servings', 5, 1);
            $table->decimal('total_weight', 5, 1);
            $table->unsignedSmallInteger('calory');
            $table->decimal('protein', 6, 1);
            $table->decimal('fat', 6, 1);
            $table->decimal('carbs', 6, 1);
            $table->decimal('salt', 6, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
