<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TcLms;
use App\Models\LmsMedia;
use Intervention\Image\Facades\Image;

class LmsMediaController extends Controller
{
    /**
     * Upload image for LMS site
     */
    public function uploadImage(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can upload images.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only upload images to your own sites.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'lms/images/' . $tcLm->id . '/' . $fileName;
            
            // Store the file
            Storage::disk('public')->put($filePath, file_get_contents($file));
            
            // Get image dimensions
            $image = Image::make($file);
            $dimensions = [
                'width' => $image->width(),
                'height' => $image->height()
            ];
            
            // Create media record
            $media = LmsMedia::create([
                'lms_site_id' => $tcLm->id,
                'media_type' => 'image',
                'original_name' => $originalName,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_url' => Storage::url($filePath),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'alt_text' => $request->alt_text,
                'description' => $request->description,
                'metadata' => $dimensions,
            ]);

            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'url' => $media->file_url,
                    'alt_text' => $media->alt_text,
                    'dimensions' => $dimensions,
                    'file_name' => $media->original_name,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add video embed from external URL
     */
    public function addVideo(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can add videos.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only add videos to your own sites.');
        }

        $request->validate([
            'video_url' => 'required|url',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $videoUrl = $request->video_url;
            $videoData = $this->parseVideoUrl($videoUrl);
            
            if (!$videoData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported video platform. Please use YouTube, Vimeo, or direct video URLs.'
                ], 400);
            }

            // Create media record for video
            $media = LmsMedia::create([
                'lms_site_id' => $tcLm->id,
                'media_type' => 'video',
                'original_name' => $request->title ?: 'Video',
                'file_name' => $videoData['embed_id'],
                'file_path' => $videoUrl,
                'file_url' => $videoUrl,
                'mime_type' => 'video/embed',
                'file_size' => 0,
                'alt_text' => $request->title,
                'description' => $request->description,
                'metadata' => $videoData,
            ]);

            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'url' => $videoUrl,
                    'embed_code' => $videoData['embed_code'],
                    'platform' => $videoData['platform'],
                    'title' => $request->title,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get media files for LMS site
     */
    public function getMedia(TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can view media.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only view media from your own sites.');
        }

        $media = $tcLm->media()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'media' => $media->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->media_type,
                    'url' => $item->file_url,
                    'alt_text' => $item->alt_text,
                    'description' => $item->description,
                    'file_name' => $item->original_name,
                    'file_size' => $item->formatted_size,
                    'created_at' => $item->created_at->format('M d, Y'),
                    'metadata' => $item->metadata,
                ];
            })
        ]);
    }

    /**
     * Delete media file
     */
    public function deleteMedia(Request $request, TcLms $tcLm, LmsMedia $media)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can delete media.');
        }

        if ($tcLm->faculty_code !== $user->email || $media->lms_site_id !== $tcLm->id) {
            abort(403, 'Unauthorized access. You can only delete media from your own sites.');
        }

        try {
            $media->delete(); // This will also delete the file due to model boot method

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse video URL and extract embed information
     */
    private function parseVideoUrl($url)
    {
        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return [
                'platform' => 'youtube',
                'video_id' => $matches[1],
                'embed_id' => $matches[1],
                'embed_code' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $matches[1] . '" frameborder="0" allowfullscreen></iframe>',
                'thumbnail' => 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg'
            ];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return [
                'platform' => 'vimeo',
                'video_id' => $matches[1],
                'embed_id' => $matches[1],
                'embed_code' => '<iframe width="560" height="315" src="https://player.vimeo.com/video/' . $matches[1] . '" frameborder="0" allowfullscreen></iframe>',
                'thumbnail' => 'https://vumbnail.com/' . $matches[1] . '.jpg'
            ];
        }

        // Direct video URL (mp4, webm, etc.)
        if (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $url)) {
            return [
                'platform' => 'direct',
                'video_id' => basename($url),
                'embed_id' => basename($url),
                'embed_code' => '<video width="560" height="315" controls><source src="' . $url . '" type="video/mp4">Your browser does not support the video tag.</video>',
                'thumbnail' => null
            ];
        }

        return null;
    }
}