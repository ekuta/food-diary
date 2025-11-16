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
        Schema::create('mext_food', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // 成分識別子
            $table->unsignedSmallInteger('calory'); // ENERC_KCAL
            $table->decimal('protein', 6, 1);       // PROT-
            $table->decimal('fat', 6, 1);           // FAT-
            $table->decimal('carbs', 6, 1);         // CHOCDF-
            $table->decimal('salt', 6, 2);          // NACL_EQ 括弧ありは推定値
            $table->string('memo');                 // MEMO (独自で追加)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mext_food');
    }
};
