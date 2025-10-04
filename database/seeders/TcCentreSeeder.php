<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TcCentre;

class TcCentreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample centres for different TCs
        $centres = [
            [
                'tc_code' => 'TC001',
                'centre_name' => 'Main Training Centre',
                'address' => '123 Main Street, Industrial Area, City - 123456',
            ],
            [
                'tc_code' => 'TC001',
                'centre_name' => 'Branch Training Centre',
                'address' => '456 Branch Road, Commercial District, City - 123457',
            ],
            [
                'tc_code' => 'TC002',
                'centre_name' => 'Advanced Skills Centre',
                'address' => '789 Technology Park, Innovation Zone, City - 123458',
            ],
            [
                'tc_code' => 'TC002',
                'centre_name' => 'Regional Training Hub',
                'address' => '321 Regional Avenue, Business Park, City - 123459',
            ],
            [
                'tc_code' => 'TC003',
                'centre_name' => 'Vocational Training Centre',
                'address' => '654 Vocational Street, Skill Development Area, City - 123460',
            ],
        ];

        foreach ($centres as $centre) {
            TcCentre::create($centre);
        }

        $this->command->info('Sample centres seeded successfully!');
    }
}
