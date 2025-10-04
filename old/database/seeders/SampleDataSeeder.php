<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentLogin;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        $adminUsers = [
            [
                'name' => 'TC Admin',
                'email' => 'tcadmin@example.com',
                'password' => Hash::make('password'),
                'user_role' => 1,
                'from_tc' => 'TC001'
            ],
            [
                'name' => 'TC Head',
                'email' => 'tchead@example.com',
                'password' => Hash::make('password'),
                'user_role' => 2,
                'from_tc' => 'TC001'
            ],
            [
                'name' => 'Exam Cell',
                'email' => 'examcell@example.com',
                'password' => Hash::make('password'),
                'user_role' => 3,
                'from_tc' => 'TC001'
            ],
            [
                'name' => 'Assessment Agency',
                'email' => 'aa@example.com',
                'password' => Hash::make('password'),
                'user_role' => 4,
                'from_tc' => 'TC001'
            ],
            [
                'name' => 'TC Faculty',
                'email' => 'tcfaculty@example.com',
                'password' => Hash::make('password'),
                'user_role' => 5,
                'from_tc' => 'TC001'
            ]
        ];

        foreach ($adminUsers as $admin) {
            User::create($admin);
        }

        // Create student users
        $studentUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@student.com',
                'password' => Hash::make('password'),
                'tc_code' => 'TC001',
                'class' => 'Class 10',
                'roll_number' => 'R001',
                'phone' => '1234567890'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@student.com',
                'password' => Hash::make('password'),
                'tc_code' => 'TC001',
                'class' => 'Class 11',
                'roll_number' => 'R002',
                'phone' => '9876543210'
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@student.com',
                'password' => Hash::make('password'),
                'tc_code' => 'TC001',
                'class' => 'Class 12',
                'roll_number' => 'R003',
                'phone' => '5551234567'
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@student.com',
                'password' => Hash::make('password'),
                'tc_code' => 'TC001',
                'class' => 'Class 10',
                'roll_number' => 'R004',
                'phone' => '4449876543'
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@student.com',
                'password' => Hash::make('password'),
                'tc_code' => 'TC001',
                'class' => 'Class 11',
                'roll_number' => 'R005',
                'phone' => '3334567890'
            ]
        ];

        foreach ($studentUsers as $student) {
            StudentLogin::create($student);
        }

        $this->command->info('Sample data seeded successfully!');
        $this->command->info('Admin Login Credentials:');
        $this->command->info('TC Admin: tcadmin@example.com / password');
        $this->command->info('TC Head: tchead@example.com / password');
        $this->command->info('Exam Cell: examcell@example.com / password');
        $this->command->info('Assessment Agency: aa@example.com / password');
        $this->command->info('TC Faculty: tcfaculty@example.com / password');
        $this->command->info('');
        $this->command->info('Student Login Credentials:');
        $this->command->info('John Doe: john.doe@student.com / password');
        $this->command->info('Jane Smith: jane.smith@student.com / password');
        $this->command->info('Mike Johnson: mike.johnson@student.com / password');
        $this->command->info('Sarah Wilson: sarah.wilson@student.com / password');
        $this->command->info('David Brown: david.brown@student.com / password');
    }
}
