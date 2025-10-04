<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AdminProfile;

class AdminProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Create dummy profile data for each user
            AdminProfile::create([
                'user_id' => $user->id,
                'contact_no' => '9876543210',
                'dob' => '1990-01-15',
                'category' => 'GEN',
                'mother_tongue' => 'English',
                'blood_group' => 'O+',
                'qualification' => 'Bachelor of Technology',
                'course_completed_from' => 'Technical Institute',
                'date_of_completion' => '2012-05-30',
                'current_section' => 'Academic',
                'designation' => 'Faculty',
                'date_of_joining' => '2015-06-01',
                'address_permanent' => '123 Main Street, City, State 12345',
                'address_correspondence' => '123 Main Street, City, State 12345',
                'tot_done' => true,
                'tot_certification_date' => '2016-03-15',
                'tot_certificate_number' => 'TOT123456',
                'is_sme' => true,
                'proficient_module_ids' => json_encode([1, 2, 3]),
                'sme_module_ids' => json_encode([1, 2]),
            ]);
        }
        
        $this->command->info('Admin profiles created for ' . $users->count() . ' users.');
    }
}
