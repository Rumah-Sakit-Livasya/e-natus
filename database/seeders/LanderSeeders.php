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

        foreach ($landers as $lander) {
            \App\Models\Lander::withTrashed()->updateOrCreate(
                ['code' => $lander['code']],
                [
                    'name' => $lander['name'],
                    'deleted_at' => null,
                ]
            );
        }
    }
}
