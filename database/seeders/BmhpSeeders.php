<?php

namespace Database\Seeders;

use App\Models\Bmhp;
use Illuminate\Database\Seeder;

class BmhpSeeders extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Sarung Tangan', 'satuan' => 'box', 'pcs_per_unit' => 100, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Masker Medis', 'satuan' => 'box', 'pcs_per_unit' => 50, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Masker N95', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Alkohol Swab', 'satuan' => 'box', 'pcs_per_unit' => 100, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Plester', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Kapas', 'satuan' => 'pack', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Spuit 3 ml', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Spuit 5 ml', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Jarum Vacutainer', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Tabung EDTA', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Tabung Serum', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Lancet', 'satuan' => 'box', 'pcs_per_unit' => 100, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Strip Gula Darah', 'satuan' => 'box', 'pcs_per_unit' => 50, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Urine Container', 'satuan' => 'pcs', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
            ['name' => 'Hand Sanitizer', 'satuan' => 'bottle', 'pcs_per_unit' => 1, 'stok_sisa' => 0, 'min_stok' => 0],
        ];

        foreach ($items as $item) {
            $bmhp = Bmhp::withTrashed()->firstOrNew(['name' => $item['name']]);
            $bmhp->fill([
                'satuan' => $item['satuan'],
                'pcs_per_unit' => $item['pcs_per_unit'],
                'stok_sisa' => $item['stok_sisa'],
                'min_stok' => $item['min_stok'],
            ]);
            $bmhp->deleted_at = null;
            $bmhp->save();
        }
    }
}
