<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use App\Rules\PasswordStrength;

class PasswordHelper
{
    /**
     * Validate password strength using PasswordStrength rule
     * 
     * @param string $password
     * @param int $minLength
     * @param bool $requireUppercase
     * @param bool $requireLowercase
     * @param bool $requireNumbers
     * @param bool $requireSpecialChars
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateStrength(
        string $password,
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireLowercase = true,
        bool $requireNumbers = true,
        bool $requireSpecialChars = false
    ): array {
        $errors = [];
        $rule = new PasswordStrength($minLength, $requireUppercase, $requireLowercase, $requireNumbers, $requireSpecialChars);
        
        // Create a mock validator context
        $fail = function ($message) use (&$errors) {
            $errors[] = $message;
        };
        
        try {
            $rule->validate('password', $password, $fail);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Hash a plain text password (for NEW passwords only)
     * 
     * @param string $plainPassword
     * @return string Hashed password
     */
    public static function hash(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    /**
     * Check if a string is already a hashed password
     * 
     * @param string|null $value
     * @return bool
     */
    public static function isHashed(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }
        
        return str_starts_with($value, '$2y$');
    }
}
