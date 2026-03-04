<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usg_abdomen_checks', function (Blueprint $table) {
            $table->string('gambar_hasil_usg_2')->nullable()->after('gambar_hasil_usg');
            $table->string('gambar_hasil_usg_3')->nullable()->after('gambar_hasil_usg_2');
            $table->string('gambar_hasil_usg_4')->nullable()->after('gambar_hasil_usg_3');
        });
    }

    public function down(): void
    {
        Schema::table('usg_abdomen_checks', function (Blueprint $table) {
            $table->dropColumn([
                'gambar_hasil_usg_2',
                'gambar_hasil_usg_3',
                'gambar_hasil_usg_4',
            ]);
        });
    }
};
