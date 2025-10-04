<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle 404 errors by redirecting to login page
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            // Check if the request is for an API route
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Page not found',
                    'message' => 'The requested resource was not found'
                ], 404);
            }

            // Check if user is already authenticated
            if (auth()->check()) {
                // If authenticated, redirect to dashboard based on user role
                $user = auth()->user();
                switch ($user->user_role) {
                    case 1: // TC Admin
                        return redirect()->route('admin.tc-admin.dashboard');
                    case 2: // TC Head
                        return redirect()->route('admin.tc-head.dashboard');
                    case 3: // Exam Cell
                        return redirect()->route('admin.exam-cell.dashboard');
                    case 4: // Assessment Agency
                        return redirect()->route('admin.aa.dashboard');
                    case 5: // Faculty
                        return redirect()->route('admin.tc-faculty.dashboard');
                    default:
                        return redirect()->route('admin.login');
                }
            }

            // If not authenticated, redirect to login page
            return redirect()->route('admin.login')->with('error', 'Page not found. Please login to continue.');
        });
    }
} 