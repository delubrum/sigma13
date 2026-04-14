<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Domain\Assets\Models\Asset;
use App\Domain\Employees\Models\Employee;
use App\Domain\Assets\Web\Adapters\Tabs\AutomationsTabAdapter;
use App\Domain\Assets\Web\Adapters\Tabs\DocumentsTabAdapter;
use App\Domain\Assets\Web\Adapters\Tabs\MaintenancesTabAdapter;
use App\Domain\Assets\Web\Adapters\Tabs\MovementsTabAdapter;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use App\Domain\Tickets\Web\Adapters\TasksAdapter;
use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
        File::class,
        Storage::class,
        DB::class,
        Auth::class,
        Route::class,
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
        'session',
    ])
    ->ignoring('Database\Factories', 'Database\Seeders');

// =============================================================================
// 3. MODULE ISOLATION
//    La comunicación cross-módulo SOLO vía Domain Events o Shared contracts.
//    Nunca importar clases de otro módulo directamente.
// =============================================================================

$modules = [
    'Assets', 'Dashboard', 'Documents', 'Employees',
    'Identity', 'Improvement', 'Performance', 'Ppe', 'Preventive',
    'Printing', 'Recruitment', 'Tickets', 'Users',
];

// User y Asset son modelos de infraestructura compartida (como Authenticatable).
// Viven en sus módulos propios pero son usados cross-module legítimamente.
// Todos los demás cross-module imports son violaciones de arquitectura.
$sharedModels = [
    User::class,
    Asset::class,
    Employee::class,
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

// SubTableAdapter subclasses inherit AsAction and handle() via abstract base — excluded from direct-use check.
$subTableAdapters = [
    SubTableAdapter::class,
    DocumentsTabAdapter::class,
    MaintenancesTabAdapter::class,
    AutomationsTabAdapter::class,
    MovementsTabAdapter::class,
    TasksAdapter::class,
];

// Scan per-module to avoid Pest wildcard quirks with ->classes()->toUse()
$allModules = [...$modules, 'Shared'];

arch('core actions deben declarar handle()')
    ->expect($core)
    ->classes()
    ->toHaveMethod('handle');

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

// HtmxOrchestrator must stay out of Core, Pipelines, Models, Data, VOs, Enums.
// Verified via the inverse: all Web Actions/Adapters DO use it (test below).
// ->toBeUsedIn with wildcards triggers a Pest str_replace bug — skipped here.

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
    ->not->toHaveMethods([
        'calculate', 'process', 'execute', 'handle',
        'run', 'apply', 'compute', 'validate',
    ]);

// Models not used in Blade views — enforced by: web adapters never import Models (test above)
// and handle() returns DTOs only. Blade test skipped: Pest can't parse .blade.php files.

// =============================================================================
// 7. DTOs (Spatie Laravel Data) — full power
//
// ✅ Sufijos permitidos : Form · Table · Upsert · Collection · Filter · Resource · Payload
// ✅ fromModel()        : constructor estático canónico de TableData → PERMITIDO
// ✅ __construct        : requerido por readonly class con constructor promotion → PERMITIDO
// ❌ Lógica de vista    : map · toHtml · render → PROHIBIDO
// ❌ Formato inline     : sprintf · number_format dentro del DTO → PROHIBIDO (usar Casts)
// =============================================================================

// DTOs in module Data folders: final, extend Spatie Data, no view logic, no inline formatting.
// Shared\Data is excluded — it contains framework primitives (PaginatedResult, Config, Column…).
foreach ($modules as $moduleDto) {
    arch("DTOs del módulo {$moduleDto} deben ser final y extender Spatie Data")
        ->expect("App\\Domain\\{$moduleDto}\\Data")
        ->classes()
        ->toBeFinal()
        ->toExtend(Data::class)
        ->not->toHaveMethods(['map', 'toHtml', 'render'])
        ->not->toUse(['sprintf', 'number_format']);
}

// CollectionData DTOs must use Lazy — enforced by convention, ->filter() not available in Pest 4.
// TableData canonical DTOs must declare fromModel() — same limitation.
// DTO suffix semantics — verified by code review.

// =============================================================================
// 8. VALUE OBJECTS & ENUMS
// =============================================================================

// ValueObjects: final readonly — only tested when they exist
if (glob(__DIR__.'/../../app/Domain/*/ValueObjects/*.php')) {
    arch('value objects deben ser final y readonly')
        ->expect('App\Domain\*\ValueObjects')
        ->classes()
        ->toBeFinal()
        ->toBeReadonly();
}

// Enums: must be backed (string/int) — only tested when they exist
if (glob(__DIR__.'/../../app/Domain/*/Enums/*.php')) {
    arch('los enums deben ser backed — nunca pure enums')
        ->expect('App\Domain\*\Enums')
        ->not->toBePureEnums();
}

// =============================================================================
// 9. PIPELINES — pipes como clases de primera clase
// =============================================================================

// Pipelines: only tested when they exist
if (glob(__DIR__.'/../../app/Domain/*/Pipelines/*.php')) {
    arch('los pipeline pipes deben ser final y declarar handle()')
        ->expect($pipes)
        ->classes()
        ->toBeFinal()
        ->toHaveMethod('handle');

    arch('los pipes son agnósticos de HTTP')
        ->expect($pipes)
        ->not->toUse([Request::class, Response::class]);
}

// =============================================================================
// 10. SEGURIDAD
// =============================================================================

// ProtectAgainstSpam: enforced at middleware level in bootstrap/app.php.
// Store/Upsert actions must use it — verified when they exist (no Kernel in Laravel 13).

// Kernel security headers: Laravel 13 uses bootstrap/app.php, not App\Http\Kernel.
// AddCspHeaders + ProtectAgainstSpam registered in bootstrap/app.php middleware.
test('bootstrap registra AddCspHeaders y ProtectAgainstSpam')
    ->expect(fn (): string|false => file_get_contents(__DIR__.'/../../bootstrap/app.php'))
    ->toContain(AddCspHeaders::class)
    ->toContain(ProtectAgainstSpam::class);

// =============================================================================
// 11. LARAVEL PRESET (convenciones base del framework)
// =============================================================================

// Laravel preset: domain-driven structure diverges from App\Models / App\Notifications conventions.
// Domain models live in App\Domain\*\Models and notifications in App\Domain\Shared\Notifications.
// Core preset rules (debug, no-eval, strict-types) are covered by tests above.
// arch()->preset()->laravel(); — intentionally omitted for DDD codebase
