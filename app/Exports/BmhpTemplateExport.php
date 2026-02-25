<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BmhpTemplateExport implements FromView
{
    public function view(): View
    {
        return view('exports.bmhp-template', [
            'sampleData' => [
                [
                    'nama_bmhp' => 'Masker Bedah',
                    'satuan' => 'Box',
                    'stok_awal' => 100,
                    'stok_sisa' => 85,
                    'stok_minimum' => 10,
                ],
                [
                    'nama_bmhp' => 'Handsanitizer',
                    'satuan' => 'Botol',
                    'stok_awal' => 50,
                    'stok_sisa' => 30,
                    'stok_minimum' => 5,
                ],
                [
                    'nama_bmhp' => 'Sarung Tangan Medis',
                    'satuan' => 'Box',
                    'stok_awal' => 200,
                    'stok_sisa' => 150,
                    'stok_minimum' => 20,
                ],
            ]
        ]);
    }
}
