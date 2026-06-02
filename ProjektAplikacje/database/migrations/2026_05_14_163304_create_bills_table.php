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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            // TE LINIE SĄ NIEZBĘDNE:
            $table->foreignId('group_id')->constrained()->onDelete('cascade'); // Z jaką grupą jest ten rachunek
            $table->foreignId('payer_id')->constrained('users')->onDelete('cascade'); // Kto za to zapłacił
            $table->string('description'); // Nazwa wydatku (np. Obiad, Paliwo)
            $table->decimal('amount', 10, 2); // Kwota rachunku
            $table->date('date'); // Kiedy to było
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
