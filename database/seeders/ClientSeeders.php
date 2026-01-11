<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $region = \App\Models\Region::where('name', 'like', '%MAJALENGKA%')->first();

        $clients = [
            [
                'name' => 'Test Client',
                'pic' => 'Dimas Candra',
                'email' => 'dimas@livasya.com',
                'phone' => '081234567890',
                'region_id' => $region->id
            ],
        ];

        \App\Models\Client::insert($clients);
    }
}
