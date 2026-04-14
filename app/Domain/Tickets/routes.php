<?php

declare(strict_types=1);

use App\Domain\Tickets\Web\Adapters\IndexAdapter as Index;
use App\Domain\Tickets\Web\Adapters\TasksAdapter;
use App\Domain\Tickets\Web\Adapters\PatchAdapter;
use App\Domain\Tickets\Web\Adapters\CloseAdapter;
use App\Domain\Tickets\Web\Adapters\Modals\ItemModalAdapter;
use App\Domain\Tickets\Web\Adapters\Modals\RejectModalAdapter;
use App\Domain\Tickets\Web\Adapters\Modals\RateModalAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('tickets')->name('tickets.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    
    // Tabs
    Route::get('/{id}/tasks', TasksAdapter::class)->name('tasks');
    Route::get('/{id}/tasks/data', [TasksAdapter::class, 'asData'])->name('tasks.data');
    
    // Quick Actions
    Route::post('/{id}/patch', PatchAdapter::class)->name('patch');
    Route::get('/{id}/close', CloseAdapter::class)->name('close');
    
    // Special Modals (Create & Upsert groups)
    Route::get('/{id}/tasks/create', ItemModalAdapter::class)->name('tasks.create');
    Route::post('/item/upsert', [ItemModalAdapter::class, 'upsert'])->name('item.upsert');

    Route::get('/{id}/reject', RejectModalAdapter::class)->name('reject.create');
    Route::post('/reject/upsert', [RejectModalAdapter::class, 'upsert'])->name('reject.upsert');

    Route::get('/{id}/rate', RateModalAdapter::class)->name('rate.create');
    Route::post('/rate/upsert', [RateModalAdapter::class, 'upsert'])->name('rate.upsert');
});
