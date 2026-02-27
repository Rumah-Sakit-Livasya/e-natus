<?php

namespace App\Imports;

use App\Models\Bmhp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BmhpImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $id = $row['id'] ?? null;
        $id = ($id === '' || $id === null) ? null : (int) $id;

        $name = trim((string) ($row['nama_bmhp'] ?? $row['name'] ?? ''));
        $name = $name !== '' ? $name : null;

        $satuan = trim((string) ($row['satuan'] ?? ''));
        $satuan = $satuan !== '' ? $satuan : 'pcs';

        $pcsPerUnit = $row['pcs_per_unit'] ?? $row['isi_per_satuan_pcs'] ?? null;
        $pcsPerUnit = ($pcsPerUnit === '' || $pcsPerUnit === null) ? null : (int) $pcsPerUnit;

        // Jika satuan pcs, paksa isi per satuan = 1 agar konsisten.
        if (strtolower($satuan) === 'pcs') {
            $pcsPerUnit = 1;
        }

        $payload = [
            'name' => $name,
            'satuan' => $satuan,
            'pcs_per_unit' => $pcsPerUnit,
            'stok_awal' => $row['stok_awal'] ?? $row['stok_awal'] ?? 0,
            'stok_sisa' => $row['stok_sisa'] ?? $row['stok_sisa'] ?? 0,
            'min_stok' => $row['stok_minimum'] ?? $row['min_stok'] ?? 0,
        ];

        // Upsert by ID (preferred), fallback by name for backward compatibility.
        $existing = null;
        if ($id) {
            $existing = Bmhp::withTrashed()->find($id);
        }
        if (! $existing && $name) {
            $existing = Bmhp::withTrashed()->where('name', $name)->first();
        }

        if ($existing) {
            if (method_exists($existing, 'trashed') && $existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($payload);

            return $existing;
        }

        return new Bmhp($payload);
    }

    public function rules(): array
    {
        return [
            'id' => 'sometimes|nullable|integer|exists:bmhp,id',
            'nama_bmhp' => 'nullable|string|max:255|required_without:name',
            'name' => 'sometimes|nullable|string|max:255|required_without:nama_bmhp',
            'satuan' => 'nullable|string|max:50',
            'pcs_per_unit' => 'sometimes|nullable|integer|min:1',
            'isi_per_satuan_pcs' => 'sometimes|nullable|integer|min:1',
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
            'id.exists' => 'ID BMHP tidak ditemukan di sistem',
            'satuan.max' => 'Satuan maksimal 50 karakter',
            'pcs_per_unit.min' => 'Isi per satuan (pcs) minimal 1',
            'isi_per_satuan_pcs.min' => 'Isi per satuan (pcs) minimal 1',
            'stok_awal.min' => 'Stok awal tidak boleh negatif',
            'stok_sisa.min' => 'Stok sisa tidak boleh negatif',
            'stok_minimum.min' => 'Stok minimum tidak boleh negatif',
        ];
    }
}
