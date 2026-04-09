<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Domain\Shared\Data\FieldWidth;
use Carbon\Carbon;
use Illuminate\Http\Middleware\FrameGuard;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Prism\Prism\Facades\Prism;
use Spatie\Csp\AddCspHeaders;
use Spatie\Honeypot\ProtectAgainstSpam;
use Spatie\LaravelData\Data;

/**
 * ⚠️ ████████████████████████████████████████████████████████████████████████ ⚠️
 * SIGMA ARCHITECTURE - AUTOMATED GOVERNANCE (MASTER BLINDADO V6 - GOD MODE)
 * Defensive + Offensive governance. Zero debt. Zero exceptions.
 * No exceptions without explicit team approval.
 * N+1 prevention → tests/Feature/Architecture/QueryCountTest.php
 */

// 1. GLOBAL SOBRIETY & PHP 8.5 RIGOR
// --------------------------------------------------------------------------

arch('strict types are mandatory')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debug leftovers')
    ->expect(['dd', 'dump', 'ray', 'die', 'exit', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('no direct superglobal access')
    ->expect('App')
    ->not->toUse(['$_POST', '$_GET', '$_REQUEST', '$_FILES']);

arch('avoid dangerous system functions')
    ->expect('App')
    ->not->toUse(['eval', 'exec', 'system', 'passthru', 'shell_exec']);

// 2. DOMAIN BOUNDARIES & ISOLATION
// --------------------------------------------------------------------------

arch('legacy namespaces are strictly forbidden inside domain')
    ->expect('App\Domain')
    ->not->toUse(['App\Models', 'App\Actions', 'App\Data', 'App\Http']);

arch('domain is agnostic to http and ui — the firewall')
    ->expect('App\Domain')
    ->not->toUse([
        'Illuminate\Http',
        'Illuminate\Contracts\View',
        'Illuminate\View',
        'Symfony\Component\HttpFoundation',
        'view', 'response', 'request', 'redirect', 'back',
    ]);

arch('domain dependencies isolation — allowlist')
    ->expect('App\Domain')
    ->toOnlyUse([
        'App\Domain',
        'App\Support',
        'App\Contracts',
        'App\Notifications',
        'App\Providers',
        'Illuminate',
        'Spatie',
        'Lorisleiva\Actions',
        'ReCaptcha',
        'Prism',
        Prism::class,
        'Override',
        'Exception', 'RuntimeException', 'Error', 'InvalidArgumentException',
        Carbon::class,
        'Database\Factories',
        'Database\Seeders',
        Arr::class,
        Collection::class,
    ]);

arch('shared domain must be pure — no module dependencies')
    ->expect('App\Domain\Shared')
    ->not->toUse([
        'App\Domain\Assets',
        'App\Domain\Maintenance',
        'App\Domain\MaintenanceP',
        'App\Domain\Recruitment',
        'App\Domain\Users',
        'App\Domain\IT',
        'App\Domain\Dashboard',
        'App\Domain\Identity',
    ]);

// 3. ACTION RIGOR — LORISLEIVA FULL POWER
// --------------------------------------------------------------------------

arch('actions must be isolated use cases with handle method')
    ->expect('App\Domain\*\Actions')
    ->classes()
    ->toUse(AsAction::class)
    ->and->toHaveMethod('handle');

arch('actions must receive DTOs — never raw HTTP requests')
    ->expect('App\Domain\*\Actions')
    ->not->toUse([\Illuminate\Http\Request::class, 'request']);

arch('actions are the sole owners of business logic')
    ->expect('App\Domain\*\Models')
    ->not->toUse('App\Domain\*\Actions');

arch('models must be anemic — no business logic methods')
    ->expect('App\Domain\*\Models')
    ->not->toHaveMethods(['calculate', 'process', 'execute', 'handle', 'run', 'apply', 'compute']);

arch('controllers must not instantiate actions directly — use AsAction routing')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Domain\*\Actions');

// Lorisleiva Actions son queueables y commandables — no necesitas clases separadas
arch('no separate jobs — actions are queueable via asJob()')
    ->expect('App\Domain\*\Jobs')
    ->not->toExist();

arch('no artisan commands outside actions — use asCommand()')
    ->expect('App\Console\Commands')
    ->not->toExist();

// 4. DATA LAYER — SPATIE DATA FULL POWER
// --------------------------------------------------------------------------

arch('dtos must be final, readonly and immutable')
    ->expect('App\Domain\*\Data')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly()
    ->ignoring(FieldWidth::class);

arch('dtos must extend Spatie Data — no manual mapping or presentation logic')
    ->expect('App\Domain\*\Data')
    ->classes()
    ->toExtend(Data::class)
    ->and->not->toHaveMethod('fromModel')  // usar Data::from(Model) via pipeline
    ->and->not->toHaveMethod('map')
    ->and->not->toHaveMethod('toHtml')
    ->and->not->toHaveMethod('render')
    ->and->not->toUse(['sprintf', 'number_format']) // formatters van en el front
    ->ignoring(FieldWidth::class);

// Forzar uso del pipeline de Spatie Data — sin constructores manuales
arch('dtos must use Spatie Data pipeline — no manual constructors')
    ->expect('App\Domain\*\Data')
    ->classes()
    ->not->toHaveMethod('__construct')
    ->ignoring(FieldWidth::class);

// Solo DTOs de colección usan Lazy — los de detalle/row no lo necesitan
// Convención: AssetCollectionData = lista, AssetTableData = fila individual
arch('collection dtos must use Lazy — never load full datasets in memory')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool => str_contains((string) $dto->getName(), 'Collection'))
    ->toUse(Lazy::class);

arch('table and upsert dtos must use enums for state')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool =>
        str_contains((string) $dto->getName(), 'Table') ||
        str_contains((string) $dto->getName(), 'Upsert')
    )
    ->toUse('App\Domain\*\Enums')
    ->ignoring('App\Domain\Shared\Data');

// Gobernar la convención Form/Table/Upsert — sin DTOs genéricos
arch('only Form, Table and Upsert dtos are allowed — no generic Data classes')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool =>
        ! str_contains((string) $dto->getName(), 'Form')     &&
        ! str_contains((string) $dto->getName(), 'Table')    &&
        ! str_contains((string) $dto->getName(), 'Upsert')   &&
        $dto->getName() !== FieldWidth::class
    )
    ->not->toExist();

// 5. VALUE OBJECTS & ENUMS — DDD FULL POWER
// --------------------------------------------------------------------------

arch('value objects must be final and immutable')
    ->expect('App\Domain\*\ValueObjects')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

// Backed enums obligatorios — pure enums no transportan valor de dominio
// No asumimos string vs int — solo que NO sean pure enums
arch('domain enums must be backed — no pure enums allowed')
    ->expect('App\Domain\*\Enums')
    ->not->toBePureEnums();

// 6. SECURITY
// --------------------------------------------------------------------------

arch('store and upsert actions must be protected against spam')
    ->expect('App\Domain\*\Actions')
    ->toUse(ProtectAgainstSpam::class)
    ->ignoring(fn (string $action): bool =>
        ! str_contains($action, 'Store') && ! str_contains($action, 'Upsert')
    );

arch('csp and frame security headers must be present')
    ->expect('App\Http\Kernel')
    ->toUse([AddCspHeaders::class, FrameGuard::class]);

// 7. PRESENTATION INTEGRITY
// --------------------------------------------------------------------------

arch('eloquent models must not leak into blade views')
    ->expect('App\Domain\*\Models')
    ->not->toBeUsedIn(['resources/views']);