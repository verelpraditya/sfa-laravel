<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmdVisitDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'po_amount',
        'payment_amount',
        'display_photo_path',
    ];

    protected function casts(): array
    {
        return [
            'po_amount' => 'decimal:2',
            'payment_amount' => 'decimal:2',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
