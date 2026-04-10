<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'pic_name',
        'pic_phone',
        'address',
        'district',
        'city',
        'category',
        'outlet_status',
        'official_kode',
        'verified_by',
        'verified_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function latestVisit(): HasOne
    {
        return $this->hasOne(Visit::class)->latestOfMany('visited_at');
    }

    public function statusLabel(): string
    {
        return match ($this->outlet_status) {
            'prospek' => 'Prospek',
            'pending' => 'Pending',
            'active' => 'Aktif',
            'inactive' => 'Inactive',
            default => '-'
        };
    }
}
