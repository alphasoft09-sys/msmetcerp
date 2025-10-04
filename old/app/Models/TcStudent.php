<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\DynamicTableService;

class TcStudent extends Model
{
    protected $table = null;
    protected $guarded = [];
    
    /**
     * Set the table name dynamically
     */
    public function setTable($tableName)
    {
        $this->table = $tableName;
        return $this;
    }
    
    /**
     * Get table name for a specific TC
     */
    public static function getTableName($tcCode)
    {
        return DynamicTableService::getTableName($tcCode);
    }
    
    /**
     * Check if table exists for a TC
     */
    public static function tableExists($tcCode)
    {
        return DynamicTableService::tableExists($tcCode);
    }
    
    /**
     * Create a new instance for a specific TC
     */
    public static function forTc($tcCode)
    {
        $tableName = self::getTableName($tcCode);
        
        if (!self::tableExists($tcCode)) {
            throw new \Exception("Student table for TC {$tcCode} does not exist");
        }
        
        return (new static())->setTable($tableName);
    }
    
    /**
     * Get all students for a specific TC
     */
    public static function getStudentsForTc($tcCode, $perPage = 15)
    {
        $model = self::forTc($tcCode);
        
        return DB::table($model->getTable())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    /**
     * Search students in a specific TC
     */
    public static function searchStudentsForTc($tcCode, $search, $perPage = 15)
    {
        $model = self::forTc($tcCode);
        $tableName = $model->getTable();
        
        return DB::table($tableName)
            ->where(function($query) use ($search) {
                $query->where('Name', 'like', "%{$search}%")
                      ->orWhere('RollNo', 'like', "%{$search}%")
                      ->orWhere('RefNo', 'like', "%{$search}%")
                      ->orWhere('Email', 'like', "%{$search}%")
                      ->orWhere('MobileNo', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    /**
     * Get student count for a specific TC
     */
    public static function getStudentCountForTc($tcCode)
    {
        $model = self::forTc($tcCode);
        
        return DB::table($model->getTable())->count();
    }
    
    /**
     * Get recent students for a specific TC
     */
    public static function getRecentStudentsForTc($tcCode, $limit = 5)
    {
        $model = self::forTc($tcCode);
        
        return DB::table($model->getTable())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get student statistics for a specific TC
     */
    public static function getStudentStatsForTc($tcCode)
    {
        $model = self::forTc($tcCode);
        $tableName = $model->getTable();
        
        $stats = DB::table($tableName)
            ->selectRaw('
                COUNT(*) as total_students,
                COUNT(CASE WHEN Gender = "Male" THEN 1 END) as male_count,
                COUNT(CASE WHEN Gender = "Female" THEN 1 END) as female_count,
                COUNT(CASE WHEN Gender = "Other" THEN 1 END) as other_count,
                SUM(TraineeFee) as total_fees,
                COUNT(CASE WHEN Minority = 1 THEN 1 END) as minority_count
            ')
            ->first();
            
        return $stats;
    }
} 