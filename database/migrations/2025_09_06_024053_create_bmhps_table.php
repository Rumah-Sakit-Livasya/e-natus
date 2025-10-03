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
        Schema::create('bmhp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('satuan')->nullable();
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_sisa')->default(0);
            $table->integer('harga_satuan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_bmhp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bmhp_id')->constrained('bmhp')->cascadeOnDelete();
            $table->integer('jumlah_rencana')->default(0);
            $table->integer('harga_satuan')->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bmhp_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bmhp_id')->constrained('bmhp')->cascadeOnDelete();
            $table->integer('stok_fisik')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys before dropping tables
        Schema::table('project_bmhp', function (Blueprint $table) {
            $table->dropForeign(['project_request_id']);
            $table->dropForeign(['bmhp_id']);
        });

        Schema::table('bmhp_stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['bmhp_id']);
        });

        Schema::dropIfExists('project_bmhp');
        Schema::dropIfExists('bmhp_stock_opnames');
        Schema::dropIfExists('bmhp');
    }
};
