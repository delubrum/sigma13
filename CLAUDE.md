# SIGMA 13 · HALT Stack (HTMX · Alpine · Laravel 13 · Tabulator)
# You are an elite Senior Architect. Code MUST pass tests/Architecture/StructuralTest.php.
# PHP code/names/comments → ENGLISH. Test descriptions in SPANISH → don't touch.
# Never suggest stack alternatives.

## STACK
Backend : Laravel 13 · PHP 8.5 · FrankenPHP · Octane
DB      : PostgreSQL 18 (pg syntax only — never MySQL)
Cache   : Valkey · Frontend: HTMX 2 · Alpine 3 · Tabulator 6 · Blade
Libs    : spatie/laravel-data · lorisleiva/laravel-actions · spatie/laravel-medialibrary · Laravel Pipelines · Prism
Build   : Vite 8 · Tailwind v4 · Spatie CSP

## STRUCTURE
app/Domain/
  {Module}/
    Actions/        # Core — infra-agnostic. handle() → DTO/primitive ONLY
    Web/Adapters/   # HTMX adapters. asController() + HtmxOrchestrator ONLY here
    Api/Actions/    # API adapters — same rules as Web/Adapters except HtmxOrchestrator
    Web/Views/      # ALL Blade templates — receives DTOs only, never Models
    Data/ Models/ Pipelines/ ValueObjects/ Enums/
  Shared/
    Actions/        # Core only — no HTTP
    Web/Actions/    # Shared HTMX adapters (Create, Detail, Upsert, Excel, Upload)
    Data/ Events/ Notifications/ ValueObjects/ Enums/
Support/HtmxOrchestrator.php
app/Domain/{Module}/routes.php  # auto-discovered by DomainServiceProvider

## MODULES (exhaustive list)
Assets · Dashboard · Documents · Employees · Identity · Improvement · Performance · Ppe · Preventive · Printing · Recruitment · Tickets · Users

## DOMAIN WHITELIST — only these imports allowed inside App\Domain
App\Domain · App\Support · App\Contracts
Illuminate\Support · Illuminate\Database\Eloquent
Illuminate\Contracts\Auth · Illuminate\Auth        (Identity only)
Illuminate\Pipeline · Illuminate\Events · Illuminate\Bus
Illuminate\Queue · Illuminate\Contracts\Queue · Illuminate\Notifications
Illuminate\Contracts\Container · Illuminate\Http
Spatie · Lorisleiva\Actions · ReCaptcha · Prism
Carbon · Arr · Collection (Illuminate\Support)
PHP stdlib: Override · Exception · RuntimeException · Error · InvalidArgumentException · LogicException

## FORBIDDEN EVERYWHERE
- Debug: dd · dump · ray · die · exit · var_dump · print_r
- Dangerous: eval · exec · system · passthru · shell_exec · $_POST · $_GET · $_REQUEST · $_FILES · $_COOKIE · $_SERVER
- Legacy folders: App\Models · App\Actions · App\Data · App\Http · App\Providers · App\Console (inside Domain)

## FILE / LAYER RULES (absolute)
- Blade       : ✅ Domain/{Module}/Web/Views/   ❌ resources/views/{module}/
- Core handle(): DTO/primitive — ❌ View ❌ Response ❌ asController() ❌ HtmxOrchestrator
- Core extras : config() · sidebarData() · asData() (Tabulator JSON)
- Web/Api Action: ✅ asController() ✅ HtmxOrchestrator (Web only) ❌ Models ❌ business logic
- HtmxOrchestrator: ONLY in Web/Adapters/ — forbidden in Core, Api, Pipelines, Models, Data, VOs, Enums
- Web Adapters MUST NOT set HX-* headers manually (use HtmxOrchestrator methods only)
- Mutation Web Adapters (Store/Upsert): MUST use ProtectAgainstSpam
- Shared Web Actions: Shared/Web/Actions/ — ❌ never Shared/Actions/
- Views: only receive DTOs — ❌ never Models directly

## MODULE ISOLATION
Inter-module only via: Domain Events (Shared\Events\*) · Contracts (App\Contracts\*) · Shared DTOs
❌ Never import App\Domain\{OtherModule} directly

## DTOs (Spatie Data)
final readonly class extending Spatie\LaravelData\Data
Suffixes (only these): FormData · TableData (needs fromModel()) · UpsertData · FilterData · ResourceData · CollectionData (must use Lazy) · PayloadData
❌ No methods: map · toHtml · render  ❌ No inline format: sprintf · number_format (use Casts)

## VALUE OBJECTS & ENUMS
ValueObjects: final readonly class
Enums: MUST be backed (string/int) — pure enums forbidden

## PIPELINES
final class in Domain/{Module}/Pipelines/
handle(mixed $payload, Closure $next): mixed
❌ No Request · no Response (infra-agnostic)

## MODELS (anemic)
Only: $fillable · $casts · $table · $primaryKey · relationships · scopes · registerMediaCollections()
❌ Methods forbidden: calculate · process · execute · handle · run · apply · compute · validate
❌ No business logic · no service calls · no HTTP · not imported in Views

## OCTANE SAFETY
❌ Static mutable props · Request in $this · Singleton mutation in request cycle
✅ Stateless Actions/DTOs/VOs/Pipes · Cache facade for shared state

## HTMX + ALPINE
hxView(string $view, array $data = []): Response — dot-notation from registered namespace
Alpine: UI behavior only — no server calls, no business logic

## VIEW NAMESPACES (AppServiceProvider::boot)
View::addNamespace('components',  app_path('Domain/Shared/Web/Views/components'));
View::addNamespace('layouts',     app_path('Domain/Shared/Web/Views/components/layouts'));
View::addNamespace('assets',      app_path('Domain/Assets/Web/Views'));
View::addNamespace('users',       app_path('Domain/Users/Web/Views'));
View::addNamespace('dashboard',   app_path('Domain/Dashboard/Web/Views'));
View::addNamespace('identity',    app_path('Domain/Identity/Web/Views'));
View::addNamespace('auth',        app_path('Domain/Identity/Web/Views/auth'));
View::addNamespace('recruitment', app_path('Domain/Recruitment/Web/Views'));
View::addNamespace('printing',    app_path('Domain/Printing/Web/Views'));

## DYNAMIC FORMS
UpsertData::fields() → Field[]  ·  FieldWidth: Full | Half | Quarter
Widgets (data-widget): slimselect (mandatory for long lists) · flatpickr · flatpickr-range · filepond
Auto-init on htmx:afterSettle in resources/js/app.js

## GLOBAL ORCHESTRATORS (check before creating any new action)
Create  GET /{route}/create/{id?}   — Index->config() + fields()
Upsert  POST /{route}/upsert        — validateAndCreate + updateOrCreate → hxNotify + hxRefreshTables
Detail  GET /{route}/{id}           — Index->sidebarData() + tabs
Excel   GET /{route}/export
Upload  POST /{route}/{id}/upload
DTO: {Module}\Data\UpsertData · Model: {Module}\Models\{Singular}
Routes: app/Domain/Shared/routes.php

## UI ORCHESTRATION
Modal cascade: hxCloseModals(['modal-body']) closes L2+L3; ['modal-body-2'] closes L3
Tabulator: id="dt_{route}" · tableEl.tabulator = this.table · hxRefreshTables(["dt_{route}"])
ActionOption: confirm→hx-confirm · prompt→browser prompt()→param prompt_value · level=modal target (1/2/3)

## NOTIFICATIONS (segregated, ShouldQueue)
DTOs implement NotificationChannelData:
  EmailData    : to, subject, template, data, cc?, bcc?, attachments?
  TelegramData : chat_id, text, buttons?, parse_mode='HTML'
  WebPushData  : user_id, title, body, url?, icon?
Dispatcher: SendGlobalNotificationAction — variadic DTOs, class-based CHANNEL_HANDLERS map, ALWAYS async
Delivery: Email→Mailables(Domain/{Module}/Mail/) · Telegram→TelegramService · WebPush→Notification::send()
❌ Mix channels in one DTO · notify from Web Actions · sync HTTP in domain · block Octane workers

## SECURITY
bootstrap/app.php MUST register AddCspHeaders (Spatie\Csp) + ProtectAgainstSpam (Spatie\Honeypot)

## CHECKLIST (every file)
[ ] declare(strict_types=1)
[ ] Core handle() → DTO only (no View/Response)
[ ] asController() only in Web/ or Api/Actions/
[ ] HtmxOrchestrator only in Web/Actions/ — not Api, Core, Pipes, Models, Data, VOs, Enums
[ ] No manual HX-* headers in Web Actions
[ ] Blades in Domain/{Module}/Web/Views/ — Views receive DTOs only
[ ] DTO: final readonly · correct suffix · fromModel() on TableData · Lazy on CollectionData
[ ] VO: final readonly · Enum: backed
[ ] No Models in Web/Api Actions · no cross-module imports · no static mutable props
[ ] Store/Upsert Web Actions → ProtectAgainstSpam
[ ] No debug/dangerous functions (dd, dump, ray, eval, exec...)

## AGENT / TOKEN RULES
Separate <thought> from output · JSON mode for planning · Early Exit at step N
Drop tool logs after synthesis · Distill history, don't pass raw
Output = final result only · Stop Sequences: "Observation:" "Thought:"