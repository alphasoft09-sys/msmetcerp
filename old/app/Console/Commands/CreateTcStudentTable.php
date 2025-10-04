<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DynamicTableService;

class CreateTcStudentTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tc:create-student-table {tcCode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a student table for a specific TC';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tcCode = $this->argument('tcCode');
        
        $this->info("Creating student table for TC: {$tcCode}");
        
        $result = DynamicTableService::createTcStudentTable($tcCode);
        
        if ($result['success']) {
            $this->info("✅ " . $result['message']);
            $this->info("Table name: " . $result['table_name']);
        } else {
            $this->error("❌ " . $result['message']);
        }
        
        return $result['success'] ? 0 : 1;
    }
}
