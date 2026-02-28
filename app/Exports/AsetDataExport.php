<?php

namespace App\Exports;

use App\Models\Aset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AsetDataExport implements FromView
{
    public function view(): View
    {
        $rows = Aset::query()
            ->with(['template', 'lander'])
            ->orderBy('id')
            ->get()
            ->map(function (Aset $aset): array {
                return [
                    'id' => $aset->id,
                    'template_name' => $aset->template?->name ?? '',
                    'lander_name' => $aset->lander?->name ?? '',
                    'custom_name' => $aset->custom_name,
                    'type' => $aset->type,
                    'serial_number' => $aset->serial_number,
                    'code' => $aset->code,
                    'condition' => $aset->condition,
                    'brand' => $aset->brand,
                    'purchase_year' => $aset->purchase_year,
                    'tarif' => $aset->tarif,
                    'harga_sewa' => $aset->harga_sewa,
                    'satuan' => $aset->satuan,
                    'status' => $aset->status,
                ];
            })
            ->toArray();

        if (empty($rows)) {
            $rows = [[
                'id' => '',
                'template_name' => '',
                'lander_name' => '',
                'custom_name' => '',
                'type' => '',
                'serial_number' => '',
                'code' => '',
                'condition' => '',
                'brand' => '',
                'purchase_year' => '',
                'tarif' => '',
                'harga_sewa' => '',
                'satuan' => '',
                'status' => 'available',
            ]];
        }

        return view('exports.aset-data', [
            'rows' => $rows,
        ]);
    }
}

