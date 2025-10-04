<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QualificationModule extends Model
{
    protected $fillable = [
        'module_name',
        'nos_code',
        'is_optional',
        'hour',
        'credit',
        'is_viva',
        'is_practical',
        'is_theory',
        'full_mark',
        'pass_mark',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'credit' => 'decimal:2',
        'is_viva' => 'boolean',
        'is_practical' => 'boolean',
        'is_theory' => 'boolean',
        'full_mark' => 'integer',
        'pass_mark' => 'integer',
    ];

    /**
     * Get the qualifications for this module
     */
    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class, 'qualification_module_mappings', 'module_id', 'qualification_id')
                    ->withTimestamps();
    }
}
