<?php

declare(strict_types=1);

use App\Domain\Assets\Web\Actions\Index;
use App\Domain\Assets\Web\Actions\Modals\Assignment;
use App\Domain\Assets\Web\Actions\Modals\Automation;
use App\Domain\Assets\Web\Actions\Modals\Document;
use App\Domain\Assets\Web\Actions\Modals\ReturnAction;
use App\Domain\Assets\Web\Actions\PrintQr;
use App\Domain\Assets\Web\Actions\Tabs\AIResolver;
use App\Domain\Assets\Web\Actions\Tabs\Automations;
use App\Domain\Assets\Web\Actions\Tabs\Details;
use App\Domain\Assets\Web\Actions\Tabs\Documents;
use App\Domain\Assets\Web\Actions\Tabs\Maintenances;
use App\Domain\Assets\Web\Actions\Tabs\Movements;
use App\Domain\Assets\Web\Actions\Tabs\Preventive;
use App\Domain\Assets\Web\Actions\PublicShow;
use Illuminate\Support\Facades\Route;

// Public Routes (No Auth)
Route::get('/v/{serial}', PublicShow::class)->name('assets.public');

Route::middleware('auth')->prefix('assets')->name('assets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/{asset}/sidebar-photo', \App\Domain\Assets\Web\Actions\SidebarPhoto::class)->name('sidebar.photo');
    Route::get('/{asset}/print-qr', PrintQr::class)->name('print-qr');

    Route::get('/{asset}/details', [Details::class, 'asController'])->name('details');
    Route::get('/{asset}/movements', [Movements::class, 'asController'])->name('movements');
    Route::get('/{asset}/movements/data', [Movements::class, 'asData'])->name('movements.data');
    Route::get('/{asset}/assignments/create', [Assignment::class, 'asController'])->name('assignments.create');
    Route::post('/{asset}/assignments', [Assignment::class, 'asStore'])->name('assignments.store');
    Route::get('/assignments/{event}/edit', [Assignment::class, 'asEdit'])->name('assignments.edit');
    Route::patch('/assignments/{event}', [Assignment::class, 'asUpdate'])->name('assignments.update');
    Route::get('/{asset}/returns/create', [ReturnAction::class, 'asController'])->name('returns.create');
    Route::post('/{asset}/returns', [ReturnAction::class, 'asStore'])->name('returns.store');
    Route::get('/{asset}/documents', [Documents::class, 'asController'])->name('documents');
    Route::get('/{asset}/documents/data', [Documents::class, 'asData'])->name('documents.data');
    Route::get('/{asset}/documents/create', [Document::class, 'asController'])->name('documents.create');
    Route::post('/{asset}/documents', [Document::class, 'asStore'])->name('documents.store');
    Route::get('/{asset}/automations', [Automations::class, 'asController'])->name('automations');
    Route::get('/{asset}/automations/data', [Automations::class, 'asData'])->name('automations.data');
    Route::get('/{asset}/automations/create', [Automation::class, 'asController'])->name('automations.create');
    Route::post('/{asset}/automations', [Automation::class, 'asStore'])->name('automations.store');

    Route::get('/{asset}/ai', [AIResolver::class, 'asController'])->name('ai');
    Route::post('/{asset}/ai/generate', [AIResolver::class, 'asGenerate'])->name('ai.generate');

    Route::get('/{asset}/preventive', [Preventive::class, 'asController'])->name('preventive');
    Route::get('/{asset}/maintenances', [Maintenances::class, 'asController'])->name('maintenances');
    Route::get('/{asset}/maintenances/data', [Maintenances::class, 'asData'])->name('maintenances.data');
});
