<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity,
        Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password',
        'phone', 'avatar', 'bio', 'address', 'is_active',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'invited_at' => 'datetime',
            'auth_version' => 'integer',
        ];
    }

    protected $hidden = [
        'password', 'remember_token', 'auth_version',
        'two_factor_secret', 'two_factor_recovery_codes',
    ];

    /**
     * Permission names loaded for this instance during the current request.
     *
     * @var Collection<int, string>|null
     */
    private ?Collection $cachedPermissionNames = null;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * Names of every permission the user holds directly or through roles.
     *
     * Memoized per instance so repeated authorization checks (for example,
     * once per row in the user list) do not recompute the permission set.
     *
     * @return Collection<int, string>
     */
    public function permissionNames(): Collection
    {
        return $this->cachedPermissionNames ??= $this->getAllPermissions()->pluck('name');
    }

    public function assignableRoles(): Collection
    {
        if ($this->hasRole('super-admin')) {
            return Role::orderBy('name')->pluck('name');
        }

        $mine = $this->permissionNames();

        return Role::with('permissions')
            ->where('name', '!=', 'super-admin')
            ->orderBy('name')
            ->get()
            ->filter(fn ($role) => $role->permissions->pluck('name')->diff($mine)->isEmpty())
            ->pluck('name')
            ->values();
    }

    public function assignablePermissions(): Collection
    {
        return $this->hasRole('super-admin')
            ? Permission::orderBy('name')->get()
            : $this->getAllPermissions()->sortBy('name')->values();
    }
}
