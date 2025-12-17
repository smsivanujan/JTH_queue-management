<?php

namespace App\Helpers;

use App\Enums\Role;

class RoleHelper
{
    /**
     * Get current user's role in current tenant
     */
    public static function currentRole(): ?string
    {
        if (!auth()->check()) {
            return null;
        }

        return auth()->user()->getCurrentRole();
    }

    /**
     * Check if current user has role
     */
    public static function hasRole(string|array $roles): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasRole($roles);
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->isAdmin();
    }

    /**
     * Get role display label
     */
    public static function roleLabel(?string $role): string
    {
        if (!$role) {
            return 'No Role';
        }

        try {
            return Role::from($role)->label();
        } catch (\ValueError $e) {
            return ucfirst($role);
        }
    }

    /**
     * Get all available roles
     */
    public static function allRoles(): array
    {
        return Role::values();
    }

    /**
     * Get roles that can manage queues
     */
    public static function queueManagementRoles(): array
    {
        return ['admin', 'reception', 'doctor'];
    }

    /**
     * Get roles that can access lab
     */
    public static function labAccessRoles(): array
    {
        return ['admin', 'lab', 'doctor'];
    }

    /**
     * Get role description (what this role can do)
     */
    public static function roleDescription(?string $role): string
    {
        if (!$role) {
            return 'No role assigned';
        }

        try {
            $roleEnum = Role::from($role);
            return match($roleEnum) {
                Role::ADMIN => 'Full administrative access to all features including queue management, service access, staff management, and subscription management.',
                Role::RECEPTION => 'Can manage queues (next, previous, reset) and customer flow. Cannot manage subscriptions.',
                Role::DOCTOR => 'Can manage queues and access services. Cannot manage subscriptions or staff.',
                Role::LAB => 'Can access services for specialized operations. Cannot manage queues or subscriptions.',
                Role::VIEWER => 'Read-only access. Can view queues but cannot manage them or manage subscriptions.',
            };
        } catch (\ValueError $e) {
            return 'Role capabilities information not available';
        }
    }

    /**
     * Get role permissions list (array of capabilities)
     */
    public static function rolePermissions(?string $role): array
    {
        if (!$role) {
            return [];
        }

        try {
            $roleEnum = Role::from($role);
            return match($roleEnum) {
                Role::ADMIN => [
                    'Manage queues (next, previous, reset)',
                    'Access services',
                    'Manage staff members',
                    'Manage subscriptions and plans',
                    'Manage clinics',
                    'Access all features',
                ],
                Role::RECEPTION => [
                    'Manage queues (next, previous, reset)',
                    'View queue status',
                ],
                Role::DOCTOR => [
                    'Manage queues (next, previous, reset)',
                    'Access services',
                    'View queue status',
                ],
                Role::LAB => [
                    'Access services',
                    'Manage service operations',
                    'View queue status',
                ],
                Role::VIEWER => [
                    'View queue status (read-only)',
                ],
            };
        } catch (\ValueError $e) {
            return [];
        }
    }
}

