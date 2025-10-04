<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TcShotCode;

class TcShotCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shotCodes = [
            ['tc_code' => 'TC001', 'shot_code' => 'TC'],
            ['tc_code' => 'TC002', 'shot_code' => 'AA'],
            ['tc_code' => 'TC003', 'shot_code' => 'BB'],
            ['tc_code' => 'TC004', 'shot_code' => 'CC'],
            ['tc_code' => 'TC005', 'shot_code' => 'DD'],
        ];

        foreach ($shotCodes as $shotCode) {
            TcShotCode::updateOrCreate(
                ['tc_code' => $shotCode['tc_code']],
                ['shot_code' => $shotCode['shot_code']]
            );
        }

        $this->command->info('TC Shot Codes seeded successfully!');
    }
}
