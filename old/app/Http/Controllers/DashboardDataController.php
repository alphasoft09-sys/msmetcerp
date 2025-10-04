<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StudentLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardDataController extends Controller
{
    /**
     * Get student registration data for charts
     */
    public function getStudentRegistrationData(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Ensure user has TC Admin role
            if ($user->user_role !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get the number of months to show (default 6)
            $months = $request->get('months', 6);
            
            // Generate labels for the last N months
            $labels = [];
            $data = [];
            
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M');
                
                // Count students registered in this month for this TC
                $count = StudentLogin::where('tc_code', $user->from_tc)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                
                $data[] = $count;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Student Registrations',
                            'data' => $data,
                            'borderColor' => '#d32f2f',
                            'backgroundColor' => 'rgba(211, 47, 47, 0.1)',
                            'tension' => 0.4,
                            'fill' => true
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard data error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data'
            ], 500);
        }
    }

    /**
     * Get student distribution data for pie chart
     */
    public function getStudentDistributionData(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Ensure user has TC Admin role
            if ($user->user_role !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get student distribution by class for this TC
            $distribution = StudentLogin::where('tc_code', $user->from_tc)
                ->select('class', DB::raw('count(*) as count'))
                ->groupBy('class')
                ->get();

            $labels = [];
            $data = [];
            $colors = ['#d32f2f', '#2e7d32', '#f57c00', '#1976d2', '#7b1fa2', '#388e3c'];

            foreach ($distribution as $index => $item) {
                $labels[] = $item->class ?: 'Not Specified';
                $data[] = $item->count;
            }

            // If no data, provide default structure
            if (empty($data)) {
                $labels = ['No Students'];
                $data = [1];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'data' => $data,
                            'backgroundColor' => array_slice($colors, 0, count($labels)),
                            'borderWidth' => 0
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard distribution data error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load distribution data'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Ensure user has TC Admin role
            if ($user->user_role !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get statistics for this TC
            $totalStudents = StudentLogin::where('tc_code', $user->from_tc)->count();
            $activeStudents = StudentLogin::where('tc_code', $user->from_tc)
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->count();
            $pendingApprovals = 0; // This can be implemented based on your approval system

            return response()->json([
                'success' => true,
                'data' => [
                    'totalStudents' => $totalStudents,
                    'activeStudents' => $activeStudents,
                    'pendingApprovals' => $pendingApprovals,
                    'tcCode' => $user->from_tc
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard stats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Get student chart data for the main student chart
     */
    public function getStudentChartData(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Ensure user has TC Admin role
            if ($user->user_role !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get the number of days to show (default 30)
            $days = $request->get('days', 30);
            
            // Generate labels for the last N days
            $labels = [];
            $data = [];
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');
                
                // Count students registered on this day for this TC
                $count = StudentLogin::where('tc_code', $user->from_tc)
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                
                $data[] = $count;
            }

            // Log the data for debugging
            \Log::info('Student chart data for TC ' . $user->from_tc . ': ' . json_encode([
                'labels' => $labels,
                'data' => $data
            ]));

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Student Registrations',
                            'data' => $data,
                            'borderColor' => '#d32f2f',
                            'backgroundColor' => 'rgba(211, 47, 47, 0.1)',
                            'tension' => 0.4,
                            'fill' => true
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Student chart data error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student chart data'
            ], 500);
        }
    }
} 