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
        // 1) Deduplicate existing rows by `code`.
        // Keep the smallest `id` as canonical, move references to it, then hard-delete duplicates.
        $duplicateCodes = DB::table('landers')
            ->select('code')
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('code');

        foreach ($duplicateCodes as $code) {
            $keepId = DB::table('landers')->where('code', $code)->min('id');
            $duplicateIds = DB::table('landers')
                ->where('code', $code)
                ->where('id', '!=', $keepId)
                ->pluck('id');

            if ($duplicateIds->isEmpty()) {
                continue;
            }

            DB::table('aset')
                ->whereIn('lander_id', $duplicateIds)
                ->update(['lander_id' => $keepId]);

            DB::table('asset_receipt_items')
                ->whereIn('lander_id', $duplicateIds)
                ->update(['lander_id' => $keepId]);

            DB::table('landers')->whereIn('id', $duplicateIds)->delete();
        }

        // 2) Prevent future duplicates.
        Schema::table('landers', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landers', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
    }
};
