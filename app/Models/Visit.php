<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'outlet_id',
        'user_id',
        'visit_type',
        'outlet_condition',
        'latitude',
        'longitude',
        'visit_photo_path',
        'visited_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salesDetail(): HasOne
    {
        return $this->hasOne(SalesVisitDetail::class);
    }

    public function smdDetail(): HasOne
    {
        return $this->hasOne(SmdVisitDetail::class);
    }

    public function smdActivities(): HasMany
    {
        return $this->hasMany(SmdVisitActivity::class);
    }

    public function displayPhotos(): HasMany
    {
        return $this->hasMany(SmdVisitDisplayPhoto::class)->orderBy('sort_order');
    }

    public function visitedAtForBranch(): ?Carbon
    {
        if (! $this->visited_at) {
            return null;
        }

        return $this->visited_at->copy()->timezone($this->branch?->timezone ?? config('app.timezone'));
    }

    public function salesAmount(): float
    {
        return (float) ($this->visit_type === 'sales'
            ? ($this->salesDetail?->order_amount ?? 0)
            : ($this->smdDetail?->po_amount ?? 0));
    }

    public function collectionAmount(): float
    {
        return (float) ($this->visit_type === 'sales'
            ? ($this->salesDetail?->receivable_amount ?? 0)
            : ($this->smdDetail?->payment_amount ?? 0));
    }

    public function typeLabel(): string
    {
        return Str::upper($this->visit_type);
    }
}
