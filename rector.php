<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/legacy',
    ])
    // Enable PHP level rules (auto-detected from composer.json)
    ->withPhpSets()
    // Laravel and project specific sets
    ->withSets([
        LaravelSetList::LARAVEL_100, // Laravel 10+ rules
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
    ]);
