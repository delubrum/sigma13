<?php

declare(strict_types=1);

use App\Domain\MaintenanceP\Actions\Index;
use App\Domain\MaintenanceP\Actions\Tabs\Detail;
use App\Domain\MaintenanceP\Actions\Tabs\Tasks;
use App\Domain\MaintenanceP\Actions\Update;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get('/maintenancep', [Index::class,  'asController'])->name('maintenancep.index');
    Route::get('/maintenancep/data', [Index::class,  'asData'])->name('maintenancep.data');

    // Updates
    Route::post('/maintenancep/{id}/update', [Update::class, 'asController'])->name('maintenancep.update');

    // Detail tabs
    Route::get('/maintenancep/{id}/detail', [Detail::class, 'asController'])->name('maintenancep.detail');
    Route::get('/maintenancep/{id}/tasks', [Tasks::class, 'asData'])->name('maintenancep.tasks');
    Route::get('/maintenancep/{id}/tasks/form', [Tasks::class, 'asForm'])->name('maintenancep.task.form');
    Route::post('/maintenancep/{id}/tasks', [Tasks::class, 'asStore'])->name('maintenancep.tasks.store');
});
