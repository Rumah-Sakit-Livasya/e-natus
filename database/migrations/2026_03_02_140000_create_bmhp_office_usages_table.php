<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bmhp_office_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bmhp_id')->constrained('bmhp')->cascadeOnDelete();
            $table->unsignedInteger('qty_used');
            $table->date('used_at');
            $table->string('location')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bmhp_office_usages');
    }
};
