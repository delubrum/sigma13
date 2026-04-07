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
    Route::get('/{id}/details', [Details::class, 'asController'])->name('details');
    Route::get('/{id}/assignments', [Assignments::class, 'asController'])->name('assignments');
    Route::get('/{id}/assignments/create', [CreateAssignment::class, 'asController'])->name('assignments.create');
    Route::post('/{id}/assignments', [CreateAssignment::class, 'asStore'])->name('assignments.store');
    Route::get('/{id}/returns', [Returns::class, 'asController'])->name('returns');
    Route::get('/{id}/documents', [Documents::class, 'asController'])->name('documents');
    Route::get('/{id}/automations', [Automations::class, 'asController'])->name('automations');
    Route::get('/{id}/preventive', [Preventive::class, 'asController'])->name('preventive');
    Route::get('/{id}/maintenances', [Maintenances::class, 'asController'])->name('maintenances');
});
