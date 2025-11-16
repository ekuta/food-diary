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
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->date('date');
            $table->boolean('breakfirst');
            $table->boolean('lunch');
            $table->boolean('dinner');
            $table->unsignedSmallInteger('calory');
            $table->decimal('protein', 6, 1);
            $table->decimal('fat', 6, 1);
            $table->decimal('carbs', 6, 1);
            $table->decimal('salt', 6, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
