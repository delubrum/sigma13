<?php

declare(strict_types=1);

namespace Tests\Architecture;

arch()->expect('App\Http\Controllers')
    ->toOnlyDependOn([
        'App\Http\Requests',
        'App\Http\Resources',
        'App\Actions',
        'App\Data',
        'App\Models',
        'Illuminate\Http',
        \Illuminate\Support\Arr::class,
        \Illuminate\Support\Collection::class,
        'Illuminate\View',
        'Illuminate\Routing',
        'Illuminate\Foundation',
        'Illuminate\Contracts',
        'App\Http\Controllers', // Self reference
        'Tests', // For testing purposes
    ]);

arch()->expect('App\Actions')
    ->not->toDependOn('App\Actions');

arch()->expect('App\Models')
    ->not->toBeUsedIn('App\View');
