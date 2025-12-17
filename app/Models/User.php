<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_tenant_id',
        'is_super_admin',
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
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Get the current tenant for this user
     */
    public function currentTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    /**
     * Get all tenants this user belongs to
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')
            ->withPivot('role', 'is_active', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Check if user belongs to a tenant
     */
    public function belongsToTenant(int $tenantId): bool
    {
        return $this->tenants()
            ->where('tenants.id', $tenantId)
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Get user's role in a tenant
     */
    public function getRoleInTenant(int $tenantId): ?string
    {
        $tenant = $this->tenants()
            ->where('tenants.id', $tenantId)
            ->wherePivot('is_active', true)
            ->first();

        return $tenant?->pivot->role;
    }

    /**
     * Get user's role in current tenant
     * Super Admins return 'admin' as their effective role for UI purposes
     */
    public function getCurrentRole(): ?string
    {
        // Super Admin always has admin-level access
        if ($this->isSuperAdmin()) {
            return 'admin';
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant || !$this->current_tenant_id) {
            return null;
        }

        return $this->getRoleInTenant($tenant->id);
    }

    /**
     * Check if user has a specific role in tenant
     */
    public function hasRoleInTenant(int $tenantId, string|array $roles): bool
    {
        $userRole = $this->getRoleInTenant($tenantId);
        
        if (!$userRole) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        
        return in_array($userRole, $roles);
    }

    /**
     * Check if user has a specific role in current tenant
     * Super Admins always return true (they have all roles)
     */
    public function hasRole(string|array $roles): bool
    {
        // Super Admin has all roles
        if ($this->isSuperAdmin()) {
            return true;
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return false;
        }

        return $this->hasRoleInTenant($tenant->id, $roles);
    }

    /**
     * Check if user is admin in tenant
     * Super Admins always return true
     */
    public function isAdmin(int $tenantId = null): bool
    {
        // Super Admin always has admin access
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($tenantId) {
            return $this->hasRoleInTenant($tenantId, 'admin');
        }

        return $this->hasRole('admin');
    }

    /**
     * Check if user can manage queues
     * Super Admins always return true
     */
    public function canManageQueues(int $tenantId = null): bool
    {
        // Super Admin can manage all queues
        if ($this->isSuperAdmin()) {
            return true;
        }

        $roles = ['admin', 'reception', 'doctor'];
        
        if ($tenantId) {
            return $this->hasRoleInTenant($tenantId, $roles);
        }

        return $this->hasRole($roles);
    }

    /**
     * Check if user can access lab
     * Super Admins always return true
     */
    public function canAccessLab(int $tenantId = null): bool
    {
        // Super Admin can access all services
        if ($this->isSuperAdmin()) {
            return true;
        }

        $roles = ['admin', 'lab', 'doctor'];
        
        if ($tenantId) {
            return $this->hasRoleInTenant($tenantId, $roles);
        }

        return $this->hasRole($roles);
    }

    /**
     * Check if user is a Super Admin (platform administrator)
     * Super Admins bypass all subscription plan restrictions
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Switch user's current tenant
     * Super Admin can switch to any tenant
     */
    public function switchTenant(Tenant $tenant): bool
    {
        // Super Admin can switch to any tenant
        if ($this->isSuperAdmin()) {
            $this->update(['current_tenant_id' => $tenant->id]);
            return true;
        }

        // Regular users must belong to the tenant
        if (!$this->belongsToTenant($tenant->id)) {
            return false;
        }

        $this->update(['current_tenant_id' => $tenant->id]);
        return true;
    }

    /**
     * Exit tenant context (set current_tenant_id to null)
     * Only available for Super Admin
     */
    public function exitTenantContext(): bool
    {
        if (!$this->isSuperAdmin()) {
            return false;
        }

        $this->update(['current_tenant_id' => null]);
        return true;
    }
}
