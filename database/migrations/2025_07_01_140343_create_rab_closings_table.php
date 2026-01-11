<?php

use App\Models\ProjectRequest;
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
        Schema::create('rab_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProjectRequest::class)->constrained()->cascadeOnDelete()->unique();
            $table->date('closing_date');
            $table->bigInteger('total_anggaran')->default(0);
            $table->bigInteger('total_realisasi')->default(0);
            $table->bigInteger('selisih')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status')->default('draft'); // Tambahkan kolom ini: 'draft', 'final'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_closings');
    }
};
