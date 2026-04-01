<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryMap = [
            'ALKES' => ['name' => 'Alat Kesehatan', 'code' => 'ALKES'],
            'NONALKES' => ['name' => 'Non Alat Kesehatan', 'code' => 'NONALKES'],
            'BMHP' => ['name' => 'Bahan Medis Habis Pakai', 'code' => 'BMHP'],
        ];

        $categories = [];
        foreach ($categoryMap as $code => $payload) {
            $categories[$code] = Category::query()->firstOrCreate(
                ['code' => $payload['code']],
                ['name' => $payload['name']]
            );
        }

        $templates = [
            // ALKES
            ['category_code' => 'ALKES', 'name' => 'Tensimeter', 'code' => 'TNS'],
            ['category_code' => 'ALKES', 'name' => 'Stetoskop', 'code' => 'STT'],
            ['category_code' => 'ALKES', 'name' => 'ECG', 'code' => 'ECG'],
            // NON-ALKES
            ['category_code' => 'NONALKES', 'name' => 'Meja', 'code' => 'MEJA'],
            ['category_code' => 'NONALKES', 'name' => 'Kursi', 'code' => 'KRS'],
        ];

        foreach ($templates as $template) {
            $category = $categories[$template['category_code']] ?? null;
            if (! $category) {
                continue;
            }

            Template::query()->firstOrCreate(
                ['code' => $template['code']],
                [
                    'category_id' => $category->id,
                    'name' => $template['name'],
                ]
            );
        }
    }
}
