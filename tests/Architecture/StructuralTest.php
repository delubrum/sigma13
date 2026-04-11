<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Domain\Shared\Data\FieldWidth;
use App\Support\HtmxOrchestrator;
use Carbon\Carbon;
use Illuminate\Http\Middleware\FrameGuard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Prism\Prism\Facades\Prism;
use Spatie\Csp\AddCspHeaders;
use Spatie\Honeypot\ProtectAgainstSpam;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

/**
 * SIGMA 13 — ARCHITECTURE TEST MASTER
 * Stack: HALT (HTMX · Alpine.js · Laravel 13 · Tabulator)
 *        + Spatie Data · lorisleiva Actions · Pipelines · FrankenPHP Octane
 *
 * Nomenclatura de tests : español  (output legible para el dev)
 * Código PHP / clases   : inglés   (sin excepción)
 */
$core = 'App\Domain\*\Actions';
$web = 'App\Domain\*\Web\Actions';
$api = 'App\Domain\*\Api\Actions';
$views = 'App\Domain\*\Web\Views';
$pipes = 'App\Domain\*\Pipelines';

// =============================================================================
// 1. GLOBAL HYGIENE
// =============================================================================

arch('strict types obligatorio en todo el proyecto')
    ->expect('App')
    ->toUseStrictTypes();

arch('no se permiten funciones de debug en ningún entorno')
    ->expect(['dd', 'dump', 'ray', 'die', 'exit', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('no se permiten funciones peligrosas ni superglobals')
    ->expect('App')
    ->not->toUse([
        'eval', 'exec', 'system', 'passthru', 'shell_exec',
        '$_POST', '$_GET', '$_REQUEST', '$_FILES', '$_COOKIE', '$_SERVER',
    ]);

// =============================================================================
// 2. DOMAIN WHITELIST — lo único que el domain puede importar
// =============================================================================

arch('carpetas legacy prohibidas dentro del domain')
    ->expect('App\Domain')
    ->not->toUse([
        'App\Models', 'App\Actions', 'App\Data',
        'App\Http', 'App\Providers', 'App\Console',
    ]);

arch('el domain solo puede usar la lista blanca estricta')
    ->expect('App\Domain')
    ->toOnlyUse([
        // Internal
        'App\Domain',
        'App\Support',
        'App\Contracts',
        // Laravel — subsets autorizados
        'Illuminate\Support',
        'Illuminate\Database\Eloquent',
        'Illuminate\Contracts\Auth',        // Identity module needs this
        'Illuminate\Auth',                  // Identity module needs this
        'Illuminate\Pipeline',              // Laravel Pipelines
        'Illuminate\Events',                // Domain event dispatching
        'Illuminate\Bus',                   // Job dispatching via AsJob
        'Illuminate\Queue',                 // Queue contracts
        'Illuminate\Contracts\Queue',       // ShouldQueue interface
        'Illuminate\Notifications',         // NotificationService
        'Illuminate\Contracts\Container',   // IoC for Pipelines
        'Illuminate\Http',                  // Web Actions — controllers, responses, requests
        // Third-party
        'Spatie',
        'Lorisleiva\Actions',
        'ReCaptcha',
        'Prism',
        Prism::class,
        // PHP stdlib
        'Override',
        'Exception',
        'RuntimeException',
        'Error',
        'InvalidArgumentException',
        'LogicException',
        Carbon::class,
        Arr::class,
        Collection::class,
    ])
    ->ignoring('Database\Factories', 'Database\Seeders');

// =============================================================================
// 3. MODULE ISOLATION
//    La comunicación cross-módulo SOLO vía Domain Events o Shared contracts.
//    Nunca importar clases de otro módulo directamente.
// =============================================================================

$modules = [
    'Assets', 'Users', 'Maintenance', 'MaintenanceP',
    'Recruitment', 'IT', 'Dashboard', 'Identity',
    'HR', 'Engineering', 'Operations',
];

foreach ($modules as $module) {
    $others = array_filter($modules, fn (string $m): bool => $m !== $module);

    arch("el módulo {$module} no puede importar clases de otros módulos")
        ->expect("App\Domain\\{$module}")
        ->not->toUse(array_map(
            fn (string $m): string => "App\Domain\\{$m}",
            $others
        ));
}

// =============================================================================
// 4. ACTIONS — contratos y responsabilidades
// =============================================================================

arch('todas las actions deben usar AsAction y declarar handle()')
    ->expect([$core, $web, $api])
    ->classes()
    ->toUse(AsAction::class)
    ->and->toHaveMethod('handle');

arch('core actions no retornan View ni Response — solo DTOs')
    ->expect($core)
    ->not->toUse([
        'Illuminate\Contracts\View',
        'Illuminate\View',
    ]);

arch('core actions no declaran asController()')
    ->expect($core)
    ->not->toHaveMethod('asController');

arch('web y api adapters nunca importan Models directamente')
    ->expect([$web, $api])
    ->not->toUse('App\Domain\*\Models');

// =============================================================================
// 5. HTMX ORCHESTRATOR — uso exclusivo en Web Actions
// =============================================================================

arch('HtmxOrchestrator solo puede usarse en Web Actions')
    ->expect(HtmxOrchestrator::class)
    ->toBeUsedIn($web)
    ->and->not->toBeUsedIn([
        $core, $api, $pipes,
        'App\Domain\*\Models',
        'App\Domain\*\Data',
        'App\Domain\*\ValueObjects',
        'App\Domain\*\Enums',
    ]);

arch('todas las Web Actions deben usar HtmxOrchestrator')
    ->expect($web)
    ->toUse(HtmxOrchestrator::class);

arch('Web Actions no manipulan headers HX-* manualmente')
    ->expect($web)
    ->not->toUse([
        'HX-Request', 'HX-Redirect', 'HX-Trigger',
        'HX-Retarget', 'HX-Reswap', 'HX-Push-Url', 'HX-Refresh',
    ])
    ->ignoring(HtmxOrchestrator::class);

// =============================================================================
// 6. MODELS — anémicos, sin lógica de negocio
// =============================================================================

arch('los models deben permanecer anémicos')
    ->expect('App\Domain\*\Models')
    ->not->toUse($core)
    ->and->not->toHaveMethods([
        'calculate', 'process', 'execute', 'handle',
        'run', 'apply', 'compute', 'validate',
    ]);

arch('los models no se filtran a vistas Blade')
    ->expect('App\Domain\*\Models')
    ->not->toBeUsedIn($views)
    ->description('Las Views solo reciben DTOs — nunca Models directamente.');

// =============================================================================
// 7. DTOs (Spatie Laravel Data) — full power
//
// ✅ Sufijos permitidos : Form · Table · Upsert · Collection · Filter · Resource · Payload
// ✅ fromModel()        : constructor estático canónico de TableData → PERMITIDO
// ✅ __construct        : requerido por readonly class con constructor promotion → PERMITIDO
// ❌ Lógica de vista    : map · toHtml · render → PROHIBIDO
// ❌ Formato inline     : sprintf · number_format dentro del DTO → PROHIBIDO (usar Casts)
// =============================================================================

arch('los DTOs deben ser final, readonly y extender Spatie Data')
    ->expect('App\Domain\*\Data')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(Data::class)
    ->not->toHaveMethods(['map', 'toHtml', 'render'])
    ->not->toUse(['sprintf', 'number_format'])
    ->ignoring(FieldWidth::class);

arch('los DTOs de colección deben usar Lazy para diferir hidratación')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool => str_contains((string) $dto->getName(), 'Collection'))
    ->toUse(Lazy::class);

arch('los TableData deben declarar fromModel() estático')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool => str_contains((string) $dto->getName(), 'Table'))
    ->toHaveMethod('fromModel');

arch('no se permiten DTOs sin sufijo semántico')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool => ! str_contains((string) $dto->getName(), 'Form') &&
        ! str_contains((string) $dto->getName(), 'Table') &&
        ! str_contains((string) $dto->getName(), 'Upsert') &&
        ! str_contains((string) $dto->getName(), 'Collection') &&
        ! str_contains((string) $dto->getName(), 'Filter') &&
        ! str_contains((string) $dto->getName(), 'Resource') &&
        ! str_contains((string) $dto->getName(), 'Payload') &&
        (string) $dto->getName() !== FieldWidth::class
    )
    ->not->toExist();

// =============================================================================
// 8. VALUE OBJECTS & ENUMS
// =============================================================================

arch('value objects deben ser final y readonly')
    ->expect('App\Domain\*\ValueObjects')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('los enums deben ser backed — nunca pure enums')
    ->expect('App\Domain\*\Enums')
    ->not->toBePureEnums();

// =============================================================================
// 9. PIPELINES — pipes como clases de primera clase
// =============================================================================

arch('los pipeline pipes deben ser final y declarar handle()')
    ->expect($pipes)
    ->classes()
    ->toBeFinal()
    ->toHaveMethod('handle');

arch('los pipes son agnósticos de HTTP')
    ->expect($pipes)
    ->not->toUse([Request::class, Response::class]);

// =============================================================================
// 10. SEGURIDAD
// =============================================================================

arch('las mutaciones web (Store/Upsert) deben usar ProtectAgainstSpam')
    ->expect($web)
    ->toUse(ProtectAgainstSpam::class)
    ->ignoring(fn (string $class): bool => ! str_contains($class, 'Store') && ! str_contains($class, 'Upsert'));

arch('el kernel debe incluir headers de seguridad')
    ->expect('App\Http\Kernel')
    ->toUse([AddCspHeaders::class, FrameGuard::class]);

// =============================================================================
// 11. LARAVEL PRESET (convenciones base del framework)
// =============================================================================

arch()->preset()->laravel();