<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ContentImageProcessor
{
    private $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Compress image data
     */
    private function compressImage($imageData, $imageType, $quality = 80, $maxWidth = 1920, $maxHeight = 1080)
    {
        try {
            // Create image from binary data
            $image = $this->imageManager->read($imageData);
            
            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            
            \Log::info("Original image size: {$originalWidth}x{$originalHeight}");
            
            // Resize if too large
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $image->scaleDown($maxWidth, $maxHeight);
                \Log::info("Resized image to: {$image->width()}x{$image->height()}");
            }
            
            // Compress based on image type
            $compressedData = null;
            switch (strtolower($imageType)) {
                case 'jpeg':
                case 'jpg':
                    $compressedData = $image->toJpeg($quality);
                    break;
                case 'png':
                    // For PNG, use lower quality (0-9, where 9 is best compression)
                    $pngQuality = max(0, 9 - ($quality / 10));
                    $compressedData = $image->toPng($pngQuality);
                    break;
                case 'webp':
                    $compressedData = $image->toWebp($quality);
                    break;
                default:
                    // For other formats, try to convert to JPEG
                    $compressedData = $image->toJpeg($quality);
                    break;
            }
            
            $originalSize = strlen($imageData);
            $compressedSize = strlen($compressedData);
            $compressionRatio = round((1 - $compressedSize / $originalSize) * 100, 2);
            
            \Log::info("Image compressed: {$originalSize} bytes -> {$compressedSize} bytes ({$compressionRatio}% reduction)");
            
            return $compressedData;
            
        } catch (\Exception $e) {
            \Log::error('Image compression failed: ' . $e->getMessage());
            // Return original data if compression fails
            return $imageData;
        }
    }

    /**
     * Process content and extract base64 images to files
     */
    public function processContent($content, $lmsSiteId)
    {
        if (empty($content)) {
            return $content;
        }

        \Log::info('Processing content for site: ' . $lmsSiteId);
        \Log::info('Content length before processing: ' . strlen($content));
        
        // Find all base64 data URLs in the content
        // Use a simpler, more reliable pattern for multiple images
        $pattern = '/data:image\/([a-zA-Z]*);base64,([A-Za-z0-9+\/=\s]+)/';
        
        $imageCount = 0;
        $processedContent = preg_replace_callback($pattern, function ($matches) use ($lmsSiteId, &$imageCount) {
            $imageCount++;
            $imageType = $matches[1];
            $base64Data = trim($matches[2]); // Remove any whitespace
            
            \Log::info("Processing image #{$imageCount}, type: {$imageType}, data length: " . strlen($base64Data));
            
            // Generate unique filename
            $filename = 'lms_image_' . $lmsSiteId . '_' . Str::random(10) . '.' . $imageType;
            
            // Create directory if it doesn't exist
            $directory = 'lms-content-images/' . $lmsSiteId;
            
            try {
                // Check if storage is writable
                if (!Storage::disk('public')->exists('/')) {
                    \Log::error('Storage disk is not accessible or writable');
                    throw new \Exception('Storage disk is not accessible');
                }
                
                // Make directory and check if it was created
                Storage::disk('public')->makeDirectory($directory);
                if (!Storage::disk('public')->exists($directory)) {
                    \Log::error('Failed to create directory: ' . $directory);
                    throw new \Exception('Failed to create directory');
                }
                
                // Decode the image
                $imageData = base64_decode($base64Data);
                if (!$imageData) {
                    \Log::error('Failed to decode base64 data');
                    throw new \Exception('Invalid base64 data');
                }
                
                // Compress the image before saving
                $compressedImageData = $this->compressImage($imageData, $imageType);
                
                $filePath = $directory . '/' . $filename;
                
                // Check disk free space if possible
                if (function_exists('disk_free_space')) {
                    $freeSpace = disk_free_space(storage_path('app/public'));
                    $imageSize = strlen($compressedImageData);
                    if ($freeSpace < $imageSize) {
                        \Log::error("Not enough disk space. Required: {$imageSize}, Available: {$freeSpace}");
                        throw new \Exception('Not enough disk space');
                    }
                }
                
                // Put file with error checking
                $result = Storage::disk('public')->put($filePath, $compressedImageData);
                if (!$result) {
                    \Log::error('Storage::put returned false for: ' . $filePath);
                    throw new \Exception('Failed to write file');
                }
                
                // Verify file was actually created
                if (!Storage::disk('public')->exists($filePath)) {
                    \Log::error('File was not created after put operation: ' . $filePath);
                    throw new \Exception('File was not created');
                }
                
                // Get file size to verify
                $savedSize = Storage::disk('public')->size($filePath);
                if ($savedSize <= 0) {
                    \Log::error('File was created but has zero size: ' . $filePath);
                    throw new \Exception('File has zero size');
                }
                
                // Get public URL
                $publicUrl = Storage::disk('public')->url($filePath);
                
                // Fix URL issues
                $appUrl = config('app.url');
                
                // If APP_URL doesn't end with a slash, add it
                if (substr($appUrl, -1) !== '/') {
                    $appUrl .= '/';
                }
                
                // If URL is relative, make it absolute
                if (substr($publicUrl, 0, 1) === '/') {
                    $publicUrl = $appUrl . ltrim($publicUrl, '/');
                }
                
                // Fix double slash issue
                $publicUrl = str_replace('//storage', '/storage', $publicUrl);
                
                // Log the URL generation
                \Log::info('Generated URL: ' . $publicUrl);
                \Log::info('APP_URL: ' . config('app.url'));
                
                \Log::info('Image saved successfully: ' . $publicUrl);
                \Log::info('File size: ' . $savedSize . ' bytes');
                
                // Return the public URL
                return $publicUrl;
            } catch (\Exception $e) {
                \Log::error('Failed to save image: ' . $filePath . ' - Error: ' . $e->getMessage());
                \Log::error('Exception trace: ' . $e->getTraceAsString());
                // If saving failed, return original data URL
                return $matches[0];
            }
        }, $content);
        
        \Log::info("Total images processed: {$imageCount}");
        \Log::info('Content length after processing: ' . strlen($processedContent));
        
        return $processedContent;
    }

    /**
     * Clean up old images for a site
     */
    public function cleanupOldImages($lmsSiteId)
    {
        $directory = 'lms-content-images/' . $lmsSiteId;
        
        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }
    }

    /**
     * Clean up unused images (images not referenced in content)
     */
    public function cleanupUnusedImages($lmsSiteId, $content)
    {
        $directory = 'lms-content-images/' . $lmsSiteId;
        
        if (!Storage::disk('public')->exists($directory)) {
            return;
        }
        
        $files = Storage::disk('public')->files($directory);
        
        foreach ($files as $file) {
            $filename = basename($file);
            // Check if this image is referenced in the content
            if (strpos($content, $filename) === false) {
                \Log::info('Deleting unused image: ' . $filename);
                Storage::disk('public')->delete($file);
            }
        }
    }

    /**
     * Get content size in bytes
     */
    public function getContentSize($content)
    {
        return strlen($content);
    }

    /**
     * Check if content is too large
     */
    public function isContentTooLarge($content, $maxSize = 16777215) // 16MB for MEDIUMTEXT
    {
        return $this->getContentSize($content) > $maxSize;
    }
}
