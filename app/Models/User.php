<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN_PUSAT = 'admin_pusat';

    public const ROLE_SUPERVISOR = 'supervisor';

    public const ROLE_SALES = 'sales';

    public const ROLE_SMD = 'smd';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'role',
        'branch_id',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN_PUSAT => 'Admin Pusat',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_SMD => 'SMD',
            default => 'Sales',
        };
    }

    public function dashboardRoute(): string
    {
        return route('dashboard');
    }

    public function isAdminPusat(): bool
    {
        return $this->role === self::ROLE_ADMIN_PUSAT;
    }

    public function isSales(): bool
    {
        return $this->role === self::ROLE_SALES;
    }

    public function isSupervisor(): bool
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    public function isSmd(): bool
    {
        return $this->role === self::ROLE_SMD;
    }

    public function canCreateSalesVisit(): bool
    {
        return in_array($this->role, [self::ROLE_SALES, self::ROLE_SUPERVISOR], true);
    }

    public function canCreateSmdVisit(): bool
    {
        return in_array($this->role, [self::ROLE_SMD, self::ROLE_SUPERVISOR], true);
    }

    public function canVerifyOutlets(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_PUSAT, self::ROLE_SUPERVISOR], true);
    }

    public function canManageOutletMaster(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_PUSAT, self::ROLE_SUPERVISOR], true);
    }

    public function createdOutlets(): HasMany
    {
        return $this->hasMany(Outlet::class, 'created_by');
    }

    public function verifiedOutlets(): HasMany
    {
        return $this->hasMany(Outlet::class, 'verified_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
