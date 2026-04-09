<?php

declare(strict_types=1);

use App\Domain\Maintenance\Actions\Create;
use App\Domain\Maintenance\Actions\Index;
use App\Domain\Maintenance\Actions\Tabs\Detail;
use App\Domain\Maintenance\Actions\Tabs\Tasks;
use App\Domain\Maintenance\Actions\Update;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get('/maintenance', [Index::class,  'asController'])->name('maintenance.index');
    Route::get('/maintenance/data', [Index::class,  'asData'])->name('maintenance.data');

    Route::get('/maintenance/create', [Create::class, 'asController'])->name('maintenance.create');
    Route::post('/maintenance', [Create::class, 'asStore'])->name('maintenance.store');

    Route::post('/maintenance/{id}/update', [Update::class, 'asController'])->name('maintenance.update');

    Route::get('/maintenance/{id}/detail', [Detail::class, 'asController'])->name('maintenance.detail');
    Route::get('/maintenance/{id}/tasks', [Tasks::class, 'asData'])->name('maintenance.tasks');
    Route::get('/maintenance/{id}/tasks/form', [Tasks::class, 'asForm'])->name('maintenance.task.form');
    Route::post('/maintenance/{id}/tasks', [Tasks::class, 'asStore'])->name('maintenance.tasks.store');
});
