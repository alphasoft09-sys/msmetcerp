<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileNumberService;
use App\Models\ExamSchedule;
use App\Models\TcShotCode;
use Carbon\Carbon;

class TestFileNumberGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:file-number {--schedule-id= : Test with specific exam schedule ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test file number generation for exam schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== File Number Generation Test ===');
        $this->newLine();

        // Check exam schedules
        $examSchedules = ExamSchedule::all();
        $this->info("Total Exam Schedules: {$examSchedules->count()}");

        // Check TC shot codes
        $tcShotCodes = TcShotCode::all();
        $this->info("Total TC Shot Codes: {$tcShotCodes->count()}");

        if ($tcShotCodes->count() > 0) {
            $this->info('Sample TC Shot Codes:');
            foreach ($tcShotCodes->take(3) as $tcShotCode) {
                $this->line("  {$tcShotCode->tc_code} -> {$tcShotCode->shot_code}");
            }
        }

        $this->newLine();

        // Test with specific schedule or first available
        $scheduleId = $this->option('schedule-id');
        if ($scheduleId) {
            $schedule = ExamSchedule::find($scheduleId);
            if (!$schedule) {
                $this->error("Exam schedule with ID {$scheduleId} not found!");
                return 1;
            }
        } else {
            $schedule = $examSchedules->first();
            if (!$schedule) {
                $this->error('No exam schedules found!');
                return 1;
            }
        }

        $this->info("Testing with Exam Schedule ID: {$schedule->id}");
        $this->info("TC Code: {$schedule->tc_code}");
        $this->info("Current File Number: " . ($schedule->file_no ?: 'NULL'));
        $this->newLine();

        // Test file number generation
        $fileNumber = FileNumberService::generateFileNumber($schedule);
        
        if ($fileNumber) {
            $this->info("✅ Generated File Number: {$fileNumber}");
            $this->info("File Number Length: " . strlen($fileNumber));
            
            // Parse the file number
            $components = FileNumberService::parseFileNumber($fileNumber);
            if ($components) {
                $this->newLine();
                $this->info('File Number Components:');
                $this->table(
                    ['Component', 'Value', 'Description'],
                    [
                        ['Prefix', $components['prefix'], 'Fixed prefix'],
                        ['Financial Year', $components['financial_year'], 'FY based on approval date'],
                        ['TC Short Code', $components['tc_short_code'], '2-letter TC code'],
                        ['Date', $components['date'], 'Approval date (DDMMYY)'],
                        ['Serial Number', $components['serial_number'], 'Sequential number'],
                    ]
                );
            }

            // Test validation
            if (FileNumberService::validateFileNumber($fileNumber)) {
                $this->info('✅ File number validation passed');
            } else {
                $this->error('❌ File number validation failed');
            }

        } else {
            $this->error('❌ Failed to generate file number');
        }

        $this->newLine();
        $this->info('=== Test Complete ===');
        
        return 0;
    }
}
