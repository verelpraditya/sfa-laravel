<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmdVisitDisplayPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'photo_path',
        'sort_order',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
