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
            Storage::disk('public')->makeDirectory($directory);
            
            // Decode the image
            $imageData = base64_decode($base64Data);
            
            // Compress the image before saving
            $compressedImageData = $this->compressImage($imageData, $imageType);
            
            $filePath = $directory . '/' . $filename;
            
            if (Storage::disk('public')->put($filePath, $compressedImageData)) {
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
