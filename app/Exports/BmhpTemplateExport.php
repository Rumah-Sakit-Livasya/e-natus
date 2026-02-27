<?php

namespace App\Exports;

use App\Models\Bmhp;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BmhpTemplateExport implements FromView
{
    public function view(): View
    {
        $rows = Bmhp::query()
            ->orderBy('id')
            ->get()
            ->map(function (Bmhp $bmhp): array {
                $satuan = (string) ($bmhp->satuan ?? 'pcs');
                $pcsPerUnit = $bmhp->pcs_per_unit;

                if (strtolower($satuan) === 'pcs') {
                    $pcsPerUnit = 1;
                }

                return [
                    'id' => $bmhp->id,
                    'nama_bmhp' => $bmhp->name,
                    'satuan' => $satuan,
                    'pcs_per_unit' => $pcsPerUnit,
                    'stok_awal' => (int) $bmhp->stok_awal,
                    'stok_sisa' => (int) $bmhp->stok_sisa,
                    'stok_minimum' => (int) $bmhp->min_stok,
                ];
            })
            ->toArray();

        if (empty($rows)) {
            $rows = [[
                'id' => '',
                'nama_bmhp' => 'Masker Bedah',
                'satuan' => 'pcs',
                'pcs_per_unit' => 1,
                'stok_awal' => 100,
                'stok_sisa' => 100,
                'stok_minimum' => 10,
            ]];
        }

        return view('exports.bmhp-template', [
            'rows' => $rows,
        ]);
    }
}
