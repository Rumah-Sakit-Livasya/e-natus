<?php

namespace Database\Seeders;

use App\Models\VendorRental;
use Illuminate\Database\Seeder;

class VendorRentalSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rentals = [
            ['name' => 'Mobil Box', 'unit' => 'Hari', 'price' => 600000, 'qty' => 1],
            ['name' => 'Pick Up', 'unit' => 'Hari', 'price' => 350000, 'qty' => 1],
            ['name' => 'Genset', 'unit' => 'Hari', 'price' => 500000, 'qty' => 1],
            ['name' => 'Tenda', 'unit' => 'Hari', 'price' => 250000, 'qty' => 1],
            ['name' => 'Kursi', 'unit' => 'Pcs', 'price' => 15000, 'qty' => 1],
            ['name' => 'Meja', 'unit' => 'Pcs', 'price' => 25000, 'qty' => 1],
            ['name' => 'Sound System', 'unit' => 'Hari', 'price' => 400000, 'qty' => 1],
            ['name' => 'Proyektor', 'unit' => 'Hari', 'price' => 200000, 'qty' => 1],
        ];

        foreach ($rentals as $rental) {
            // Idempotent: update/create by (name, unit)
            VendorRental::query()->updateOrCreate(
                [
                    'name' => $rental['name'],
                    'unit' => $rental['unit'],
                ],
                [
                    'price' => $rental['price'],
                    'qty' => $rental['qty'] ?? 1,
                ],
            );
        }
    }
}
