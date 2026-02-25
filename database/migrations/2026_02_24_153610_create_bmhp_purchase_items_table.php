<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bmhp_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bmhp_purchase_id')->constrained('bmhp_purchases')->cascadeOnDelete();
            $table->foreignId('bmhp_id')->constrained('bmhp')->cascadeOnDelete();

            $table->string('purchase_type', 10); // unit|pcs
            $table->integer('qty')->default(0);
            $table->integer('pcs_per_unit_snapshot')->nullable();
            $table->integer('total_pcs')->default(0);

            $table->integer('harga')->default(0); // sesuai purchase_type
            $table->integer('subtotal')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bmhp_purchase_items');
    }
};
