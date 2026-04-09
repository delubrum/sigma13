<?php

declare(strict_types=1);

use App\Domain\IT\Actions\Create;
use App\Domain\IT\Actions\Index;
use App\Domain\IT\Actions\Tabs\Detail;
use App\Domain\IT\Actions\Tabs\Tasks;
use App\Domain\IT\Actions\Update;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    // Index + data
    Route::get('/it', [Index::class,  'asController'])->name('it.index');
    Route::get('/it/data', [Index::class,  'asData'])->name('it.data');

    // Create
    Route::get('/it/create', [Create::class, 'asController'])->name('it.create');
    Route::post('/it', [Create::class, 'asStore'])->name('it.store');

    // Inline update (field=asset_id|priority|sgc|assignee_id|ended_at|closed_at|reason)
    Route::post('/it/{id}/update', [Update::class, 'asController'])->name('it.update');

    // Detail tabs
    Route::get('/it/{id}/detail', [Detail::class, 'asController'])->name('it.detail');
    Route::get('/it/{id}/tasks', [Tasks::class, 'asData'])->name('it.tasks');
    Route::get('/it/{id}/tasks/form', [Tasks::class, 'asForm'])->name('it.task.form');
    Route::post('/it/{id}/tasks', [Tasks::class, 'asStore'])->name('it.tasks.store');
});
