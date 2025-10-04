<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class CleanLoginAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'login:clean {--days=30 : Number of days to keep login attempts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old login attempts from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning login attempts older than {$days} days...");

        $deletedCount = LoginAttempt::where('attempted_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deletedCount} old login attempts.");

        // Also clean successful attempts older than 7 days
        $successfulCutoff = Carbon::now()->subDays(7);
        $successfulDeleted = LoginAttempt::where('success', true)
            ->where('attempted_at', '<', $successfulCutoff)
            ->delete();

        $this->info("Deleted {$successfulDeleted} old successful login attempts.");

        $this->info('Login attempts cleanup completed successfully!');
    }
}
