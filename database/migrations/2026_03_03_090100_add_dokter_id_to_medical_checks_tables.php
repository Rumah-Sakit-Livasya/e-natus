<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekg_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });

        Schema::table('spirometry_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });

        Schema::table('rontgen_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });

        Schema::table('usg_abdomen_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });

        Schema::table('usg_mammae_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });

        Schema::table('treadmill_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });

        Schema::table('lab_checks', function (Blueprint $table) {
            $table->foreignId('dokter_id')->nullable()->after('participant_id')->constrained('dokters')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });

        Schema::table('treadmill_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });

        Schema::table('usg_mammae_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });

        Schema::table('usg_abdomen_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });

        Schema::table('rontgen_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });

        Schema::table('spirometry_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });

        Schema::table('ekg_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_id');
        });
    }
};
