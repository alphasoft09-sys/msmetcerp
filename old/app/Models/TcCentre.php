<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TcCentre extends Model
{
    protected $fillable = [
        'tc_code',
        'centre_name',
        'address',
    ];

    /**
     * Get the exam schedules for this centre
     */
    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class, 'centre_id');
    }

    /**
     * Scope to get centres by TC code
     */
    public function scopeByTcCode($query, $tcCode)
    {
        return $query->where('tc_code', $tcCode);
    }

    /**
     * Get centres for a specific TC
     */
    public static function getCentresForTc($tcCode)
    {
        return self::where('tc_code', $tcCode)->orderBy('centre_name')->get();
    }
}
