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
        Schema::create('rab_closing_fee_petugas_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_closing_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_closing_fee_petugas_items');
    }
};
