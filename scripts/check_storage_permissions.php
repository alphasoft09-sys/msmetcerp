<?php
/**
 * Script to check storage permissions and diagnose issues
 * Run this script on the server to diagnose storage problems
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "=== Storage Diagnostics ===\n\n";

// Check if storage directory exists
$storagePath = storage_path('app/public');
echo "Storage path: " . $storagePath . "\n";
echo "Exists: " . (file_exists($storagePath) ? "Yes" : "No") . "\n";

if (file_exists($storagePath)) {
    echo "Is directory: " . (is_dir($storagePath) ? "Yes" : "No") . "\n";
    echo "Is readable: " . (is_readable($storagePath) ? "Yes" : "No") . "\n";
    echo "Is writable: " . (is_writable($storagePath) ? "Yes" : "No") . "\n";
    
    // Get permissions
    echo "Permissions: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n";
    
    // Get owner/group
    $owner = posix_getpwuid(fileowner($storagePath));
    $group = posix_getgrgid(filegroup($storagePath));
    echo "Owner: " . $owner['name'] . "\n";
    echo "Group: " . $group['name'] . "\n";
}

// Check if public/storage symlink exists
$publicStoragePath = public_path('storage');
echo "\nPublic storage symlink: " . $publicStoragePath . "\n";
echo "Exists: " . (file_exists($publicStoragePath) ? "Yes" : "No") . "\n";

if (file_exists($publicStoragePath)) {
    echo "Is symlink: " . (is_link($publicStoragePath) ? "Yes" : "No") . "\n";
    if (is_link($publicStoragePath)) {
        echo "Target: " . readlink($publicStoragePath) . "\n";
        echo "Target exists: " . (file_exists(readlink($publicStoragePath)) ? "Yes" : "No") . "\n";
    }
}

// Check if we can write a test file
echo "\nTesting file write to storage...\n";
try {
    $testContent = "Storage test at " . date('Y-m-d H:i:s');
    $testFile = 'test-' . time() . '.txt';
    
    if (Storage::disk('public')->put($testFile, $testContent)) {
        echo "Test file written successfully\n";
        
        // Check if file exists
        if (Storage::disk('public')->exists($testFile)) {
            echo "Test file exists after writing\n";
            
            // Check content
            $readContent = Storage::disk('public')->get($testFile);
            echo "Content matches: " . ($readContent === $testContent ? "Yes" : "No") . "\n";
            
            // Get URL
            $url = Storage::disk('public')->url($testFile);
            echo "File URL: " . $url . "\n";
            
            // Clean up
            Storage::disk('public')->delete($testFile);
            echo "Test file deleted\n";
        } else {
            echo "ERROR: Test file does not exist after writing!\n";
        }
    } else {
        echo "ERROR: Failed to write test file\n";
    }
} catch (Exception $e) {
    echo "ERROR: Exception while testing file write: " . $e->getMessage() . "\n";
}

// Check disk space
echo "\nDisk space:\n";
if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
    $free = disk_free_space($storagePath);
    $total = disk_total_space($storagePath);
    $used = $total - $free;
    $percentUsed = round($used / $total * 100, 2);
    
    echo "Total: " . formatBytes($total) . "\n";
    echo "Used: " . formatBytes($used) . " (" . $percentUsed . "%)\n";
    echo "Free: " . formatBytes($free) . "\n";
} else {
    echo "Cannot check disk space - function not available\n";
}

// Check for common LMS image directories
echo "\nChecking for LMS image directories:\n";
$lmsDirs = Storage::disk('public')->directories('lms-content-images');
echo "Found " . count($lmsDirs) . " LMS content directories\n";
foreach ($lmsDirs as $dir) {
    $files = Storage::disk('public')->files($dir);
    echo "- " . $dir . ": " . count($files) . " files\n";
}

echo "\n=== End of Diagnostics ===\n";

// Helper function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
