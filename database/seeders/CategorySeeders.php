<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Alat Kesehatan', 'code' => 'ALKES'],
            ['name' => 'Non Alat Kesehatan', 'code' => 'NONALKES'],
            ['name' => 'Bahan Medis Habis Pakai', 'code' => 'BMHP'],
        ];

        \App\Models\Category::insert($categories);
    }
}
