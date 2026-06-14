<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CfmTraineeChecklistItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'phase_number',
        'phase_title',
        'phase_target',
        'section_title',
        'title',
        'slug',
        'sort_order',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(CfmTraineeChecklistProgress::class);
    }
}
