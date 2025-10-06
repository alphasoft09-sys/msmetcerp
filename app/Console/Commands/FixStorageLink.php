<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class FixStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix storage link issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking storage link...');

        $publicStoragePath = public_path('storage');
        $targetPath = storage_path('app/public');

        // Check if public/storage exists
        if (File::exists($publicStoragePath)) {
            // Check if it's a symlink
            if (is_link($publicStoragePath)) {
                $linkTarget = readlink($publicStoragePath);
                $this->info("Storage link exists and points to: {$linkTarget}");
                
                // Check if target exists
                if (!File::exists($linkTarget)) {
                    $this->error("Link target does not exist: {$linkTarget}");
                    $this->info("Removing broken link...");
                    File::delete($publicStoragePath);
                    $this->recreateLink();
                } else {
                    $this->info("Link target exists and is valid.");
                }
            } else {
                $this->error("Storage path exists but is not a symlink. Removing...");
                if (is_dir($publicStoragePath)) {
                    File::deleteDirectory($publicStoragePath);
                } else {
                    File::delete($publicStoragePath);
                }
                $this->recreateLink();
            }
        } else {
            $this->warn("Storage link does not exist. Creating...");
            $this->recreateLink();
        }

        // Check permissions on storage directory
        $this->checkPermissions();
        
        // Create test file
        $this->testFileCreation();
        
        $this->info('Storage link check completed!');
    }
    
    /**
     * Recreate the storage link
     */
    private function recreateLink()
    {
        $this->info("Creating storage link...");
        try {
            Artisan::call('storage:link');
            $this->info("Storage link created successfully!");
        } catch (\Exception $e) {
            $this->error("Failed to create storage link: " . $e->getMessage());
        }
    }
    
    /**
     * Check and fix permissions on storage directory
     */
    private function checkPermissions()
    {
        $storagePath = storage_path();
        $publicStoragePath = public_path('storage');
        
        $this->info("Checking permissions on storage directories...");
        
        // Check storage directory
        if (File::exists($storagePath)) {
            if (!is_writable($storagePath)) {
                $this->warn("Storage directory is not writable: {$storagePath}");
                $this->info("Attempting to fix permissions...");
                chmod($storagePath, 0775);
                if (is_writable($storagePath)) {
                    $this->info("Fixed permissions on storage directory.");
                } else {
                    $this->error("Failed to fix permissions on storage directory.");
                }
            } else {
                $this->info("Storage directory is writable.");
            }
        }
        
        // Check public/storage directory
        if (File::exists($publicStoragePath)) {
            if (!is_writable($publicStoragePath)) {
                $this->warn("Public storage directory is not writable: {$publicStoragePath}");
                $this->info("Attempting to fix permissions...");
                chmod($publicStoragePath, 0775);
                if (is_writable($publicStoragePath)) {
                    $this->info("Fixed permissions on public storage directory.");
                } else {
                    $this->error("Failed to fix permissions on public storage directory.");
                }
            } else {
                $this->info("Public storage directory is writable.");
            }
        }
        
        // Check app/public directory
        $appPublicPath = storage_path('app/public');
        if (File::exists($appPublicPath)) {
            if (!is_writable($appPublicPath)) {
                $this->warn("App public directory is not writable: {$appPublicPath}");
                $this->info("Attempting to fix permissions...");
                chmod($appPublicPath, 0775);
                if (is_writable($appPublicPath)) {
                    $this->info("Fixed permissions on app public directory.");
                } else {
                    $this->error("Failed to fix permissions on app public directory.");
                }
            } else {
                $this->info("App public directory is writable.");
            }
        } else {
            $this->info("Creating app public directory...");
            File::makeDirectory($appPublicPath, 0775, true, true);
        }
    }
    
    /**
     * Test file creation in storage
     */
    private function testFileCreation()
    {
        $this->info("Testing file creation in storage...");
        
        $testFile = storage_path('app/public/test-' . time() . '.txt');
        $testContent = "Storage test at " . date('Y-m-d H:i:s');
        
        try {
            if (File::put($testFile, $testContent)) {
                $this->info("Test file created successfully: {$testFile}");
                
                // Read back the file
                $readContent = File::get($testFile);
                if ($readContent === $testContent) {
                    $this->info("File content verified successfully.");
                } else {
                    $this->error("File content does not match what was written!");
                }
                
                // Clean up
                File::delete($testFile);
                $this->info("Test file deleted.");
            } else {
                $this->error("Failed to create test file!");
            }
        } catch (\Exception $e) {
            $this->error("Exception while testing file creation: " . $e->getMessage());
        }
    }
}
