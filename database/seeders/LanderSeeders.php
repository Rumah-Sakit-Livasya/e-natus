<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanderSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landers = [
            ['name' => 'Ibu Lia Vallini', 'code' => 'LV'],
            ['name' => 'Ibu Luci', 'code' => 'LA'],
            ['name' => 'Natus', 'code' => 'NVM']
        ];

        \App\Models\Lander::insert($landers);
    }
}
