<?php

use App\Models\RabClosing;
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
        Schema::create('rab_closing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RabClosing::class)->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->integer('qty');
            $table->bigInteger('harga_satuan');
            $table->bigInteger('total_anggaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_closing_items');
    }
};
