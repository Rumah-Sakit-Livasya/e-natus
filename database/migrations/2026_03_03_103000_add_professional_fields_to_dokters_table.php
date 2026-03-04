<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->string('no_str')->nullable()->after('spesialisasi');
            $table->string('no_sip')->nullable()->after('no_str');
            $table->string('scan_str_sip')->nullable()->after('no_sip');
        });
    }

    public function down(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->dropColumn(['no_str', 'no_sip', 'scan_str_sip']);
        });
    }
};
