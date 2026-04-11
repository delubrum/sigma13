<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contract to allow cross-module password resets without direct dependency on the User model.
 */
interface CanResetPasswordContract
{
    /**
     * Update the user's password and save the record.
     */
    public function updatePassword(string $newPassword): void;

    /**
     * Get the user's email address.
     */
    public function getEmail(): string;
}
