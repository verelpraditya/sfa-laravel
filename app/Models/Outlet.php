<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'address',
        'district',
        'city',
        'category',
        'outlet_type',
        'outlet_status',
        'official_kode',
        'verification_status',
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

    public function typeLabel(): string
    {
        return Str::of($this->outlet_type)->replace('_', ' ')->title()->toString();
    }

    public function statusLabel(): string
    {
        return $this->outlet_status === 'inactive' ? 'Inactive' : 'Active';
    }

    public function verificationLabel(): string
    {
        return $this->verification_status ? Str::ucfirst($this->verification_status) : 'Tidak Perlu';
    }
}
