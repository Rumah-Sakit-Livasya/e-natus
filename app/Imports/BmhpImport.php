<?php

namespace App\Imports;

use App\Models\Bmhp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class BmhpImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Bmhp([
            'name' => $row['nama_bmhp'] ?? $row['name'] ?? null,
            'satuan' => $row['satuan'] ?? null,
            'stok_awal' => $row['stok_awal'] ?? $row['stok_awal'] ?? 0,
            'stok_sisa' => $row['stok_sisa'] ?? $row['stok_sisa'] ?? 0,
            'min_stok' => $row['stok_minimum'] ?? $row['min_stok'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_bmhp' => 'required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'stok_awal' => 'nullable|integer|min:0',
            'stok_sisa' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'min_stok' => 'sometimes|nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_bmhp.required' => 'Nama BMHP wajib diisi',
            'nama_bmhp.max' => 'Nama BMHP maksimal 255 karakter',
            'satuan.max' => 'Satuan maksimal 50 karakter',
            'stok_awal.min' => 'Stok awal tidak boleh negatif',
            'stok_sisa.min' => 'Stok sisa tidak boleh negatif',
            'stok_minimum.min' => 'Stok minimum tidak boleh negatif',
        ];
    }
}
