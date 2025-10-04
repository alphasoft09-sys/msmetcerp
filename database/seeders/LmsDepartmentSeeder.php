<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LmsDepartment;
use App\Models\User;

class LmsDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first Assessment Agency user (Role 4)
        $admin = User::where('user_role', 4)->first();
        
        if (!$admin) {
            $this->command->warn('No Assessment Agency user found. Please create one first.');
            return;
        }

        $departments = [
            [
                'department_name' => 'Computer Science',
                'description' => 'Computer Science and Information Technology courses and resources.',
            ],
            [
                'department_name' => 'Mechanical Engineering',
                'description' => 'Mechanical Engineering courses covering design, manufacturing, and automation.',
            ],
            [
                'department_name' => 'Electrical Engineering',
                'description' => 'Electrical Engineering courses including power systems, electronics, and control systems.',
            ],
            [
                'department_name' => 'Civil Engineering',
                'description' => 'Civil Engineering courses covering construction, structural design, and infrastructure.',
            ],
            [
                'department_name' => 'Business Management',
                'description' => 'Business and Management courses for entrepreneurship and corporate skills.',
            ],
            [
                'department_name' => 'Agriculture Technology',
                'description' => 'Agricultural technology and farming techniques for modern agriculture.',
            ],
            [
                'department_name' => 'Healthcare Technology',
                'description' => 'Healthcare and medical technology courses for healthcare professionals.',
            ],
            [
                'department_name' => 'Digital Marketing',
                'description' => 'Digital marketing strategies, social media, and online business promotion.',
            ],
        ];

        foreach ($departments as $departmentData) {
            LmsDepartment::create([
                'department_name' => $departmentData['department_name'],
                'department_slug' => LmsDepartment::generateUniqueSlug($departmentData['department_name']),
                'description' => $departmentData['description'],
                'is_active' => true,
                'created_by' => $admin->id,
            ]);
        }

        $this->command->info('LMS Departments seeded successfully!');
    }
}