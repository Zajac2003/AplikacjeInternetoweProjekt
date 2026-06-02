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
        Schema::create('bill_splits', function (Blueprint $table) {
            $table->id();
            // Powiązanie z rachunkiem
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            // Powiązanie z dłużnikiem (użytkownikiem)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('amount', 10, 2); // Kwota jaką ta osoba ma oddać
            $table->boolean('is_paid')->default(false); // Czy już spłacone?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_splits');
    }
};
