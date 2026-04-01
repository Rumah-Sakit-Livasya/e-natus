<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Deduplicate existing assets by `code`.
        // Keep a canonical record per code, repoint JSON references in project_requests.asset_ids,
        // then delete duplicates.
        $duplicateCodes = DB::table('aset')
            ->select('code')
            ->whereNull('deleted_at')
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('code');

        foreach ($duplicateCodes as $code) {
            $keepId = DB::table('aset')
                ->where('code', $code)
                ->whereNull('deleted_at')
                ->min('id');

            if (! $keepId) {
                continue;
            }

            $duplicateIds = DB::table('aset')
                ->where('code', $code)
                ->whereNull('deleted_at')
                ->where('id', '!=', $keepId)
                ->pluck('id')
                ->all();

            if (empty($duplicateIds)) {
                continue;
            }

            // Update JSON references in project_requests.asset_ids
            $projectRows = DB::table('project_requests')
                ->select(['id', 'asset_ids'])
                ->whereNotNull('asset_ids')
                ->get();

            foreach ($projectRows as $row) {
                $assetIds = json_decode((string) $row->asset_ids, true);
                if (! is_array($assetIds) || empty($assetIds)) {
                    continue;
                }

                $changed = false;
                $newIds = [];

                foreach ($assetIds as $assetId) {
                    $normalizedId = is_numeric($assetId) ? (int) $assetId : $assetId;

                    if (is_int($normalizedId) && in_array($normalizedId, $duplicateIds, true)) {
                        $newIds[] = (string) $keepId;
                        $changed = true;
                        continue;
                    }

                    // Keep original as string (existing codebase uses string IDs in JSON contains)
                    $newIds[] = is_int($normalizedId) ? (string) $normalizedId : (string) $assetId;
                }

                // Ensure uniqueness
                $newIds = array_values(array_unique($newIds));

                if ($changed) {
                    DB::table('project_requests')
                        ->where('id', $row->id)
                        ->update(['asset_ids' => json_encode($newIds)]);
                }
            }

            // Delete duplicate assets (hard delete)
            DB::table('aset')->whereIn('id', $duplicateIds)->delete();
        }

        // 2) Prevent future duplicates for active records.
        // Using (code, deleted_at) allows reusing codes after soft delete.
        Schema::table('aset', function (Blueprint $table) {
            $table->unique(['code', 'deleted_at'], 'aset_code_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropUnique('aset_code_deleted_at_unique');
        });
    }
};
