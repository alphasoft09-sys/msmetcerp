<?php

namespace App\Helpers;

class DashboardHelper
{
    /**
     * Get the dashboard route name based on user role
     */
    public static function getDashboardRoute($userRole)
    {
        return match($userRole) {
            1 => 'admin.tc-admin.dashboard',
            2 => 'admin.tc-head.dashboard',
            3 => 'admin.exam-cell.dashboard',
            4 => 'admin.aa.dashboard',
            5 => 'admin.tc-faculty.dashboard',
            default => 'admin.tc-admin.dashboard'
        };
    }

    /**
     * Get the dashboard title based on user role
     */
    public static function getDashboardTitle($userRole)
    {
        return match($userRole) {
            1 => 'TC Admin Dashboard',
            2 => 'TC Head Dashboard',
            3 => 'Exam Cell Dashboard',
            4 => 'Assessment Agency Dashboard',
            5 => 'TC Faculty Dashboard',
            default => 'Admin Dashboard'
        };
    }

    /**
     * Get the dashboard description based on user role
     */
    public static function getDashboardDescription($userRole)
    {
        return match($userRole) {
            1 => 'Manage TC operations and student data',
            2 => 'Oversee TC administration and reports',
            3 => 'Manage exam schedules and results',
            4 => 'Handle assessment and evaluation',
            5 => 'Manage faculty operations and student progress',
            default => 'Administrative dashboard'
        };
    }

    /**
     * Check if current route matches user's dashboard
     */
    public static function isCurrentDashboard($userRole)
    {
        $dashboardRoute = self::getDashboardRoute($userRole);
        return request()->routeIs($dashboardRoute);
    }
} 