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
        Schema::table('project_requests', function (Blueprint $table) {
            if (Schema::hasColumn('project_requests', 'lander_id')) {
                $table->dropConstrainedForeignId('lander_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('project_requests', 'lander_id')) {
                $table->foreignId('lander_id')
                    ->nullable()
                    ->after('asset_ids')
                    ->constrained('landers')
                    ->nullOnDelete();
            }
        });
    }
};
