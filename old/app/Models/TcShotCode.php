<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TcShotCode extends Model
{
    use HasFactory;

    protected $table = 'tc_shot_code';

    protected $fillable = [
        'tc_code',
        'shot_code',
    ];

    /**
     * Get the TC code associated with a shot code
     */
    public static function getTcCodeByShotCode($shotCode)
    {
        return self::where('shot_code', $shotCode)->first();
    }

    /**
     * Get the shot code associated with a TC code
     */
    public static function getShotCodeByTcCode($tcCode)
    {
        return self::where('tc_code', $tcCode)->first();
    }

    /**
     * Check if a shot code is available
     * Note: This includes shot codes from deleted TCs to prevent reuse
     */
    public static function isShotCodeAvailable($shotCode)
    {
        return !self::where('shot_code', $shotCode)->exists();
    }

    /**
     * Get all used shot codes (including from deleted TCs)
     */
    public static function getAllUsedShotCodes()
    {
        return self::pluck('shot_code')->toArray();
    }

    /**
     * Check if a shot code was ever used (including deleted TCs)
     */
    public static function wasShotCodeEverUsed($shotCode)
    {
        return self::where('shot_code', $shotCode)->exists();
    }

    /**
     * Check if a TC code exists
     */
    public static function isTcCodeExists($tcCode)
    {
        return self::where('tc_code', $tcCode)->exists();
    }
}
