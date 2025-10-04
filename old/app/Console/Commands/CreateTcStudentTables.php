<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\DynamicTableService;

class CreateTcStudentTables extends Command
{
    protected $signature = 'tc:create-student-tables {--force : Force creation even if table exists}';
    protected $description = 'Create student tables for existing TC Admins';

    public function handle()
    {
        $tcAdmins = User::where('user_role', 1)->get();
        
        if ($tcAdmins->isEmpty()) {
            $this->info('No TC Admins found.');
            return;
        }

        $this->info("Found {$tcAdmins->count()} TC Admin(s) to process...");
        $this->newLine();

        $created = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($tcAdmins as $tcAdmin) {
            $this->line("Processing TC Admin: {$tcAdmin->name} (TC Code: {$tcAdmin->from_tc})");
            
            if (empty($tcAdmin->from_tc)) {
                $this->warn("  ⚠️  TC Code is empty, skipping...");
                $skipped++;
                continue;
            }

            $tableName = DynamicTableService::getTableName($tcAdmin->from_tc);
            
            if (DynamicTableService::tableExists($tcAdmin->from_tc) && !$this->option('force')) {
                $this->line("  ℹ️  Table {$tableName} already exists, skipping...");
                $skipped++;
                continue;
            }

            $result = DynamicTableService::createTcStudentTable($tcAdmin->from_tc);
            
            if ($result['success']) {
                $this->info("  ✅ Table {$tableName} created successfully");
                $created++;
            } else {
                $this->error("  ❌ Failed to create table {$tableName}: {$result['message']}");
                $failed++;
            }
            
            $this->newLine();
        }

        $this->info("Summary:");
        $this->line("  ✅ Created: {$created} tables");
        $this->line("  ⏭️  Skipped: {$skipped} tables");
        $this->line("  ❌ Failed: {$failed} tables");
        
        if ($failed > 0) {
            $this->error("Some tables failed to create. Check the logs for details.");
        } else {
            $this->info("All tables processed successfully!");
        }
    }
} 