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
        Schema::create('realisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('procurement_id')->nullable()->constrained('procurements')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->enum('status', ['SELESAI', 'TIDAK']);
            $table->decimal('harga_realisasi', 15, 2);
            $table->decimal('qty_realisasi', 10, 2);
            $table->string('satuan');
            $table->decimal('jumlah_realisasi', 15, 2)->storedAs('harga_realisasi * qty_realisasi');
            $table->decimal('persentase_hemat', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisations');
    }
};
