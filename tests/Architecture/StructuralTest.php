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
$web = ['App\Domain\*\Web\Actions', 'App\Domain\*\Web\Adapters'];
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
        'App\Mail',
        // Laravel — subsets autorizados
        'Illuminate\Support',
        'Illuminate\Database\Eloquent',
        'Illuminate\Contracts\Auth',        // Identity module needs this
        'Illuminate\Auth',                  // Identity module needs this
        'Illuminate\Foundation\Auth',       // Authenticatable base class
        'Illuminate\Foundation\Events',     // Dispatchable trait for domain events
        'Illuminate\Pipeline',              // Laravel Pipelines
        'Illuminate\Events',                // Domain event dispatching
        'Illuminate\Bus',                   // Job dispatching via AsJob
        'Illuminate\Queue',                 // Queue contracts
        'Illuminate\Contracts\Queue',       // ShouldQueue interface
        'Illuminate\Notifications',         // NotificationService
        'Illuminate\Contracts\Container',   // IoC for Pipelines
        'Illuminate\Http',                  // Web Actions — controllers, responses, requests
        'Illuminate\Pagination',            // LengthAwarePaginator in data actions
        'Illuminate\Contracts\Pagination',  // LengthAwarePaginator contract
        'Illuminate\Contracts\View',        // View return type in Web Actions
        'Illuminate\Validation',            // ValidationException in Upsert
        'Symfony\Component\HttpFoundation', // Response constants used by Laravel HTTP layer
        // Third-party
        'Spatie',
        'Spatie\LaravelData',
        'Spatie\Activitylog',
        'Spatie\MediaLibrary',
        'Spatie\SimpleExcel',
        'Spatie\Honeypot',
        'Spatie\Csp',
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
        // Laravel global helpers and facades used in domain
        'public_path',
        'base_path',
        'once',
        'auth',
        'response',
        'view',
        'route',
        'now',
        'abort',
        'resolve',
        'config',
        'app',
        'blank',
        'Illuminate\Filesystem',
        'Illuminate\Support\Facades\File',
        'Illuminate\Support\Facades\Storage',
        'Illuminate\Support\Facades\DB',
        'Illuminate\Support\Facades\Auth',
        'Illuminate\Support\Facades\Route',
        'ReflectionClass',
        'ReflectionProperty',
        'collect',
        'asset',
        'redirect',
        'today',
        'e',
        'class_uses_recursive',
        'str_contains',
        'str_starts_with',
        'array_values',
        'array_map',
        'array_filter',
        'array_keys',
        'sprintf',
        'implode',
        'explode',
        'trim',
        'strtolower',
        'number_format',
        'json_encode',
        'json_decode',
        'intval',
        'floatval',
        'is_null',
        'is_array',
        'is_string',
        'is_int',
        'count',
        'round',
        'filemtime',
        'file_exists',
        'scandir',
        'time',
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

// User y Asset son modelos de infraestructura compartida (como Authenticatable).
// Viven en sus módulos propios pero son usados cross-module legítimamente.
// Todos los demás cross-module imports son violaciones de arquitectura.
$sharedModels = [
    'App\Domain\Users\Models\User',
    'App\Domain\Assets\Models\Asset',
];

foreach ($modules as $module) {
    $others = array_filter($modules, fn (string $m): bool => $m !== $module);

    arch("el módulo {$module} no puede importar clases de otros módulos")
        ->expect("App\Domain\\{$module}")
        ->not->toUse(array_map(
            fn (string $m): string => "App\Domain\\{$m}",
            $others
        ))
        ->ignoring($sharedModels);
}

// =============================================================================
// 4. ACTIONS — contratos y responsabilidades
// =============================================================================

arch('core actions deben usar AsAction')
    ->expect($core)
    ->classes()
    ->toUse(AsAction::class);

arch('core actions deben declarar handle()')
    ->expect($core)
    ->classes()
    ->toHaveMethod('handle');

// SubTableAdapter subclasses inherit AsAction and handle() via abstract base — excluded from direct-use check.
$subTableAdapters = [
    \App\Domain\Shared\Web\Actions\SubTableAdapter::class,
    \App\Domain\Assets\Web\Adapters\Tabs\DocumentsTabAdapter::class,
    \App\Domain\Assets\Web\Adapters\Tabs\MaintenancesTabAdapter::class,
    \App\Domain\Assets\Web\Adapters\Tabs\AutomationsTabAdapter::class,
    \App\Domain\Assets\Web\Adapters\Tabs\MovementsTabAdapter::class,
    \App\Domain\Tickets\Web\Adapters\TasksAdapter::class,
];

arch('web adapters deben usar AsAction')
    ->expect($web)
    ->classes()
    ->toUse(AsAction::class)
    ->ignoring($subTableAdapters);

arch('web adapters deben declarar handle()')
    ->expect($web)
    ->classes()
    ->toHaveMethod('handle')
    ->ignoring($subTableAdapters);

arch('core actions no retornan View ni Response — solo DTOs')
    ->expect($core)
    ->not->toUse([
        'Illuminate\Contracts\View',
        'Illuminate\View',
    ]);

arch('core actions no declaran asController()')
    ->expect($core)
    ->not->toHaveMethod('asController');

arch('web adapters nunca importan Models directamente')
    ->expect($web)
    ->not->toUse('App\Domain\*\Models')
    ->ignoring($sharedModels);

// =============================================================================
// 5. HTMX ORCHESTRATOR — uso exclusivo en Web Actions
// =============================================================================

arch('HtmxOrchestrator solo puede usarse en Web Actions')
    ->expect(HtmxOrchestrator::class)
    ->toBeUsedIn($web)
    ->and->not->toBeUsedIn($core)
    ->and->not->toBeUsedIn($pipes)
    ->and->not->toBeUsedIn('App\Domain\*\Models')
    ->and->not->toBeUsedIn('App\Domain\*\Data')
    ->and->not->toBeUsedIn('App\Domain\*\ValueObjects')
    ->and->not->toBeUsedIn('App\Domain\*\Enums');

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

// Solo los TableData canónicos (App\Domain\{Module}\Data\TableData) requieren fromModel().
// Los sub-tabla DTOs (e.g. MaintenanceTableData, ItTableData) son schema-only para SchemaGenerator::toColumns()
// y no hidratan modelos, por lo que no necesitan fromModel().
arch('los TableData canónicos deben declarar fromModel() estático')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool => (string) $dto->getShortName() === 'TableData')
    ->toHaveMethod('fromModel');

// Sufijos semánticos permitidos en DTOs de módulo.
// Shared\Data contiene primitivos de configuración (Config, Column, Field, Tabs, ActionOption, PaginatedResult…)
// que son parte del framework interno — se excluyen del contrato de sufijos.
arch('no se permiten DTOs de módulo sin sufijo semántico')
    ->expect('App\Domain\*\Data')
    ->filter(fn ($dto): bool =>
        ! str_starts_with((string) $dto->getName(), 'App\Domain\Shared\Data\\') &&
        ! str_contains((string) $dto->getName(), 'Form') &&
        ! str_contains((string) $dto->getName(), 'Table') &&
        ! str_contains((string) $dto->getName(), 'Upsert') &&
        ! str_contains((string) $dto->getName(), 'Collection') &&
        ! str_contains((string) $dto->getName(), 'Filter') &&
        ! str_contains((string) $dto->getName(), 'Resource') &&
        ! str_contains((string) $dto->getName(), 'Payload') &&
        ! str_contains((string) $dto->getName(), 'Modal') &&
        ! str_contains((string) $dto->getName(), 'Sidebar') &&
        ! str_contains((string) $dto->getName(), 'Login') &&
        ! str_contains((string) $dto->getName(), 'Reset') &&
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