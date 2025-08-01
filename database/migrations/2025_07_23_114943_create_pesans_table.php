<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesan', function (Blueprint $table) {
            $table->id(); // int(11), primary, auto_increment
            $table->timestamp('tanggal')->default(DB::raw('CURRENT_TIMESTAMP')); // Default ke waktu sekarang
            $table->string('info_app', 250)->nullable();
            $table->string('nama', 250)->nullable();
            $table->string('nomor', 250)->nullable();
            $table->string('id_pesan', 250)->nullable();
            $table->text('pesan')->nullable();
            $table->text('balasan')->nullable();
            $table->string('status_balasan', 250)->nullable();
            $table->string('message', 2000)->nullable();
            $table->text('data')->nullable();
            $table->string('status', 250)->nullable();

            // Kita tidak menggunakan $table->timestamps() karena Anda punya kolom `tanggal` sendiri
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan');
    }
};
