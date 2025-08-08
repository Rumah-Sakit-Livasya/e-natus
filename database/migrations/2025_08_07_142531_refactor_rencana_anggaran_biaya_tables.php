<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nonaktifkan sementara foreign key checks
        Schema::disableForeignKeyConstraints();

        // Hapus tabel lama (yang sebelumnya gagal)
        Schema::dropIfExists('rencana_anggaran_biaya');

        // Buat dua tabel baru
        Schema::create('rab_operasional_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->integer('qty_aset')->default(1);
            $table->decimal('harga_sewa', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('rab_fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->integer('qty_aset')->default(1);
            $table->decimal('harga_sewa', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });

        // Aktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rab_operasional_items');
        Schema::dropIfExists('rab_fee_items');
        // (kode untuk membuat kembali tabel 'rencana_anggaran_biaya' dari jawaban sebelumnya)
        Schema::enableForeignKeyConstraints();
    }
};
