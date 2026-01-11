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
        Schema::create('realisation_rab_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('rencana_anggaran_biaya_id')->constrained('rencana_anggaran_biaya')->onDelete('cascade');

            $table->string('status')->default('draft'); // draft, approved, rejected, done
            $table->string('description')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('harga')->nullable();
            $table->bigInteger('total')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_realisasi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisation_rab_items');
    }
};
