<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionTestController extends Controller
{
    public function testSession(Request $request)
    {
        // Test session functionality
        $sessionId = Session::getId();
        $csrfToken = csrf_token();
        
        // Store a test value in session
        Session::put('test_value', 'session_working_' . time());
        $testValue = Session::get('test_value');
        
        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'csrf_token' => $csrfToken,
            'test_value' => $testValue,
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime'),
            'timestamp' => now()->toISOString()
        ]);
    }

    public function testCsrf(Request $request)
    {
        // Test CSRF token validation
        try {
            $request->validate([
                'test_field' => 'required|string'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'CSRF token is valid',
                'received_value' => $request->input('test_field')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'CSRF token validation failed: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 419);
        }
    }

    public function clearSession(Request $request)
    {
        // Clear session for testing
        Session::flush();
        
        return response()->json([
            'success' => true,
            'message' => 'Session cleared successfully'
        ]);
    }
}
