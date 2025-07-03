<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusPengajuanEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_danas', function (Blueprint $table) {
            $table->id();

            // Relasi ke Proyek dan Pengaju
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('User yang mengajukan')->constrained('users');

            // Detail Pengajuan
            $table->string('tujuan');
            $table->decimal('jumlah_diajukan', 15, 2);
            $table->date('tanggal_pengajuan');
            $table->string('status')->default(StatusPengajuanEnum::DIAJUKAN->value);

            // Detail Approval
            $table->text('catatan_approval')->nullable();
            $table->foreignId('approved_by_id')->nullable()->comment('User yang menyetujui/menolak')->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // Detail Pencairan
            $table->string('bukti_transfer')->nullable();
            $table->timestamp('dicairkan_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_danas');
    }
};
