<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesVisitDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'order_amount',
        'receivable_amount',
    ];

    protected function casts(): array
    {
        return [
            'order_amount' => 'decimal:2',
            'receivable_amount' => 'decimal:2',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
