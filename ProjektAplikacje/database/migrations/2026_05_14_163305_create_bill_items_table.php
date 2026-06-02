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
        // Twoja oryginalna tabela pozycji z paragonu
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // DODAJEMY TO NA 5.0: Tabela łącząca pozycję z użytkownikami (kto za co odpowiada)
        Schema::create('bill_item_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_item_user');
        Schema::dropIfExists('bill_items');
    }
};
