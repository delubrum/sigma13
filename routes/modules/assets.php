<?php

declare(strict_types=1);

use App\Actions\Assets\Index;
use App\Actions\Assets\Modals\Assignment;
use App\Actions\Assets\Modals\Document;
use App\Actions\Assets\Modals\ReturnAction;
use App\Actions\Assets\PrintQr;
use App\Actions\Assets\Tabs\AIResolver;
use App\Actions\Assets\Tabs\Automations;
use App\Actions\Assets\Modals\Automation;
use App\Actions\Assets\Tabs\Details;
use App\Actions\Assets\Tabs\Documents;
use App\Actions\Assets\Tabs\Maintenances;
use App\Actions\Assets\Tabs\Movements;
use App\Actions\Assets\Tabs\Preventive;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('assets')->name('assets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
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
