# ASSETS MODULE - Complete File Mapping
# SIGMA Project - HALT Stack (HTMX 2.x, Alpine.js, Laravel 13, Tailwind 4)
# ================================================================

# =============================================================================
# 1. ACTIONS - Assets Module
# =============================================================================

# --- app/Actions/Assets/Index.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Assets;

use App\Contracts\HasSidebar;
use App\Contracts\HasTabs;
use App\Data\Assets\Sidebar;
use App\Data\Assets\Table;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\Tabs;
use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index implements HasSidebar, HasTabs
{
    use AsAction;

    public function config(): Config
    {
        return new Config(
            moduleTitle: 'Activos',
            newButtonLabel: 'Nuevo Activo',
            newIcon: 'ri-list-view',
            modalTitle: 'Activos',
            modalSubtitle: 'Registro de activos tecnológicos',
            modalIcon: 'ri-stack-line',
            columns: [
                ['title' => 'ID', 'field' => 'id', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Área', 'field' => 'area', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'SAP', 'field' => 'sap', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Serial', 'field' => 'serial', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Responsable', 'field' => 'assignee', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Hostname', 'field' => 'hostname', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Marca', 'field' => 'brand', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Modelo', 'field' => 'model', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Tipo', 'field' => 'kind', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'CPU', 'field' => 'cpu', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'RAM', 'field' => 'ram', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'SSD', 'field' => 'ssd', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'HDD', 'field' => 'hdd', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'S.O.', 'field' => 'so', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Precio', 'field' => 'price', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Fecha', 'field' => 'date', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Factura', 'field' => 'invoice', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Proveedor', 'field' => 'supplier', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Garantía', 'field' => 'warranty', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Modo Trabajo', 'field' => 'work_mode', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Ubicación', 'field' => 'location', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Teléfono', 'field' => 'phone', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Operador', 'field' => 'operator', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Estado', 'field' => 'status', 'formatter' => 'html', 'headerHozAlign' => 'center', 'hozAlign' => 'center', 'headerFilter' => 'list', 'headerFilterParams' => ['values' => ['available' => 'Disponible', 'assigned' => 'Asignado', 'maintenance' => 'Mantenimiento', 'retired' => 'Retirado'], 'clearable' => true], 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Clasificación', 'field' => 'classification', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Conf.', 'field' => 'confidentiality', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Int.', 'field' => 'integrity', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Disp.', 'field' => 'availability', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Criticidad', 'field' => 'criticality', 'formatter' => 'html', 'headerHozAlign' => 'center', 'hozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ],
            formFields: [
                new Field(name: 'area', label: 'Área', required: true, placeholder: 'Área o departamento'),
                new Field(name: 'hostname', label: 'Hostname', required: true, placeholder: 'Nombre del equipo'),
                new Field(name: 'serial', label: 'Serial', required: true, placeholder: 'Número de serie'),
                new Field(name: 'brand', label: 'Marca', required: true, placeholder: 'Marca del equipo'),
                new Field(name: 'model', label: 'Modelo', required: true, placeholder: 'Modelo del equipo'),
                new Field(name: 'kind', label: 'Tipo', required: true, placeholder: 'Tipo de activo'),
                new Field(name: 'cpu', label: 'CPU', required: false, placeholder: 'Procesador'),
                new Field(name: 'ram', label: 'RAM', required: false, placeholder: 'Memoria RAM'),
                new Field(name: 'ssd', label: 'SSD', required: false, placeholder: 'Capacidad SSD'),
                new Field(name: 'hdd', label: 'HDD', required: false, placeholder: 'Capacidad HDD'),
                new Field(name: 'so', label: 'S.O.', required: false, placeholder: 'Sistema Operativo'),
                new Field(name: 'sap', label: 'Código SAP', required: false, placeholder: 'ID SAP del activo'),
                new Field(name: 'price', label: 'Precio', required: false, placeholder: '0.00'),
                new Field(name: 'date', label: 'Fecha Compra', required: false, placeholder: 'AAAA-MM-DD'),
                new Field(name: 'invoice', label: 'Factura', required: false, placeholder: 'Número de factura'),
                new Field(name: 'supplier', label: 'Proveedor', required: false, placeholder: 'Nombre del proveedor'),
                new Field(name: 'warranty', label: 'Garantía', required: false, placeholder: 'Meses o fecha'),
                new Field(name: 'status', label: 'Estado', required: true, placeholder: 'assigned, storage, retired'),
                new Field(name: 'classification', label: 'Clasificación', required: false, placeholder: 'Categoría'),
                new Field(name: 'confidentiality', label: 'Confidencialidad', required: false, placeholder: '1-3'),
                new Field(name: 'integrity', label: 'Integridad', required: false, placeholder: '1-3'),
                new Field(name: 'availability', label: 'Disponibilidad', required: false, placeholder: '1-3'),
                new Field(name: 'location', label: 'Ubicación', required: false, placeholder: 'Sede o puesto'),
                new Field(name: 'phone', label: 'Teléfono', required: false, placeholder: 'Extensión o móvil'),
                new Field(name: 'work_mode', label: 'Modalidad', required: false, placeholder: 'Presencial, Remoto'),
                new Field(name: 'url', label: 'URL Docs', required: false, placeholder: 'Link documentación'),
                new Field(name: 'operator', label: 'Operador', required: false, placeholder: 'Nombre del operador'),
            ],
        );
    }

    public function asController(Request $request): View
    {
        return view('components.index', [
            'route' => 'assets',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 15));
        $offset = ($page - 1) * $size;

        $query = Asset::query()->with(['currentAssignment.employee']);

        if ($request->has('filter')) {
            foreach ((array) $request->input('filter') as $f) {
                $field = $f['field'] ?? null;
                $value = $f['value'] ?? null;

                if (empty($value) && $value !== '0') {
                    continue;
                }

                match ($field) {
                    'area', 'hostname', 'serial', 'sap', 'brand', 'model', 'kind',
                    'cpu', 'ram', 'ssd', 'hdd', 'so', 'invoice', 'supplier',
                    'location', 'operator', 'classification', 'work_mode', 'phone' => $query->where($field, 'ilike', "%{$value}%"),

                    'status' => $query->where('status', $value),
                    'confidentiality', 'integrity', 'availability' => $query->where($field, (int) $value),

                    'date', 'acquisition_date' => str_contains((string) $value, ' to ')
                        ? $query->whereBetween('acquisition_date', explode(' to ', (string) $value))
                        : $query->whereDate('acquisition_date', $value),

                    'assignee' => $query->whereHas('currentAssignment.employee', function ($q) use ($value): void {
                        $q->where('name', 'ilike', "%{$value}%");
                    }),

                    default => null
                };
            }
        }

        $sort = collect((array) $request->input('sorters'))->first() ?? ['field' => 'id', 'dir' => 'desc'];
        $sortDir = in_array(strtolower($sort['dir'] ?? ''), ['asc', 'desc']) ? $sort['dir'] : 'desc';

        if ($sort['field'] === 'criticality') {
            $query->orderByRaw('(confidentiality + integrity + availability) '.$sortDir);
        } elseif ($sort['field'] === 'assignee') {
            $query->orderBy('id', $sortDir);
        } else {
            $query->orderBy($sort['field'] ?? 'id', $sortDir);
        }

        $total = $query->count();
        $rows = $query->offset($offset)
            ->limit($size)
            ->get()
            ->map(fn (Asset $asset): Table => Table::fromModel($asset));

        return response()->json($rows->all())
            ->header('X-Page-Count', (string) ceil($total / $size))
            ->header('X-Total-Rows', (string) $total);
    }

    public function sidebarData(int $id): Sidebar
    {
        $asset = Asset::with('currentAssignment.employee')->findOrFail($id);

        return Sidebar::fromModel($asset);
    }

    public function sidebarView(): string
    {
        return 'assets.sidebar';
    }

    public function tabs(): array
    {
        return [
            new Tabs(key: 'details', label: 'Detalles', icon: 'ri-information-line', route: 'assets.details', default: true),
            new Tabs(key: 'assignments', label: 'Asignaciones', icon: 'ri-user-line', route: 'assets.assignments'),
            new Tabs(key: 'returns', label: 'Retornos', icon: 'ri-arrow-go-back-line', route: 'assets.returns'),
            new Tabs(key: 'documents', label: 'Documentos', icon: 'ri-file-line', route: 'assets.documents'),
            new Tabs(key: 'maintenances', label: 'Mantenimientos', icon: 'ri-tools-line', route: 'assets.maintenances'),
        ];
    }
}

# --- app/Actions/Assets/PrintQr.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Assets;

use App\Data\Assets\Sidebar;
use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class PrintQr
{
    use AsAction;

    public function handle(int $id): View
    {
        $asset = Asset::findOrFail($id);
        $data = Sidebar::fromModel($asset);

        return view('assets.print-qr', ['data' => $data]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}

# --- app/Actions/Assets/Tabs/Details.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Details
{
    use AsAction;

    public function handle(int $id): View
    {
        $asset = Asset::with('currentAssignment.employee')->findOrFail($id);

        return view('assets.tabs.details', [
            'asset' => $asset,
        ]);
    }

    public function asTab(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}

# --- app/Actions/Assets/Tabs/Assignments.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use App\Models\AssetEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Assignments
{
    use AsAction;

    public function handle(int $id): View
    {
        $asset = Asset::with('currentAssignment.employee')->findOrFail($id);

        $assignments = AssetEvent::query()
            ->where('asset_id', $id)
            ->where('kind', 'assignment')
            ->with(['employee'])
            ->orderByDesc('id')
            ->get()
            ->map(function (AssetEvent $event) {
                return [
                    'id' => $event->id,
                    'date' => $event->created_at?->format('d/m/Y H:i') ?? '---',
                    'assignee' => $event->employee?->name ?? '---',
                    'hardware' => is_array($event->hardware) ? implode(', ', $event->hardware) : ($event->hardware ?? '---'),
                    'software' => is_array($event->software) ? implode(', ', $event->software) : ($event->software ?? '---'),
                    'minute' => $event->notes ? '<span class="text-emerald-600"><i class="ri-file-text-line"></i> Adjunto</span>' : '<span class="opacity-30">---</span>',
                    'asset_id' => $event->asset_id,
                ];
            });

        return view('assets.tabs.assignments', [
            'asset' => $asset,
            'assignments' => $assignments,
        ]);
    }

    public function asTab(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}

# --- app/Actions/Assets/Modals/CreateAssignment.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Assets\Modals;

use App\Data\Assets\Modals\Assignment;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Models\Asset;
use App\Models\AssetEvent;
use App\Models\Employee;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateAssignment
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(int $id): Config
    {
        $employees = Employee::orderBy('name')->get()
            ->mapWithKeys(fn ($e) => [$e->id => "{$e->id} || {$e->name}"]);

        return new Config(
            moduleTitle: 'Nueva Asignación',
            newButtonLabel: 'Nueva Asignación',
            newIcon: 'ri-user-add-line',
            modalTitle: 'Asignación',
            modalSubtitle: 'Registro de asignación de activo',
            modalIcon: 'ri-user-add-line',
            modalWidth: '50%',
            columns: [],
            formFields: [
                new Field(
                    name: 'employee_id',
                    label: 'Responsable',
                    required: true,
                    placeholder: 'Seleccionar empleado',
                    type: 'select',
                    options: $employees->all(),
                ),
                new Field(
                    name: 'hardware',
                    label: 'Hardware',
                    required: false,
                    placeholder: 'Base, Teclado, Mouse, etc. (separado por comas)',
                    type: 'text',
                    hint: 'Separar múltiples items con comas',
                ),
                new Field(
                    name: 'software',
                    label: 'Software',
                    required: false,
                    placeholder: 'Office 365, Autodesk, etc. (separado por comas)',
                    type: 'text',
                    hint: 'Separar múltiples items con comas',
                ),
                new Field(
                    name: 'notes',
                    label: 'Acta / Notas',
                    required: false,
                    placeholder: 'Observaciones de la asignación',
                    type: 'textarea',
                ),
            ],
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config($id);

        $this->hxModalHeader([
            'icon' => $config->modalIcon,
            'title' => $config->modalTitle,
            'subtitle' => $config->modalSubtitle,
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route' => "assets/{$id}/assignments",
            'config' => $config,
        ]);
    }

    public function asController(Request $request, int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'hardware' => 'nullable|string',
            'software' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data = Assignment::fromRequest($request->all());

        AssetEvent::create([
            'kind' => 'assignment',
            'asset_id' => $id,
            'employee_id' => $data->employee_id,
            'hardware' => $data->hardware,
            'software' => $data->software,
            'notes' => $data->notes,
            'user_id' => Auth::id(),
        ]);

        Asset::where('id', $id)->update(['status' => 'assigned']);

        $this->hxNotify('Asignación creada correctamente');
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}

# =============================================================================
# 2. MODELS
# =============================================================================

# --- app/Models/Asset.php ---
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'area', 'hostname', 'serial', 'brand', 'model', 'kind', 'cpu', 'ram',
    'ssd', 'hdd', 'so', 'sap', 'price', 'acquisition_date', 'invoice', 'supplier',
    'warranty', 'status', 'classification', 'confidentiality',
    'integrity', 'availability', 'location', 'phone', 'work_mode',
    'url', 'operator',
])]
class Asset extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'price' => 'decimal:2',
            'confidentiality' => 'integer',
            'integrity' => 'integer',
            'availability' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile')
            ->singleFile()
            ->useDisk('s3');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->nonQueued();
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetEvent::class, 'asset_id')
            ->where('kind', 'assignment')
            ->latestOfMany();
    }

    protected function getAssigneeNameAttribute(): ?string
    {
        if ($this->status !== 'assigned') {
            return null;
        }

        return $this->currentAssignment?->employee?->name;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class, 'asset_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AssetEvent::class, 'asset_id')->orderByDesc('id');
    }
}

# --- app/Models/AssetEvent.php ---
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'kind',
    'asset_id',
    'employee_id',
    'software',
    'hardware',
    'notes',
    'created_at',
    'user_id',
    'wipe',
    'expiry',
])]
#[Hidden(['user_id'])]
class AssetEvent extends Model
{
    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'expiry' => 'date',
            'asset_id' => 'integer',
            'employee_id' => 'integer',
            'user_id' => 'integer',
            'hardware' => 'array',
            'software' => 'array',
            'wipe' => 'boolean',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

# --- app/Models/Employee.php ---
<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable([
    'name',
    'profile',
    'email',
    'phone',
    'department',
    'position',
    'status',
    'hire_date',
    'salary',
    'user_id',
])]
#[Hidden(['user_id'])]
class Employee extends Authenticatable
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assetEvents()
    {
        return $this->hasMany(AssetEvent::class, 'employee_id');
    }
}

# =============================================================================
# 3. DATA / DTOs - Assets
# =============================================================================

# --- app/Data/Assets/Table.php ---
<?php

declare(strict_types=1);

namespace App\Data\Assets;

use App\Models\Asset;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $area,
        public readonly ?string $sap,
        public readonly ?string $serial,
        public readonly ?string $assignee,
        public readonly ?string $hostname,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly ?string $kind,
        public readonly ?string $cpu,
        public readonly ?string $ram,
        public readonly ?string $ssd,
        public readonly ?string $hdd,
        public readonly ?string $so,
        public readonly ?string $invoice,
        public readonly ?string $supplier,
        public readonly ?string $warranty,
        public readonly ?string $work_mode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly ?string $operator,
        public readonly ?string $classification,
        public readonly ?int $confidentiality,
        public readonly ?int $integrity,
        public readonly ?int $availability,
        // Procesados
        public readonly string $criticality,
        public readonly string $price,
        public readonly string $date,
        public readonly string $status,
    ) {}

    public static function fromModel(Asset $asset): self
    {
        $score = ($asset->confidentiality ?? 0) + ($asset->integrity ?? 0) + ($asset->availability ?? 0);

        $color = match (true) {
            $score >= 8 => 'border-red-500 text-red-500',
            $score >= 5 => 'border-orange-500 text-orange-500',
            default => 'border-sigma-b text-sigma-tx2',
        };

        return new self(
            id: $asset->id,
            area: $asset->area,
            sap: $asset->sap ?? '—',
            serial: $asset->serial,
            assignee: $asset->assignee_name ?? '—',
            hostname: $asset->hostname,
            brand: $asset->brand,
            model: $asset->model,
            kind: $asset->kind,
            cpu: $asset->cpu,
            ram: $asset->ram,
            ssd: $asset->ssd,
            hdd: $asset->hdd,
            so: $asset->so,
            invoice: $asset->invoice,
            supplier: $asset->supplier,
            warranty: $asset->warranty,
            work_mode: $asset->work_mode,
            location: $asset->location,
            phone: $asset->phone,
            operator: $asset->operator,
            classification: $asset->classification,
            confidentiality: $asset->confidentiality,
            integrity: $asset->integrity,
            availability: $asset->availability,
            criticality: sprintf('<span class="px-2 py-0.5 rounded border %s bg-sigma-bg2 font-bold text-[10px]">%d</span>', $color, $score),
            price: '$'.number_format((float) $asset->price, 2),
            date: $asset->acquisition_date?->format('d/m/Y') ?? '—',
            status: sprintf(
                '<span class="px-2 py-0.5 rounded border %s bg-sigma-bg2 font-bold uppercase text-[10px]">%s</span>',
                $asset->status === 'assigned' ? 'border-sigma-b text-sigma-tx' : 'border-dashed opacity-50',
                $asset->status
            ),
        );
    }
}

# --- app/Data/Assets/Sidebar.php ---
<?php

declare(strict_types=1);

namespace App\Data\Assets;

use App\Models\Asset;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $serial,
        public readonly string $sap,
        public readonly ?string $hostname,
        public readonly ?string $workMode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly string $status,
        public readonly ?string $assignee,
        public readonly ?string $assignedAt,
        public readonly ?string $photoUrl,
        public readonly ?string $qrUrl,
    ) {}

    public static function fromModel(Asset $asset): self
    {
        return new self(
            id: $asset->id,
            serial: $asset->serial,
            sap: $asset->sap,
            hostname: $asset->hostname,
            workMode: $asset->work_mode,
            location: $asset->location,
            phone: $asset->phone,
            status: $asset->status,
            assignee: $asset->assignee_name,
            assignedAt: $asset->assigned_at?->format('Y-m-d'),
            photoUrl: $asset->url,
            qrUrl: route('detail', ['route' => 'assets', 'id' => $asset->id]),
        );
    }
}

# --- app/Data/Assets/Modals/Assignment.php ---
<?php

declare(strict_types=1);

namespace App\Data\Assets\Modals;

final class Assignment
{
    public function __construct(
        public readonly int $employee_id,
        /** @var string[] */
        public readonly array $hardware,
        /** @var string[] */
        public readonly array $software,
        public readonly ?string $notes,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            employee_id: (int) ($data['employee_id'] ?? 0),
            hardware: isset($data['hardware']) && is_string($data['hardware'])
                ? array_map('trim', explode(',', $data['hardware']))
                : [],
            software: isset($data['software']) && is_string($data['software'])
                ? array_map('trim', explode(',', $data['software']))
                : [],
            notes: $data['notes'] ?? null,
        );
    }
}

# =============================================================================
# 4. DATA / DTOs - Shared
# =============================================================================

# --- app/Data/Shared/Config.php ---
<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Spatie\LaravelData\Data;

final class Config extends Data
{
    public function __construct(
        public readonly string $moduleTitle,
        public readonly bool $showKpi = false,
        public readonly string $newButtonLabel = 'Nuevo',
        public readonly string $newIcon = 'ri-add-line',
        public readonly string $modalTitle = '',
        public readonly string $modalSubtitle = '',
        public readonly string $modalIcon = 'ri-file-line',
        public readonly string $modalWidth = '50%',
        public readonly array $columns = [],
        public readonly array $formFields = [],
    ) {}
}

# --- app/Data/Shared/Field.php ---
<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Spatie\LaravelData\Data;

final class Field extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type = 'text',
        public readonly bool $required = false,
        public readonly string $placeholder = '',
        public readonly ?string $hint = null,
        public readonly array $options = [],

        /** 'flatpickr' | 'flatpickr-range' | 'slimselect' | 'filepond' | null */
        public readonly ?string $widget = null,

        /** Columnas del grid que ocupa: 1 | 2 | 3 | 4 */
        public readonly int $cols = 1,
    ) {}
}

# --- app/Data/Shared/Tabs.php ---
<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Spatie\LaravelData\Data;

final class Tabs extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly string $icon,
        public readonly string $route,
        public readonly bool $default = false,
    ) {}
}

# =============================================================================
# 5. CONTRACTS
# =============================================================================

# --- app/Contracts/HasSidebar.php ---
<?php

declare(strict_types=1);

namespace App\Contracts;

use Spatie\LaravelData\Data;

interface HasSidebar
{
    public function sidebarData(int $id): Data;

    public function sidebarView(): string;
}

# --- app/Contracts/HasTabs.php ---
<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\Shared\Tabs;

interface HasTabs
{
    /**
     * @return array<Tabs>
     */
    public function tabs(): array;
}

# =============================================================================
# 6. SUPPORT
# =============================================================================

# --- app/Support/HtmxOrchestrator.php ---
<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HtmxOrchestrator
{
    /** @var array<string, mixed> */
    protected array $hxTriggers = [];

    public function hxNotify(string $message, string $type = 'success'): static
    {
        $this->hxTriggers['notify'] = ['type' => $type, 'message' => $message];

        return $this;
    }

    /** @param string[] $ids */
    public function hxCloseModals(array $ids): static
    {
        $this->hxTriggers['close-modals'] = ['ids' => $ids];

        return $this;
    }

    /** @param string[] $ids */
    public function hxRefresh(array $ids): static
    {
        $this->hxTriggers['refresh-divs'] = ['ids' => $ids];

        return $this;
    }

    /** @param string[] $ids */
    public function hxRefreshTables(array $ids): static
    {
        $this->hxTriggers['refresh-tables'] = ['ids' => $ids];

        return $this;
    }

    /** @param array{icon: string, title: string, subtitle: string} $header */
    public function hxModalHeader(array $header): static
    {
        $this->hxTriggers['update-modal-header'] = $header;

        return $this;
    }

    public function hxModalWidth(string $width): static
    {
        $this->hxTriggers['set-modal-width'] = ['width' => $width];

        return $this;
    }

    /** @param array<string, mixed> $data */
    public function hxResponse(array $data = [], int $status = 200): JsonResponse
    {
        $notify = $this->hxTriggers['notify'] ?? null;
        if ($notify !== null && is_array($notify) && $data === []) {
            $data = ['message' => $notify['message'] ?? ''];
        }

        return response()->json($data, $status)->withHeaders([
            'HX-Trigger' => json_encode($this->hxTriggers),
        ]);
    }

    /** @param View $view */
    public function hxView($view): Response
    {
        return response($view->render())->withHeaders([
            'HX-Trigger' => json_encode($this->hxTriggers),
        ]);
    }

    public function hxRedirect(string $to): JsonResponse
    {
        return response()->json(['redirect' => $to])->withHeaders([
            'HX-Redirect' => $to,
            'HX-Trigger' => json_encode($this->hxTriggers),
        ]);
    }
}

# =============================================================================
# 7. SHARED ACTIONS
# =============================================================================

# --- app/Actions/Shared/Create.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Shared;

use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Create
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route): View
    {
        $folder = Str::studly($route);
        $indexAction = "App\\Actions\\{$folder}\\Index";

        if (! class_exists($indexAction)) {
            abort(404, 'Módulo no encontrado.');
        }

        $config = resolve($indexAction)->config();

        $this->hxModalHeader([
            'icon' => $config->modalIcon,
            'title' => $config->modalTitle ?: $config->newButtonLabel,
            'subtitle' => $config->modalSubtitle ?: 'Registro',
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route' => $route,
            'config' => $config,
        ]);
    }

    public function asController(Request $request, string $route): Response
    {
        return $this->hxView($this->handle($route));
    }
}

# --- app/Actions/Shared/Detail.php ---
<?php

declare(strict_types=1);

namespace App\Actions\Shared;

use App\Contracts\HasSidebar;
use App\Contracts\HasTabs;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, int $id): View
    {
        $folder = Str::studly($route);
        $indexAction = "App\\Actions\\{$folder}\\Index";

        if (! class_exists($indexAction)) {
            abort(404, 'Módulo no encontrado.');
        }

        /** @var object $index */
        $index = resolve($indexAction);
        $config = $index->config();

        $sidebarView = null;
        $sidebarData = null;

        if ($index instanceof HasSidebar) {
            $sidebarView = $index->sidebarView();
            $sidebarData = $index->sidebarData($id);
        }

        $tabs = $index instanceof HasTabs ? $index->tabs() : [];
        $defaultTab = collect($tabs)->firstWhere('default', true);

        $this->hxModalHeader([
            'icon' => $config->modalIcon,
            'title' => $config->modalTitle ?: $config->moduleTitle,
            'subtitle' => $config->modalSubtitle ?: 'Detalle',
        ]);

        $this->hxModalWidth('98%');

        return view('components.detail-modal', [
            'route' => $route,
            'config' => $config,
            'id' => $id,
            'sidebarView' => $sidebarView,
            'sidebarData' => $sidebarData,
            'tabs' => $tabs,
            'defaultTab' => $defaultTab,
        ]);
    }

    public function asController(Request $request, string $route, int $id): Response
    {
        return $this->hxView($this->handle($route, $id));
    }
}

# =============================================================================
# 8. ROUTES
# =============================================================================

# --- routes/modules/assets.php ---
<?php

declare(strict_types=1);

use App\Actions\Assets\Index;
use App\Actions\Assets\Modals\CreateAssignment;
use App\Actions\Assets\PrintQr;
use App\Actions\Assets\Tabs\Assignments;
use App\Actions\Assets\Tabs\Automations;
use App\Actions\Assets\Tabs\Details;
use App\Actions\Assets\Tabs\Documents;
use App\Actions\Assets\Tabs\Maintenances;
use App\Actions\Assets\Tabs\Preventive;
use App\Actions\Assets\Tabs\Returns;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('assets')->name('assets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/{id}/print-qr', PrintQr::class)->name('print-qr');

    // Tabs
    Route::get('/{id}/details', [Details::class, 'asTab'])->name('details');
    Route::get('/{id}/assignments', [Assignments::class, 'asTab'])->name('assignments');
    Route::get('/{id}/assignments/create', [CreateAssignment::class, 'asController'])->name('assignments.create');
    Route::post('/{id}/assignments', [CreateAssignment::class, 'asStore'])->name('assignments.store');
    Route::get('/{id}/returns', [Returns::class, 'asTab'])->name('returns');
    Route::get('/{id}/documents', [Documents::class, 'asTab'])->name('documents');
    Route::get('/{id}/automations', [Automations::class, 'asTab'])->name('automations');
    Route::get('/{id}/preventive', [Preventive::class, 'asTab'])->name('preventive');
    Route::get('/{id}/maintenances', [Maintenances::class, 'asTab'])->name('maintenances');
});

# =============================================================================
# 9. VIEWS - Assets-specific
# =============================================================================

# --- resources/views/assets/sidebar.blade.php ---
<div class="relative h-52 overflow-hidden" style="background:var(--bg3)">

    <div id="asset_photo_preview" class="w-full h-full">
        @if($data->photoUrl)
            <img src="{{ $data->photoUrl }}?t={{ time() }}"
                 class="w-full h-full object-cover">
        @else
            <div class="flex flex-col items-center justify-center h-full gap-2">
                <canvas id="asset-qr-{{ $data->id }}" class="w-36 h-36 opacity-60"></canvas>
                <span class="text-[9px] font-black uppercase tracking-widest italic"
                      style="color:var(--tx2)">Tocar para subir foto</span>
            </div>
        @endif
    </div>

    <input type="file"
           class="absolute inset-0 opacity-0 cursor-pointer z-10"
           onchange="sigmaUpload(this)"
           data-url="{{ route('shared.upload', ['route' => 'assets', 'id' => $data->id]) }}"
           data-target="#asset_photo_preview"
           name="photo">

    <div id="asset_upload_loader"
         class="htmx-indicator absolute inset-0 flex flex-col items-center justify-center z-20"
         style="background:color-mix(in srgb, var(--bg) 80%, transparent)">
        <i class="ri-loader-4-line animate-spin text-3xl" style="color:var(--ac)"></i>
        <span class="text-[10px] font-bold uppercase mt-2" style="color:var(--tx)">Procesando...</span>
    </div>

    <button onclick="window.open('{{ route('assets.print-qr', $data->id) }}', '_blank')"
            class="absolute bottom-3 right-3 z-30 p-2 rounded-xl shadow-lg transition-all hover:scale-110 active:scale-95"
            style="background:var(--ac); color:var(--ac-inv)">
        <i class="ri-qr-code-line text-xl"></i>
    </button>
</div>

<div class="p-4 space-y-4">

    <div class="flex justify-center">
        @php
            $statusLabel = match($data->status) {
                'available' => 'Disponible',
                'assigned'  => 'Asignado',
                'maintenance' => 'Mantenimiento',
                'retired'   => 'Retirado',
                default     => Str::ucfirst($data->status),
            };
            $statusStyle = match($data->status) {
                'available' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                'assigned'  => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                'maintenance' => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                default     => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
            };
        @endphp
        <span class="px-4 py-1 rounded-full text-xs font-bold border-2 shadow-sm"
              style="{{ $statusStyle }}">
            {{ $statusLabel }}
        </span>
    </div>

    <x-sidebar-section icon="ri-information-line" label="Información Básica">
        <x-sidebar-row label="Serial"    :value="$data->serial" />
        <x-sidebar-row label="SAP"       :value="$data->sap" />
        <x-sidebar-row label="Hostname"  :value="$data->hostname" />
        <x-sidebar-row label="Modo Trabajo" :value="$data->workMode" />
        <x-sidebar-row label="Ubicación"  :value="$data->location" />
        <x-sidebar-row label="Teléfono"     :value="$data->phone" />
    </x-sidebar-section>

    <x-sidebar-section icon="ri-shield-user-line" label="Asignación Actual">
        <x-sidebar-row label="Asignado a" :value="$data->assignee" id="sidebarAssignedTo" />
        <x-sidebar-row label="Fecha"        :value="$data->assignedAt" />
    </x-sidebar-section>

</div>

<script>
QRCode.toCanvas(
    document.getElementById('asset-qr-{{ $data->id }}'),
    '{{ $data->qrUrl }}',
    { width: 144, errorCorrectionLevel: 'L' }
);
</script>

# --- resources/views/assets/tabs/details.blade.php ---
{{-- Adquisición --}}
<div class="mb-5">
    <h2 class="text-base font-bold mb-3 flex items-center gap-1.5" style="color:var(--tx)">
        <i class="ri-information-line text-xl"></i>
        <span>Detalles</span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Fecha Adquisición</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">{{ $asset->acquisition_date?->format('Y-m-d') ?? '---' }}</div>
        </div>
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Costo Adquisición</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">$ {{ number_format($asset->price ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Proveedor</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">{{ $asset->supplier ? Str::upper($asset->supplier) : '---' }}</div>
        </div>
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Factura No.</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">{{ $asset->invoice ?? '---' }}</div>
        </div>
    </div>
</div>

{{-- Especificaciones Técnicas --}}
<div class="mb-5">
    <h2 class="text-base font-bold mb-3 flex items-center gap-1.5" style="color:var(--tx)">
        <i class="ri-mac-line text-xl"></i>
        <span>Especificaciones Técnicas</span>
    </h2>
    <table class="w-full border-collapse rounded-md overflow-hidden text-xs" style="border:1px solid var(--b)">
        <thead>
            <tr style="background:var(--bg2)">
                <th class="px-3 py-2 text-left font-semibold" style="color:var(--tx2)">Característica</th>
                <th class="px-3 py-2 text-left font-semibold" style="color:var(--tx2)">Detalle</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Procesador</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">{{ $asset->cpu ?? '---' }}</td>
            </tr>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Memoria RAM</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">{{ $asset->ram ?? '---' }}</td>
            </tr>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Almacenamiento</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">SSD1: {{ $asset->ssd ?? 'N/A' }} / SSD2: {{ $asset->hdd ?? 'N/A' }}</td>
            </tr>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Sistema Operativo</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">{{ $asset->so ? Str::title($asset->so) : '---' }}</td>
            </tr>
        </tbody>
    </table>
</div>

# --- resources/views/assets/tabs/assignments.blade.php ---
<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold flex items-center gap-1.5" style="color:var(--tx)">
            <i class="ri-user-add-line text-xl"></i>
            <span>Asignaciones</span>
        </h2>

        @if($asset->status === 'available')
        <button
            class="px-3 py-1.5 rounded-md text-sm font-medium flex items-center gap-1"
            style="background:var(--ac); color:var(--ac-inv)"
            hx-get="{{ route('assets.assignments.create', $asset->id) }}"
            hx-target="#modal-body"
            hx-swap="innerHTML"
            hx-on::after-request="window.dispatchEvent(new CustomEvent('open-modal'))">
            <i class="ri-add-line text-xs"></i>
            <span>Nueva Asignación</span>
        </button>
        @endif
    </div>

    <div id="tabTableAssignments" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
    const el = document.getElementById('tabTableAssignments');
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;
    el.dataset.tabulatorInitialized = true;

    new Tabulator(el, {
        pagination: true,
        paginationSize: 15,
        layout: "fitColumns",
        data: @json($assignments),
        columns: [
            {title:"ID", field:"id", width:70},
            {title:"Fecha", field:"date"},
            {title:"Responsable", field:"assignee", width:250},
            {title:"Hardware", field:"hardware", formatter: "textarea"},
            {title:"Software", field:"software"},
            {title:"Acta", field:"minute", formatter: "html"},
        ],
    });
})();
</script>

# =============================================================================
# 10. VIEWS - Shared Components
# =============================================================================

# --- resources/views/components/index.blade.php ---
@props([
    'route',
    'config',
])

<x-layouts.app :title="$config->moduleTitle">
    @php
        $jsFriendlyName = str_replace(['.', '-', '/'], '_', $route);
        $instanceName = 'dt_' . $jsFriendlyName;
        $storageKey = $jsFriendlyName; 
    @endphp

    <div x-data="{{ $instanceName }}()" class="flex flex-col gap-4 h-full animate-core">
        
        {{-- ── Toolbar ────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between px-1">

            <div class="flex items-center gap-2">
                {{-- Selector de Columnas --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sigma-b bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx transition-all text-[10px] font-bold uppercase tracking-widest">
                        <i class="ri-layout-column-line text-amber-500 text-sm"></i>
                        <span class="hidden lg:inline">Columnas</span>
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute left-0 mt-2 w-64 bg-sigma-bg border border-sigma-b rounded-xl shadow-2xl z-50 p-3 border-sigma-b">
                        <span class="text-[9px] font-black uppercase text-sigma-tx2 mb-2 block opacity-50">Visibilidad</span>
                        <div class="flex flex-col gap-1 max-h-64 overflow-y-auto pr-1 scrollbar-thin">
                            <template x-for="col in allColumns" :key="col.field">
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-sigma-bg2 p-2 rounded-lg transition-colors group">
                                    <input type="checkbox" :checked="col.visible" @change="toggleColumn(col.field)"
                                           class="rounded border-sigma-b text-sigma-ac focus:ring-sigma-ac bg-sigma-bg2 w-4 h-4">
                                    <span class="text-[10px] font-bold text-sigma-tx2 uppercase group-hover:text-sigma-tx" x-text="col.title"></span>
                                </label>
                            </template>
                        </div>
                        <hr class="border-sigma-b my-2">
                        <button @click="resetColumns()" class="w-full text-center py-2 text-[9px] font-black text-red-500 hover:bg-red-500/10 rounded-lg uppercase transition-all">
                            Restablecer Vista
                        </button>
                    </div>
                </div>

                {{-- Export Dropdown (Direct Links) --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sigma-b bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx transition-all text-[10px] font-bold uppercase tracking-widest">
                        <i class="ri-file-excel-2-line text-emerald-500 text-sm"></i>
                        <span class="hidden lg:inline">Exportar</span>
                        <i class="ri-arrow-down-s-line text-xs transition-transform hidden lg:inline" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute left-0 mt-2 w-48 bg-sigma-bg border border-sigma-b rounded-xl shadow-2xl z-50 p-2 border-sigma-b">                       
                        <div class="flex flex-col gap-1">
                            @foreach([
                                'all'   => ['label' => 'Todo el historial', 'icon' => 'ri-database-line'],
                                'today' => ['label' => 'Solo hoy', 'icon' => 'ri-calendar-event-line'],
                                'week'  => ['label' => 'Esta semana', 'icon' => 'ri-calendar-todo-line'],
                                'month' => ['label' => 'Este mes', 'icon' => 'ri-calendar-2-line']
                            ] as $key => $item)
                                <a href="/{{ $route }}/export?range={{ $key }}" target="_blank"
                                   class="w-full px-3 py-2 text-[9px] font-bold uppercase text-sigma-tx2 hover:bg-sigma-bg2 hover:text-sigma-ac rounded-lg flex items-center gap-2 transition-all">
                                    <i class="{{ $item['icon'] }} opacity-50 text-sm"></i>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($config->showKpi)
                    <a href="/{{ $route }}/kpis" target="_blank" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sigma-b bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx transition-all text-[10px] font-bold uppercase tracking-widest">
                        <i class="ri-bar-chart-box-line text-blue-500 text-sm"></i>
                        <span class="hidden lg:inline">KPIs</span>
                    </a>
                @endif
            </div>

            {{-- Botón Nuevo --}}
            <div class="flex items-center">
                <button
                    hx-get="/{{ $route }}/create"
                    hx-target="#modal-body"
                    hx-swap="innerHTML"
                    hx-on::after-request="window.dispatchEvent(new CustomEvent('open-modal'))"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-sigma-ac text-sigma-ac-inv text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <i class="{{ $config->newIcon }} text-sm"></i>
                    <span>{{ $config->newButtonLabel }}</span>
                </button>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="flex-1 min-h-0">
            <div x-ref="table" x-init="initTable()" class="h-full"></div>
        </div>
    </div>

@push('scripts')
<script>
let table_{{ $jsFriendlyName }} = null;

document.addEventListener('alpine:init', () => {
    Alpine.data('{{ $instanceName }}', () => ({
        allColumns: [],
        storageKey: '{{ $storageKey }}',

        initTable(){
            if(table_{{ $jsFriendlyName }}){
                table_{{ $jsFriendlyName }}.destroy();
            }

            table_{{ $jsFriendlyName }} = new Tabulator(this.$refs.table, {
                height: "100%",
                index: "id",
                ajaxURL: '/{{ $route }}/data',
                ajaxConfig: 'GET',
                pagination: "remote",
                paginationSize: 15,
                paginationButtonCount: 5,
                ajaxParams: {},
                ajaxFiltering: true,
                ajaxSorting: true,
                layout: "fitDataStretch",
                movableColumns: true,
                persistence: {
                    columns: ["width", "visible", "order"],
                },
                persistenceMode: "local",
                persistenceID: this.storageKey,
                locale: "es",
                columns: @js($config->columns),
                ajaxResponse: (url, params, response) => response,
            });

            table_{{ $jsFriendlyName }}.on("tableBuilt", () => {
                this.refreshColumnList();
                const urlParams = new URLSearchParams(window.location.search);
                const id = urlParams.get("id");
                if(id) setTimeout(() => this.openRow(id, true), 300);
            });

            const events = ["columnMoved", "columnResized", "columnVisibilityChanged"];
            events.forEach(event => {
                table_{{ $jsFriendlyName }}.on(event, () => this.refreshColumnList());
            });

            table_{{ $jsFriendlyName }}.on("rowClick", (e, row) => {
                if(e.target.closest('button, a, input, [tabulator-field="files"], .no-click')) return;
                this.openRow(row.getData().id);
            });
        },

        refreshColumnList() {
            const cols = table_{{ $jsFriendlyName }}.getColumns();
            this.allColumns = cols
                .filter(c => c.getDefinition().field)
                .map(c => ({
                    title: c.getDefinition().title || c.getDefinition().field,
                    field: c.getDefinition().field,
                    visible: c.isVisible()
                }));
        },

        toggleColumn(field) {
            table_{{ $jsFriendlyName }}.toggleColumn(field);
            this.refreshColumnList();
        },

        resetColumns() {
            const key = `tabulator-${this.storageKey}-columns`;
            localStorage.removeItem(key);
            window.location.reload(); 
        },

        openRow(id, cleanUrl = false) {
            htmx.ajax('GET', `/{{ $route }}/${id}`, {
                target: '#modal-body',
                swap: 'innerHTML',
            }).then(() => {
                window.dispatchEvent(new CustomEvent('open-modal'));
                
                if(cleanUrl){
                    const url = new URL(window.location);
                    url.searchParams.delete("id");
                    window.history.replaceState({}, '', url);
                }
            });
        }
    }));
});
</script>
@endpush
</x-layouts.app>

# --- resources/views/components/new-modal.blade.php ---
<form id="sigma-new-form"
      hx-post="/{{ $route }}"
      hx-target="#modal-body"
      hx-swap="innerHTML"
      class="grid grid-cols-1 sm:grid-cols-4 gap-4">

    @csrf

    @foreach ($config->formFields as $field)
        @php
            $colSpan = match($field->cols) {
                2 => 'col-span-4 sm:col-span-2',
                3 => 'col-span-4 sm:col-span-3',
                4 => 'col-span-4 sm:col-span-4',
                default => 'col-span-4 sm:col-span-4',
            };
            $inputId = 'field-' . $field->name;
        @endphp

        <div class="flex flex-col gap-1 {{ $colSpan }}">

            <label for="{{ $inputId }}"
                   class="text-[10px] font-black uppercase tracking-widest"
                   style="color:var(--tx2)">
                {{ $field->label }}
                @if ($field->required)<span style="color:var(--ac)">*</span>@endif
            </label>

            @if ($field->widget === 'filepond')
                <input type="file"
                       id="{{ $inputId }}"
                       name="{{ $field->name }}"
                       data-widget="filepond"
                       {{ $field->required ? 'required' : '' }}>

            @elseif ($field->widget === 'flatpickr')
                <input type="text"
                       id="{{ $inputId }}"
                       name="{{ $field->name }}"
                       placeholder="{{ $field->placeholder ?: 'Seleccionar fecha' }}"
                       data-widget="flatpickr"
                       autocomplete="off"
                       {{ $field->required ? 'required' : '' }}
                       class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">

            @elseif ($field->widget === 'flatpickr-range')
                <input type="text"
                       id="{{ $inputId }}"
                       name="{{ $field->name }}"
                       placeholder="{{ $field->placeholder ?: 'Rango de fechas' }}"
                       data-widget="flatpickr-range"
                       autocomplete="off"
                       {{ $field->required ? 'required' : '' }}
                       class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">

            @elseif ($field->widget === 'slimselect')
                <select id="{{ $inputId }}"
                        name="{{ $field->name }}"
                        data-widget="slimselect"
                        {{ $field->required ? 'required' : '' }}>
                    <option value="">— Seleccionar —</option>
                    @foreach ($field->options as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

            @elseif ($field->type === 'select')
                <select id="{{ $inputId }}"
                        name="{{ $field->name }}"
                        {{ $field->required ? 'required' : '' }}
                        class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                        style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
                    <option value="">— Seleccionar —</option>
                    @foreach ($field->options as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

            @elseif ($field->type === 'textarea')
                <textarea id="{{ $inputId }}"
                          name="{{ $field->name }}"
                          rows="3"
                          placeholder="{{ $field->placeholder }}"
                          {{ $field->required ? 'required' : '' }}
                          class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border resize-none"
                          style="background:var(--bg2); border-color:var(--b); color:var(--tx)"></textarea>

            @else
                <input id="{{ $inputId }}"
                       type="{{ $field->type }}"
                       name="{{ $field->name }}"
                       placeholder="{{ $field->placeholder }}"
                       {{ $field->required ? 'required' : '' }}
                       class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            @endif

            @if ($field->hint)
                <p class="text-[10px]" style="color:var(--tx2); opacity:.5">{{ $field->hint }}</p>
            @endif

        </div>
    @endforeach

    <div class="col-span-4 flex justify-end gap-3 pt-2 mt-2 border-t" style="border-color:var(--b)">
        <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('close-modal'))"
                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all hover:scale-[1.02] active:scale-[0.98]"
                style="border-color:var(--b); color:var(--tx2)">
            Cancelar
        </button>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-[0.98]"
                style="background:var(--ac); color:var(--ac-inv)">
            <i class="ri-save-line text-sm"></i>
            <span>Guardar</span>
        </button>
    </div>

</form>

# --- resources/views/components/detail-modal.blade.php ---
<div class="grid grid-cols-4 gap-4">

    <div id="sidebar-summary" class="rounded-lg shadow-md lg:col-span-1 overflow-hidden" style="background:var(--bg); border:1px solid var(--b)">
        @if($sidebarView && $sidebarData)
            @include($sidebarView, ['data' => $sidebarData])
        @endif
    </div>  

    <div class="rounded-lg shadow-md overflow-hidden lg:col-span-3" style="background:var(--bg); border:1px solid var(--b)">

        {{-- Tab bar --}}
        <div class="flex border-b flex-wrap shrink-0" style="border-color:var(--b); background:var(--bg2)">
            @foreach($tabs as $tab)
            <div class="tab px-3 py-2.5 cursor-pointer font-medium text-sm whitespace-nowrap transition-colors duration-200"
                style="color:var(--tx2)"
                hx-get="{{ route($tab->route, ['id' => $id]) }}"
                hx-target="#tab-content"
                hx-swap="innerHTML"
                data-tab="{{ $tab->key }}"
                @if($tab->default) hx-trigger="load, click" @else hx-trigger="click" @endif>
                <i class="{{ $tab->icon }}"></i> {{ $tab->label }}
            </div>
            @endforeach
        </div>

        {{-- Tab content --}}
        <div id="tab-content" class="p-4 overflow-y-auto flex-grow" style="background:var(--bg)">
            <div class="flex justify-center p-10 opacity-20">
                <i class="ri-loader-4-line animate-spin text-4xl"></i>
            </div>
        </div>
    </div>
</div>

# --- resources/views/components/sidebar-section.blade.php ---
@props(['icon', 'label'])

<div class="pb-3 border-b border-dashed last:border-0 last:pb-0" style="border-color:var(--b)">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-2" style="color:var(--tx2)">
        <i class="{{ $icon }} text-base"></i>
        <span class="uppercase tracking-wider">{{ $label }}</span>
    </h3>
    {{ $slot }}
</div>

# --- resources/views/components/sidebar-row.blade.php ---
@props(['label', 'value' => null, 'id' => null])

@if($value)
<div class="flex text-xs gap-1" @if($id) id="{{ $id }}" @endif>
    <span class="w-24 shrink-0" style="color:var(--tx2)">{{ $label }}:</span>
    <span class="font-semibold truncate" style="color:var(--tx)">{{ Str::ucfirst((string)$value) }}</span>
</div>
@endif
