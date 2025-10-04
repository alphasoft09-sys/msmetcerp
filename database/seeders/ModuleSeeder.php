<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'module_name' => 'Computer Fundamentals',
                'nos_code' => 'M001',
                'description' => 'Basic computer concepts and operations',
                'is_theory' => true,
            ],
            [
                'module_name' => 'Programming Fundamentals',
                'nos_code' => 'M002',
                'description' => 'Introduction to programming concepts',
                'is_theory' => false,
            ],
            [
                'module_name' => 'Database Management',
                'nos_code' => 'M003',
                'description' => 'Database design and SQL',
                'is_theory' => true,
            ],
            [
                'module_name' => 'Web Development',
                'nos_code' => 'M004',
                'description' => 'HTML, CSS, and JavaScript',
                'is_theory' => false,
            ],
            [
                'module_name' => 'Software Engineering',
                'nos_code' => 'M005',
                'description' => 'Software development lifecycle',
                'is_theory' => true,
            ],
            [
                'module_name' => 'Network Administration',
                'nos_code' => 'M006',
                'description' => 'Computer networking concepts',
                'is_theory' => true,
            ],
            [
                'module_name' => 'Cybersecurity',
                'nos_code' => 'M007',
                'description' => 'Information security principles',
                'is_theory' => true,
            ],
            [
                'module_name' => 'Mobile App Development',
                'nos_code' => 'M008',
                'description' => 'Mobile application development',
                'is_theory' => false,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }

        $this->command->info('Modules created successfully!');
    }
}
