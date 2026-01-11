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
        Schema::create('project_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained('project_requests')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('lokasi_maps');
            $table->string('foto')->nullable(); // path ke foto absen
            $table->timestamps();

            $table->unique(['project_request_id', 'employee_id', 'tanggal'], 'unique_absen_per_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_attendances');
    }
};
