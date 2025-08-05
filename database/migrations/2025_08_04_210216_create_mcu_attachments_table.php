<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_result_id')->constrained('mcu_results')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('description')->nullable(); // Misal: "Foto Thorax", "Hasil EKG"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_attachments');
    }
};
