<?php

declare(strict_types=1);

use App\Domain\Assets\Web\Adapters\IndexAdapter as Index;
use App\Domain\Assets\Web\Adapters\Modals\AssignmentsModalAdapter as Assignment;
use App\Domain\Assets\Web\Adapters\Modals\AutomationsModalAdapter as Automation;
use App\Domain\Assets\Web\Adapters\Modals\DocumentsModalAdapter as Document;
use App\Domain\Assets\Web\Adapters\Modals\ReturnsModalAdapter as ReturnAction;
use App\Domain\Assets\Web\Adapters\PrintQrAdapter as PrintQr;
use App\Domain\Assets\Web\Adapters\PublicShowAdapter as PublicShow;
use App\Domain\Assets\Web\Adapters\SidebarPhotoAdapter as SidebarPhoto;
use App\Domain\Assets\Web\Adapters\Tabs\AITabAdapter as AIResolver;
use App\Domain\Assets\Web\Adapters\Tabs\AutomationsTabAdapter as Automations;
use App\Domain\Assets\Web\Adapters\Tabs\DetailsTabAdapter as Details;
use App\Domain\Assets\Web\Adapters\Tabs\DocumentsTabAdapter as Documents;
use App\Domain\Assets\Web\Adapters\Tabs\MaintenancesTabAdapter as Maintenances;
use App\Domain\Assets\Web\Adapters\Tabs\MovementsTabAdapter as Movements;
use App\Domain\Assets\Web\Adapters\Tabs\PreventiveTabAdapter as Preventive;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Public Routes (No Auth)
Route::get('/v/{serial}', PublicShow::class)->name('assets.public');

Route::middleware('auth')->prefix('assets')->name('assets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', (new Index)->asData(...))->name('data');
    Route::get('/{asset}/sidebar-photo', SidebarPhoto::class)->name('sidebar.photo');
    Route::get('/{asset}/print-qr', PrintQr::class)->name('print-qr');

    Route::get('/{asset}/details', (new Details)->asController(...))->name('details');
    Route::get('/{asset}/movements', (new Movements)->asController(...))->name('movements');
    Route::get('/{asset}/movements/data', (new Movements)->asData(...))->name('movements.data');
    Route::get('/{asset}/assignments/create', (new Assignment)->asController(...))->name('assignments.create');
    Route::post('/{asset}/assignments', (new Assignment)->asStore(...))->middleware(ProtectAgainstSpam::class)->name('assignments.store');
    Route::get('/assignments/{event}/edit', (new Assignment)->asEdit(...))->name('assignments.edit');
    Route::patch('/assignments/{event}', (new Assignment)->asUpdate(...))->middleware(ProtectAgainstSpam::class)->name('assignments.update');
    Route::get('/{asset}/returns/create', (new ReturnAction)->asController(...))->name('returns.create');
    Route::post('/{asset}/returns', (new ReturnAction)->asStore(...))->middleware(ProtectAgainstSpam::class)->name('returns.store');
    Route::moduleTab('documents', Documents::class);
    Route::get('/{asset}/documents/create', (new Document)->asController(...))->name('documents.create');
    Route::post('/{asset}/documents', (new Document)->asStore(...))->middleware(ProtectAgainstSpam::class)->name('documents.store');
    Route::moduleTab('automations', Automations::class);
    Route::get('/{asset}/automations/create', (new Automation)->asController(...))->name('automations.create');
    Route::post('/{asset}/automations', (new Automation)->asStore(...))->middleware(ProtectAgainstSpam::class)->name('automations.store');

    Route::get('/{asset}/ai', (new AIResolver)->asController(...))->name('ai');
    Route::post('/{asset}/ai/generate', (new AIResolver)->asGenerate(...))->name('ai.generate');

    Route::get('/{asset}/preventive', (new Preventive)->asController(...))->name('preventive');
    Route::moduleTab('maintenances', Maintenances::class);
});
