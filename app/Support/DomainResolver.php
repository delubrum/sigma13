<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class DomainResolver
{
    /** Routes whose studly-case differs from the actual domain folder name */
    private const array ALIASES = [
        'helpdesk' => 'HelpDesk',
    ];

    public static function fromRoute(string $route): string
    {
        return self::ALIASES[$route] ?? Str::studly($route);
    }
}
