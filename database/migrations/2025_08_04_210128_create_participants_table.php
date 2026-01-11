<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();

            // INI YANG PALING PENTING: RELASI KE PROJECT
            $table->foreignId('project_request_id')
                ->constrained('project_requests')
                ->cascadeOnDelete(); // Jika project dihapus, pesertanya juga ikut terhapus

            // Data demografi peserta
            $table->string('name');
            $table->string('employee_code')->nullable()->index();
            $table->string('department')->nullable();
            $table->date('date_of_birth');
            $table->text('address');
            $table->string('gender');
            $table->string('marital_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
