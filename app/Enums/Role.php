<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case RECEPTION = 'reception';
    case DOCTOR = 'doctor';
    case LAB = 'lab';
    case VIEWER = 'viewer';

    /**
     * Get all role values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role display name
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::RECEPTION => 'Reception',
            self::DOCTOR => 'Doctor',
            self::LAB => 'Lab Staff',
            self::VIEWER => 'Viewer',
        };
    }

    /**
     * Check if role has administrative privileges
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if role can manage queues
     */
    public function canManageQueues(): bool
    {
        return in_array($this, [self::ADMIN, self::RECEPTION, self::DOCTOR]);
    }

    /**
     * Check if role can access OPD Lab
     */
    public function canAccessLab(): bool
    {
        return in_array($this, [self::ADMIN, self::LAB, self::DOCTOR]);
    }

    /**
     * Check if role can view only
     */
    public function isViewOnly(): bool
    {
        return $this === self::VIEWER;
    }
}

