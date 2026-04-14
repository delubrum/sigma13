<?php

declare(strict_types=1);

use App\Domain\Assets\Web\Adapters\IndexAdapter as Index;
use App\Domain\Assets\Web\Adapters\PrintQrAdapter as PrintQr;
use App\Domain\Assets\Web\Adapters\PublicShowAdapter as PublicShow;
use App\Domain\Assets\Web\Adapters\SidebarPhotoAdapter as SidebarPhoto;
use App\Domain\Assets\Web\Adapters\Modals\AssignmentsModalAdapter as Assignment;
use App\Domain\Assets\Web\Adapters\Modals\AutomationsModalAdapter as Automation;
use App\Domain\Assets\Web\Adapters\Modals\DocumentsModalAdapter as Document;
use App\Domain\Assets\Web\Adapters\Modals\ReturnsModalAdapter as ReturnAction;
use App\Domain\Assets\Web\Adapters\Tabs\AITabAdapter as AIResolver;
use App\Domain\Assets\Web\Adapters\Tabs\AutomationsTabAdapter as Automations;
use App\Domain\Assets\Web\Adapters\Tabs\DetailsTabAdapter as Details;
use App\Domain\Assets\Web\Adapters\Tabs\DocumentsTabAdapter as Documents;
use App\Domain\Assets\Web\Adapters\Tabs\MaintenancesTabAdapter as Maintenances;
use App\Domain\Assets\Web\Adapters\Tabs\MovementsTabAdapter as Movements;
use App\Domain\Assets\Web\Adapters\Tabs\PreventiveTabAdapter as Preventive;
use Illuminate\Support\Facades\Route;

// Public Routes (No Auth)
Route::get('/v/{serial}', PublicShow::class)->name('assets.public');

Route::middleware('auth')->prefix('assets')->name('assets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/{asset}/sidebar-photo', SidebarPhoto::class)->name('sidebar.photo');
    Route::get('/{asset}/print-qr', PrintQr::class)->name('print-qr');

    Route::get('/{asset}/details', [Details::class, 'asController'])->name('details');
    Route::get('/{asset}/movements', [Movements::class, 'asController'])->name('movements');
    Route::get('/{asset}/movements/data', [Movements::class, 'asData'])->name('movements.data');
    Route::get('/{asset}/assignments/create', [Assignment::class, 'asController'])->name('assignments.create');
    Route::post('/{asset}/assignments', [Assignment::class, 'asStore'])->middleware(\Spatie\Honeypot\ProtectAgainstSpam::class)->name('assignments.store');
    Route::get('/assignments/{event}/edit', [Assignment::class, 'asEdit'])->name('assignments.edit');
    Route::patch('/assignments/{event}', [Assignment::class, 'asUpdate'])->middleware(\Spatie\Honeypot\ProtectAgainstSpam::class)->name('assignments.update');
    Route::get('/{asset}/returns/create', [ReturnAction::class, 'asController'])->name('returns.create');
    Route::post('/{asset}/returns', [ReturnAction::class, 'asStore'])->middleware(\Spatie\Honeypot\ProtectAgainstSpam::class)->name('returns.store');
    Route::get('/{asset}/documents', [Documents::class, 'asController'])->name('documents');
    Route::get('/{asset}/documents/data', [Documents::class, 'asData'])->name('documents.data');
    Route::get('/{asset}/documents/create', [Document::class, 'asController'])->name('documents.create');
    Route::post('/{asset}/documents', [Document::class, 'asStore'])->middleware(\Spatie\Honeypot\ProtectAgainstSpam::class)->name('documents.store');
    Route::get('/{asset}/automations', [Automations::class, 'asController'])->name('automations');
    Route::get('/{asset}/automations/data', [Automations::class, 'asData'])->name('automations.data');
    Route::get('/{asset}/automations/create', [Automation::class, 'asController'])->name('automations.create');
    Route::post('/{asset}/automations', [Automation::class, 'asStore'])->middleware(\Spatie\Honeypot\ProtectAgainstSpam::class)->name('automations.store');

    Route::get('/{asset}/ai', [AIResolver::class, 'asController'])->name('ai');
    Route::post('/{asset}/ai/generate', [AIResolver::class, 'asGenerate'])->name('ai.generate');

    Route::get('/{asset}/preventive', [Preventive::class, 'asController'])->name('preventive');
    Route::get('/{asset}/maintenances', [Maintenances::class, 'asController'])->name('maintenances');
    Route::get('/{asset}/maintenances/data', [Maintenances::class, 'asData'])->name('maintenances.data');
});
