<?php

declare(strict_types=1);

namespace App\Domain\Maintenance;

use App\Domain\Maintenance\Web\Adapters\IndexAdapter as Index;
use App\Domain\Maintenance\Web\Adapters\CreateAdapter as Create;
use App\Domain\Maintenance\Web\Adapters\SaveAdapter as Save;
use App\Domain\Maintenance\Web\Adapters\Tabs\DetailsTabAdapter;
use App\Domain\Maintenance\Web\Adapters\Tabs\TasksTabAdapter;
use App\Domain\Maintenance\Web\Adapters\PatchAdapter;
use App\Domain\Maintenance\Web\Adapters\ProcessActionAdapter;
use App\Domain\Maintenance\Web\Adapters\Modals\TaskModalAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('maintenance')->name('maintenance.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/create/{id?}', Create::class)->name('create');
    Route::post('/save', Save::class)->name('save');

    // Detail Tabs
    Route::get('/tabs/details/{id}', DetailsTabAdapter::class)->name('details');
    Route::get('/tabs/tasks/{id}', TasksTabAdapter::class)->name('tasks');
    Route::get('/tabs/tasks/data/{id}', [TasksTabAdapter::class, 'asData'])->name('tasks.data');
    
    // Quick Actions
    Route::post('/{id}/patch', PatchAdapter::class)->name('patch');
    Route::post('/{id}/action/{action}', [ProcessActionAdapter::class, 'asController'])->name('process');
    
    // Modals
    Route::get('/tabs/tasks/create/{id}', TaskModalAdapter::class)->name('tasks.create');
    Route::post('/tasks/upsert', [TaskModalAdapter::class, 'upsert'])->name('tasks.upsert');
});
