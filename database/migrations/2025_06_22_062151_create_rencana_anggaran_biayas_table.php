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
        Schema::create('rencana_anggaran_biaya', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_request_id')
                ->constrained('project_requests')
                ->onDelete('cascade');

            $table->string('description'); // ubah dari template_id jadi deskripsi bebas
            $table->integer('qty_aset');
            $table->bigInteger('harga_sewa'); // dari template saat dibuat
            $table->bigInteger('total'); // harga_sewa * qty_aset * durasi

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_anggaran_biaya');
    }
};
