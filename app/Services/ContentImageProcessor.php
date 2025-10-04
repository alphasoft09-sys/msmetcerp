<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentImageProcessor
{
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
            Storage::disk('public')->makeDirectory($directory);
            
            // Decode and save the image
            $imageData = base64_decode($base64Data);
            $filePath = $directory . '/' . $filename;
            
            if (Storage::disk('public')->put($filePath, $imageData)) {
                $publicUrl = Storage::disk('public')->url($filePath);
                // Fix double slash issue
                $publicUrl = str_replace('//storage', '/storage', $publicUrl);
                \Log::info('Image saved successfully: ' . $publicUrl);
                // Return the public URL
                return $publicUrl;
            }
            
            \Log::error('Failed to save image: ' . $filePath);
            // If saving failed, return original data URL
            return $matches[0];
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
