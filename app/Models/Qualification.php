<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Qualification extends Model
{
    protected $fillable = [
        'qf_name',
        'nqr_no',
        'sector',
        'level',
        'qf_type',
        'qf_total_hour',
    ];

    /**
     * Get the modules for this qualification
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(QualificationModule::class, 'qualification_module_mappings', 'qualification_id', 'module_id')
                    ->withTimestamps();
    }
}
