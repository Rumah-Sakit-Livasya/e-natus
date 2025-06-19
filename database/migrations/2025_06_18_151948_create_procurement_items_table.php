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
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained()->onDelete('cascade');
            $table->string('nama_barang');
            $table->string('unit');
            $table->decimal('harga_pengajuan', 15, 2);
            $table->decimal('qty_pengajuan', 10, 2);
            $table->string('satuan');
            $table->decimal('jumlah_pengajuan', 15, 2)->storedAs('harga_pengajuan * qty_pengajuan');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};
