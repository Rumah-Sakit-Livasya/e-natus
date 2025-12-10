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
            // Level 1 Approval Fields
            $table->enum('approval_level_1_status', ['pending', 'approved', 'rejected'])
                ->nullable()
                ->after('status');
            $table->unsignedBigInteger('approval_level_1_by')->nullable()->after('approval_level_1_status');
            $table->timestamp('approval_level_1_at')->nullable()->after('approval_level_1_by');
            $table->text('approval_level_1_notes')->nullable()->after('approval_level_1_at');

            // Level 2 Approval Fields
            $table->enum('approval_level_2_status', ['pending', 'approved', 'rejected'])
                ->nullable()
                ->after('approval_level_1_notes');
            $table->unsignedBigInteger('approval_level_2_by')->nullable()->after('approval_level_2_status');
            $table->timestamp('approval_level_2_at')->nullable()->after('approval_level_2_by');
            $table->text('approval_level_2_notes')->nullable()->after('approval_level_2_at');

            // Foreign Keys
            $table->foreign('approval_level_1_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approval_level_2_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['approval_level_1_by']);
            $table->dropForeign(['approval_level_2_by']);

            // Drop columns
            $table->dropColumn([
                'approval_level_1_status',
                'approval_level_1_by',
                'approval_level_1_at',
                'approval_level_1_notes',
                'approval_level_2_status',
                'approval_level_2_by',
                'approval_level_2_at',
                'approval_level_2_notes',
            ]);
        });
    }
};
