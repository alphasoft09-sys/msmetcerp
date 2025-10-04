<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Qualification;
use App\Models\QualificationModule;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample qualifications
        $qualifications = [
            [
                'qf_name' => 'Diploma in Computer Applications',
                'nqr_no' => 'NQR001',
                'sector' => 'Information Technology',
                'level' => 'Level 5',
                'qf_type' => 'Diploma',
                'qf_total_hour' => 1200,
            ],
            [
                'qf_name' => 'Certificate in Web Development',
                'nqr_no' => 'NQR002',
                'sector' => 'Information Technology',
                'level' => 'Level 4',
                'qf_type' => 'Certificate',
                'qf_total_hour' => 800,
            ],
            [
                'qf_name' => 'Advanced Diploma in Software Engineering',
                'nqr_no' => 'NQR003',
                'sector' => 'Information Technology',
                'level' => 'Level 6',
                'qf_type' => 'Advanced Diploma',
                'qf_total_hour' => 1800,
            ],
        ];

        foreach ($qualifications as $qualificationData) {
            Qualification::create($qualificationData);
        }

        // Create sample modules
        $modules = [
            [
                'module_name' => 'Programming Fundamentals',
                'nos_code' => 'NOS001',
                'is_optional' => false,
                'hour' => 120,
                'credit' => 12.00,
            ],
            [
                'module_name' => 'Database Management',
                'nos_code' => 'NOS002',
                'is_optional' => false,
                'hour' => 100,
                'credit' => 10.00,
            ],
            [
                'module_name' => 'Web Development Basics',
                'nos_code' => 'NOS003',
                'is_optional' => false,
                'hour' => 150,
                'credit' => 15.00,
            ],
            [
                'module_name' => 'Advanced JavaScript',
                'nos_code' => 'NOS004',
                'is_optional' => true,
                'hour' => 80,
                'credit' => 8.00,
            ],
            [
                'module_name' => 'Mobile App Development',
                'nos_code' => 'NOS005',
                'is_optional' => true,
                'hour' => 200,
                'credit' => 20.00,
            ],
            [
                'module_name' => 'Software Testing',
                'nos_code' => 'NOS006',
                'is_optional' => false,
                'hour' => 90,
                'credit' => 9.00,
            ],
            [
                'module_name' => 'Project Management',
                'nos_code' => 'NOS007',
                'is_optional' => true,
                'hour' => 60,
                'credit' => 6.00,
            ],
        ];

        foreach ($modules as $moduleData) {
            QualificationModule::create($moduleData);
        }

        // Create some sample mappings
        $qualification1 = Qualification::where('nqr_no', 'NQR001')->first();
        $qualification2 = Qualification::where('nqr_no', 'NQR002')->first();
        $qualification3 = Qualification::where('nqr_no', 'NQR003')->first();

        $module1 = QualificationModule::where('nos_code', 'NOS001')->first();
        $module2 = QualificationModule::where('nos_code', 'NOS002')->first();
        $module3 = QualificationModule::where('nos_code', 'NOS003')->first();
        $module4 = QualificationModule::where('nos_code', 'NOS004')->first();
        $module5 = QualificationModule::where('nos_code', 'NOS005')->first();
        $module6 = QualificationModule::where('nos_code', 'NOS006')->first();
        $module7 = QualificationModule::where('nos_code', 'NOS007')->first();

        // Map modules to qualifications
        if ($qualification1 && $module1 && $module2 && $module3) {
            $qualification1->modules()->attach([$module1->id, $module2->id, $module3->id]);
        }

        if ($qualification2 && $module3 && $module4) {
            $qualification2->modules()->attach([$module3->id, $module4->id]);
        }

        if ($qualification3 && $module1 && $module2 && $module3 && $module4 && $module5 && $module6 && $module7) {
            $qualification3->modules()->attach([$module1->id, $module2->id, $module3->id, $module4->id, $module5->id, $module6->id, $module7->id]);
        }
    }
}
