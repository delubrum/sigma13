<?php

declare(strict_types=1);

use App\Domain\Assets\Actions\Index;
use App\Domain\Assets\Actions\Modals\Assignment;
use App\Domain\Assets\Actions\Modals\Automation;
use App\Domain\Assets\Actions\Modals\Document;
use App\Domain\Assets\Actions\Modals\ReturnAction;
use App\Domain\Assets\Actions\PrintQr;
use App\Domain\Assets\Actions\Tabs\AIResolver;
use App\Domain\Assets\Actions\Tabs\Automations;
use App\Domain\Assets\Actions\Tabs\Details;
use App\Domain\Assets\Actions\Tabs\Documents;
use App\Domain\Assets\Actions\Tabs\Maintenances;
use App\Domain\Assets\Actions\Tabs\Movements;
use App\Domain\Assets\Actions\Tabs\Preventive;
use App\Domain\Assets\Actions\Upsert;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('assets')->name('assets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/create', [Upsert::class, 'asController'])->name('create');
    Route::post('/', [Upsert::class, 'handle'])->name('store');
    Route::get('/{id}/edit', [Upsert::class, 'asController'])->name('edit');
    Route::put('/{id}', [Upsert::class, 'handle'])->name('update');
    Route::get('/{id}/print-qr', PrintQr::class)->name('print-qr');

    // Tabs
    Route::get('/{id}/details', [Details::class, 'asController'])->name('details');
    // Movements (Asignaciones y Devoluciones unificadas)
    Route::get('/{id}/movements', [Movements::class, 'asController'])->name('movements');
    Route::get('/{id}/movements/data', [Movements::class, 'asData'])->name('movements.data');
    Route::get('/{id}/assignments/create', [Assignment::class, 'asController'])->name('assignments.create');
    Route::post('/{id}/assignments', [Assignment::class, 'asStore'])->name('assignments.store');
    Route::get('/{id}/returns/create', [ReturnAction::class, 'asController'])->name('returns.create');
    Route::post('/{id}/returns', [ReturnAction::class, 'asStore'])->name('returns.store');
    Route::get('/{id}/documents', [Documents::class, 'asController'])->name('documents');
    Route::get('/{id}/documents/data', [Documents::class, 'asData'])->name('documents.data');
    Route::get('/{id}/documents/create', [Document::class, 'asController'])->name('documents.create');
    Route::post('/{id}/documents', [Document::class, 'asStore'])->name('documents.store');
    Route::get('/{id}/automations', [Automations::class, 'asController'])->name('automations');
    Route::get('/{id}/automations/data', [Automations::class, 'asData'])->name('automations.data');
    Route::get('/{id}/automations/create', [Automation::class, 'asController'])->name('automations.create');
    Route::post('/{id}/automations', [Automation::class, 'asStore'])->name('automations.store');

    // AI Analysis Tab
    Route::get('/{id}/ai', [AIResolver::class, 'asController'])->name('ai');
    Route::post('/{id}/ai/generate', [AIResolver::class, 'asGenerate'])->name('ai.generate');

    Route::get('/{id}/preventive', [Preventive::class, 'asController'])->name('preventive');
    Route::get('/{id}/maintenances', [Maintenances::class, 'asController'])->name('maintenances');
    Route::get('/{id}/maintenances/data', [Maintenances::class, 'asData'])->name('maintenances.data');
});
