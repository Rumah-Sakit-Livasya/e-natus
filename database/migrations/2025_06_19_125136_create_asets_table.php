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
        Schema::create('aset', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')->constrained()->onDelete('cascade');

            $table->unsignedBigInteger('lander_id')->nullable();
            $table->foreign('lander_id')->references('id')->on('landers')->onDelete('set null');

            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete(); // Tambahan dari receipt
            $table->foreignId('asset_receipt_item_id')->nullable()->constrained()->nullOnDelete(); // Link ke item penerimaan

            $table->string('custom_name', 50);
            $table->string('code', 50); // disarankan auto-generate + unique
            $table->string('condition', 50)->default('baik');
            $table->string('brand', 50)->nullable();
            $table->string('purchase_year', 4)->nullable();
            $table->integer('tarif')->nullable();
            $table->string('satuan')->nullable();
            $table->integer('index')->nullable(); // dipertahankan
            $table->string('image', 50)->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
};
