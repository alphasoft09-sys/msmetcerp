<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use App\Models\Qualification;
use App\Models\QualificationModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    /**
     * Show the profile form
     */
    public function showForm()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        // If no profile exists, create a default one with "NA" values
        if (!$profile) {
            $profile = AdminProfile::create([
                'user_id' => $user->id,
                'name' => 'NA',
                'contact_no' => 'NA',
                'dob' => '1900-01-01', // Default date
                'category' => 'GEN',
                'mother_tongue' => 'NA',
                'blood_group' => 'NA',
                'qualification' => 'NA',
                'course_completed_from' => 'NA',
                'date_of_completion' => '1900-01-01',
                'current_section' => 'NA',
                'designation' => 'NA',
                'date_of_joining' => '1900-01-01',
                'address_permanent' => 'NA',
                'address_correspondence' => 'NA',
                'tot_done' => false,
                'tot_certification_date' => '1900-01-01',
                'tot_certificate_number' => 'NA',
                'is_sme' => false,
                'proficient_module_ids' => json_encode([]),
                'sme_module_ids' => json_encode([]),
            ]);
            
            \Log::info('Created default profile for user: ' . $user->id);
        }
        
        // Load the qualification relationship if profile exists
        if ($profile) {
            $profile->load('qualificationRelation');
        }
        
        // Get qualifications for dropdown
        $qualifications = Qualification::orderBy('qf_name')->get();
        
        // Get all modules for proficiency selection
        $allModules = \App\Models\QualificationModule::orderBy('module_name')->get();
        
        \Log::info('Profile form loaded - User: ' . $user->id . ', Profile exists: ' . ($profile ? 'Yes' : 'No') . ', All modules count: ' . $allModules->count());
        
        return view('admin.profile.form', compact('user', 'profile', 'qualifications', 'allModules'));
    }

    // Note: Removed getModulesByQualification method since we now show all modules statically

    /**
     * Store or update the admin profile
     */
    public function storeOrUpdate(Request $request)
    {
        try {
            $user = Auth::user();

            // Sanitize module ID arrays before validation
            $request->merge([
                'proficient_module_ids' => array_filter((array) $request->input('proficient_module_ids', [])),
                'sme_qualification_ids' => array_filter((array) $request->input('sme_qualification_ids', [])),
            ]);
            
            // Validation rules - using file instead of image to avoid fileinfo dependency
            $request->validate([
                'profile_photo' => 'nullable|file|max:50', // 50 KB
                'signature' => 'nullable|file|max:20', // 20 KB
                'qualification' => 'nullable|string|max:255',
                'contact_no' => 'required|string|max:15',
                'dob' => 'required|date',
                'category' => 'required|in:GEN,SC,ST,OTHER',
                'mother_tongue' => 'nullable|string|max:100',
                'blood_group' => 'nullable|string|max:10',
                'course_completed_from' => 'nullable|string|max:255',
                'date_of_completion' => 'nullable|date',
                'current_section' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'date_of_joining' => 'nullable|date',
                'address_permanent' => 'nullable|string',
                'address_correspondence' => 'nullable|string',
                'tot_done' => 'required|boolean',
                'tot_certification_date' => 'nullable|date|required_if:tot_done,1',
                'tot_certificate_number' => 'nullable|string|max:100|required_if:tot_done,1|unique:admin_profiles,tot_certificate_number,' . ($user->profile ? $user->profile->id : 'NULL'),
                'qualification_id' => 'nullable|exists:qualifications,id',
                'is_sme' => 'required|boolean',
                'proficient_module_ids' => 'nullable|array',
                'proficient_module_ids.*' => 'exists:qualification_modules,id',
                'sme_qualification_ids' => 'nullable|array',
                'sme_qualification_ids.*' => 'exists:qualifications,id',
                'toa_done' => 'required|boolean',
                'toa_certification_date' => 'nullable|date',
                'toa_certificate_number' => 'nullable|string|max:100|unique:admin_profiles,toa_certificate_number,' . ($user->profile ? $user->profile->id : 'NULL'),
                'toa_completed_at' => 'nullable|date',
                'toa_version' => 'nullable|string|max:50',
                'toa_notes' => 'nullable|string|max:1000',
            ]);

            // Handle file uploads
            $profileData = $request->except(['profile_photo', 'signature']);
            
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Manual file type validation
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $fileExtension = strtolower($request->file('profile_photo')->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid profile photo file type. Only PNG, JPG, and JPEG files are allowed.'
                    ], 422);
                }
                
                // Delete old file if exists
                if ($user->profile && $user->profile->profile_photo) {
                    Storage::disk('public')->delete($user->profile->profile_photo);
                }
                
                $profilePhotoPath = $request->file('profile_photo')->store('admin_profiles/photos', 'public');
                $profileData['profile_photo'] = $profilePhotoPath;
            }

            // Handle signature upload
            if ($request->hasFile('signature')) {
                // Manual file type validation
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $fileExtension = strtolower($request->file('signature')->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid signature file type. Only PNG, JPG, and JPEG files are allowed.'
                    ], 422);
                }
                
                // Delete old file if exists
                if ($user->profile && $user->profile->signature) {
                    Storage::disk('public')->delete($user->profile->signature);
                }
                
                $signaturePath = $request->file('signature')->store('admin_profiles/signatures', 'public');
                $profileData['signature'] = $signaturePath;
            }

            // Convert arrays to JSON for storage
            if (isset($profileData['proficient_module_ids'])) {
                $profileData['proficient_module_ids'] = json_encode($profileData['proficient_module_ids']);
            } else {
                $profileData['proficient_module_ids'] = json_encode([]);
            }
            
            if (isset($profileData['sme_qualification_ids'])) {
                $profileData['sme_qualification_ids'] = json_encode($profileData['sme_qualification_ids']);
            } else {
                $profileData['sme_qualification_ids'] = json_encode([]);
            }

            // Handle TOA completion timestamp
            if (isset($profileData['toa_done']) && $profileData['toa_done'] == 1) {
                if (!isset($profileData['toa_completed_at']) || empty($profileData['toa_completed_at'])) {
                    $profileData['toa_completed_at'] = now();
                }
                // Set default version if not provided
                if (!isset($profileData['toa_version']) || empty($profileData['toa_version'])) {
                    $profileData['toa_version'] = '1.0';
                }
            } else {
                // If TOA is not done, clear the completion timestamp and certificate details
                $profileData['toa_completed_at'] = null;
                $profileData['toa_certification_date'] = null;
                $profileData['toa_certificate_number'] = null;
            }

            // Store or update profile
            if ($user->profile) {
                $user->profile->update($profileData);
                $message = 'Profile updated successfully!';
            } else {
                $profileData['user_id'] = $user->id;
                AdminProfile::create($profileData);
                $message = 'Profile created successfully!';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin Profile Update Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save profile. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View profile (read-only)
     */
    public function view()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            return redirect()->route('admin.profile.form')->with('error', 'Please complete your profile first.');
        }
        
        // Load the qualification relationship
        $profile->load('qualificationRelation');
        
        return view('admin.profile.view', compact('user', 'profile'));
    }

    /**
     * Serve admin profile images (photos and signatures)
     */
    public function serveImage($type, $filename)
    {
        try {
            // Validate type parameter
            if (!in_array($type, ['photos', 'signatures'])) {
                abort(404, 'Invalid image type');
            }

            $filePath = storage_path('app/public/admin_profiles/' . $type . '/' . $filename);
            
            if (!file_exists($filePath)) {
                abort(404, 'Image not found');
            }

            // Determine content type based on file extension
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $contentType = match($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                default => 'application/octet-stream'
            };

            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=31536000',
                'Access-Control-Allow-Origin' => '*'
            ]);
        } catch (\Exception $e) {
            \Log::error('Admin Profile Image Serve Error: ' . $e->getMessage());
            abort(404, 'Image not found');
        }
    }
}
