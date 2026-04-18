<?php

declare(strict_types=1);

use App\Domain\HelpDesk\Web\Adapters\IndexAdapter as Index;
use App\Domain\Shared\Web\Actions\Create;
use App\Domain\HelpDesk\Web\Adapters\UpsertAdapter as Upsert;
use App\Domain\HelpDesk\Web\Adapters\TasksAdapter;
use App\Domain\HelpDesk\Web\Adapters\ProcessActionAdapter;
use App\Domain\HelpDesk\Web\Adapters\Modals\TaskModalAdapter;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::middleware('auth')->prefix('helpdesk')->name('helpdesk.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');

    Route::get('/create/{id?}', fn (\Illuminate\Http\Request $req, ?string $id = null) => app(Create::class)->asController($req, 'helpdesk', $id))->name('create');
    Route::post('/upsert', Upsert::class)->middleware(ProtectAgainstSpam::class)->name('upsert');

    // Tabs
    Route::moduleTab('tasks', TasksAdapter::class);

    // Quick Actions
    Route::post('/{id}/action/{action}', [ProcessActionAdapter::class, 'asController'])->name('process');
    Route::post('/action/attend/{id}', [ProcessActionAdapter::class, 'asController'])->defaults('action', 'attend')->name('action.attend');
    Route::post('/action/close/{id}', [ProcessActionAdapter::class, 'asController'])->defaults('action', 'close')->name('action.close');
    Route::post('/action/reject/{id}', [ProcessActionAdapter::class, 'asController'])->defaults('action', 'reject')->name('action.reject');

    // Task Modal
    Route::get('/{id}/tasks/create', TaskModalAdapter::class)->name('tasks.create');
    Route::post('/{id}/tasks', [TaskModalAdapter::class, 'asStore'])->middleware(ProtectAgainstSpam::class)->name('tasks.store');

});
