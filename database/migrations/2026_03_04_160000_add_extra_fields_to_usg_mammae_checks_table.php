<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usg_mammae_checks', function (Blueprint $table) {
            $table->string('nik_no_pekerja')->nullable()->after('instansi');
            $table->string('gambar_hasil_usg_2')->nullable()->after('gambar_hasil_usg');
            $table->string('gambar_hasil_usg_3')->nullable()->after('gambar_hasil_usg_2');
            $table->string('gambar_hasil_usg_4')->nullable()->after('gambar_hasil_usg_3');
            $table->string('gambar_hasil_usg_5')->nullable()->after('gambar_hasil_usg_4');
            $table->string('gambar_hasil_usg_6')->nullable()->after('gambar_hasil_usg_5');
        });
    }

    public function down(): void
    {
        Schema::table('usg_mammae_checks', function (Blueprint $table) {
            $table->dropColumn([
                'nik_no_pekerja',
                'gambar_hasil_usg_2',
                'gambar_hasil_usg_3',
                'gambar_hasil_usg_4',
                'gambar_hasil_usg_5',
                'gambar_hasil_usg_6',
            ]);
        });
    }
};
