<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rab_closing_bmhp_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_closing_id')->constrained('rab_closings')->cascadeOnDelete();
            $table->foreignId('bmhp_id')->nullable()->constrained('bmhp')->nullOnDelete();
            $table->string('name');
            $table->string('satuan')->nullable();
            $table->integer('jumlah_rencana')->default(0);
            $table->integer('harga_satuan')->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_closing_bmhp_items');
    }
};
