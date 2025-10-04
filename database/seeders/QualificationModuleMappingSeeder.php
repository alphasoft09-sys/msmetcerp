<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Qualification;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class QualificationModuleMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing mappings
        DB::table('qualification_module_mappings')->truncate();
        
        // Get qualifications and modules
        $qualifications = Qualification::all();
        $modules = Module::all();
        
        // Map modules to qualifications
        foreach ($qualifications as $index => $qualification) {
            // Assign 3-4 modules to each qualification
            $moduleIds = $modules->skip($index * 3)->take(3)->pluck('id')->toArray();
            
            foreach ($moduleIds as $moduleId) {
                DB::table('qualification_module_mappings')->insert([
                    'qualification_id' => $qualification->id,
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Qualification-Module mappings created successfully!');
    }
}
