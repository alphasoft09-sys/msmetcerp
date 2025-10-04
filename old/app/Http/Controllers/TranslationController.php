<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Translate text using Google Translate API
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'target_lang' => 'required|string|size:2',
            'source_lang' => 'nullable|string|size:2'
        ]);

        $text = $request->input('text');
        $targetLang = $request->input('target_lang');
        $sourceLang = $request->input('source_lang', 'en');

        // Check if we have a cached translation
        $cacheKey = "translation_{$sourceLang}_{$targetLang}_" . md5($text);
        $cachedTranslation = Cache::get($cacheKey);
        
        if ($cachedTranslation) {
            return response()->json([
                'success' => true,
                'translated_text' => $cachedTranslation
            ]);
        }

        try {
            // Get Google Translate API key from environment
            $apiKey = config('services.google.translate_api_key');
            
            if (!$apiKey) {
                // Fallback to predefined translations
                $fallbackTranslation = $this->getFallbackTranslation($text, $targetLang);
                
                if ($fallbackTranslation) {
                    return response()->json([
                        'success' => true,
                        'translated_text' => $fallbackTranslation,
                        'source' => 'fallback'
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Translation service not configured'
                ], 503);
            }

            // Call Google Translate API
            $response = Http::post('https://translation.googleapis.com/language/translate/v2', [
                'q' => $text,
                'target' => $targetLang,
                'source' => $sourceLang,
                'key' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $translatedText = $data['data']['translations'][0]['translatedText'];
                
                // Cache the translation for 24 hours
                Cache::put($cacheKey, $translatedText, now()->addHours(24));
                
                return response()->json([
                    'success' => true,
                    'translated_text' => $translatedText,
                    'source' => 'google_api'
                ]);
            } else {
                // Fallback to predefined translations
                $fallbackTranslation = $this->getFallbackTranslation($text, $targetLang);
                
                if ($fallbackTranslation) {
                    return response()->json([
                        'success' => true,
                        'translated_text' => $fallbackTranslation,
                        'source' => 'fallback'
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Translation failed'
                ], 500);
            }
        } catch (\Exception $e) {
            // Fallback to predefined translations
            $fallbackTranslation = $this->getFallbackTranslation($text, $targetLang);
            
            if ($fallbackTranslation) {
                return response()->json([
                    'success' => true,
                    'translated_text' => $fallbackTranslation,
                    'source' => 'fallback'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Translation service error'
            ], 500);
        }
    }

    /**
     * Get fallback translations for common text
     */
    private function getFallbackTranslation($text, $targetLang)
    {
        $fallbackTranslations = [
            'hi' => [
                'Create an account' => 'खाता बनाएं',
                'Sign in and get started' => 'साइन इन करें और शुरू करें',
                'Email Address' => 'ईमेल पता',
                'Password' => 'पासवर्ड',
                'Remember me' => 'मुझे याद रखें',
                'Forgot Password?' => 'पासवर्ड भूल गए?',
                'Submit' => 'सबमिट करें',
                'Signing In...' => 'साइन इन हो रहा है...',
                'By signing in, you agree to our' => 'साइन इन करके, आप हमारे',
                'Terms & Conditions' => 'नियम और शर्तें',
                'and' => 'और',
                'Privacy Policy' => 'गोपनीयता नीति',
                'Welcome!' => 'स्वागत है!',
                'Sign in to access MSME admin tools and resources.' => 'MSME एडमिन टूल्स और संसाधनों तक पहुंचने के लिए साइन इन करें।',
                'Admin Login | MSME Technology Centre | Government of India' => 'एडमिन लॉगिन | MSME प्रौद्योगिकी केंद्र | भारत सरकार',
                'ADMINISTRATOR LOGIN PORTAL' => 'प्रशासक लॉगिन पोर्टल',
                'SECURE ACCESS' => 'सुरक्षित पहुंच',
                'yourname@example.com' => 'आपकानाम@उदाहरण.com'
            ]
        ];

        return $fallbackTranslations[$targetLang][$text] ?? null;
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages()
    {
        return response()->json([
            'success' => true,
            'languages' => [
                ['code' => 'en', 'name' => 'English', 'native_name' => 'English'],
                ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी'],
                ['code' => 'bn', 'name' => 'Bengali', 'native_name' => 'বাংলা'],
                ['code' => 'te', 'name' => 'Telugu', 'native_name' => 'తెలుగు'],
                ['code' => 'mr', 'name' => 'Marathi', 'native_name' => 'मराठी'],
                ['code' => 'ta', 'name' => 'Tamil', 'native_name' => 'தமிழ்'],
                ['code' => 'gu', 'name' => 'Gujarati', 'native_name' => 'ગુજરાતી'],
                ['code' => 'kn', 'name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ'],
                ['code' => 'ml', 'name' => 'Malayalam', 'native_name' => 'മലയാളം'],
                ['code' => 'pa', 'name' => 'Punjabi', 'native_name' => 'ਪੰਜਾਬੀ']
            ]
        ]);
    }
}
