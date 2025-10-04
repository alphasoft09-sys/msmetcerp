<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DynamicTableService
{
    /**
     * Create a student table for a specific TC
     */
    public static function createTcStudentTable($tcCode)
    {
        // Sanitize the TC code for table name
        $tableName = self::sanitizeTableName($tcCode . '_students');
        
        // Check if table already exists
        if (Schema::hasTable($tableName)) {
            return [
                'success' => false,
                'message' => "Table {$tableName} already exists"
            ];
        }
        
        try {
            Schema::create($tableName, function ($table) {
                $table->id();
                $table->string('ProgName')->nullable()->comment('Program name');
                $table->string('RefNo')->nullable()->comment('Reference number');
                $table->string('RollNo')->nullable()->comment('Roll number');
                $table->string('Name')->nullable()->comment('Student\'s full name');
                $table->string('FatherName')->nullable()->comment('Father\'s name');
                $table->date('DOB')->nullable()->comment('Date of birth');
                $table->enum('Gender', ['Male', 'Female', 'Other'])->nullable()->comment('Gender');
                $table->string('Category')->nullable()->comment('General/OBC/SC/ST/etc.');
                $table->boolean('Minority')->default(false)->comment('Yes/No');
                $table->string('MinorityType')->nullable()->comment('Type of minority (if applicable)');
                $table->string('EducationName')->nullable()->comment('Educational qualification');
                $table->text('Address')->nullable()->comment('Full address');
                $table->string('City')->nullable()->comment('City');
                $table->string('State')->nullable()->comment('State');
                $table->string('District')->nullable()->comment('District');
                $table->string('Country')->default('India')->comment('Country');
                $table->string('Pincode')->nullable()->comment('Postal code');
                $table->string('MobileNo')->nullable()->comment('Mobile number');
                $table->string('PhoneNo')->nullable()->comment('Alternate phone (optional)');
                $table->string('Email')->nullable()->comment('Email address');
                $table->decimal('TraineeFee', 10, 2)->default(0.00)->comment('Fee paid or due');
                $table->string('Photo')->nullable()->comment('File path or URL to the photo');
                $table->timestamps();
                
                // Add indexes for better performance
                $table->index('Email');
                $table->index('RollNo');
                $table->index('RefNo');
                $table->index('Name');
                $table->index('MobileNo');
            });
            
            return [
                'success' => true,
                'message' => "Table {$tableName} created successfully",
                'table_name' => $tableName
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Failed to create table {$tableName}: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Drop a TC student table
     */
    public static function dropTcStudentTable($tcCode)
    {
        $tableName = self::sanitizeTableName($tcCode . '_students');
        
        if (!Schema::hasTable($tableName)) {
            return [
                'success' => false,
                'message' => "Table {$tableName} does not exist"
            ];
        }
        
        try {
            Schema::dropIfExists($tableName);
            
            return [
                'success' => true,
                'message' => "Table {$tableName} dropped successfully"
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Failed to drop table {$tableName}: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if a TC student table exists
     */
    public static function tableExists($tcCode)
    {
        $tableName = self::sanitizeTableName($tcCode . '_students');
        return Schema::hasTable($tableName);
    }
    
    /**
     * Get table name for a TC
     */
    public static function getTableName($tcCode)
    {
        return self::sanitizeTableName($tcCode . '_students');
    }
    
    /**
     * Sanitize table name to ensure it's safe for MySQL
     */
    private static function sanitizeTableName($name)
    {
        // Remove any characters that are not alphanumeric or underscore
        $sanitized = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        
        // Ensure it starts with a letter or underscore
        if (!preg_match('/^[a-zA-Z_]/', $sanitized)) {
            $sanitized = 'tc_' . $sanitized;
        }
        
        // Convert to lowercase for consistency
        return strtolower($sanitized);
    }
    
    /**
     * Get all TC student tables
     */
    public static function getAllTcStudentTables()
    {
        $tables = [];
        $allTables = Schema::getAllTables();
        
        foreach ($allTables as $table) {
            $tableName = $table->name;
            if (preg_match('/^[a-z0-9_]+_students$/', $tableName)) {
                $tables[] = $tableName;
            }
        }
        
        return $tables;
    }
} 