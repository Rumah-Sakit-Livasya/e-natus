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
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('sdm_ids')->nullable();
            $table->json('employee_ids')->nullable();
            $table->json('asset_ids')->nullable(); // Store multiple asset IDs as JSON array

            $table->string('name');
            $table->string('pic');
            $table->integer('jumlah');
            $table->string('lokasi');
            $table->string('status')->default('pending');
            $table->string('code')->nullable();
            $table->date('start_period');
            $table->date('end_period');
            $table->text('keterangan')->nullable();
            $table->bigInteger('nilai_invoice');
            $table->date('due_date');
            $table->string('status_pembayaran')->default('unpaid');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
