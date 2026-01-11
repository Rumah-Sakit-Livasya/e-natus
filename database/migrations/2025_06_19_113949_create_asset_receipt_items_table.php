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
        Schema::create('asset_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->foreignId('lander_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_name', 50);
            $table->string('code', 50);
            $table->string('condition', 50)->default('baik');
            $table->string('brand', 50)->nullable();
            $table->string('purchase_year', 4);
            $table->integer('tarif')->nullable();
            $table->string('satuan')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_receipt_items');
    }
};
