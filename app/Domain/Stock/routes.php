<?php

declare(strict_types=1);

namespace App\Domain\Stock;

use App\Domain\Shared\Web\Actions\Create;
use App\Domain\Stock\Web\Adapters\IndexAdapter as Index;
use App\Domain\Stock\Web\Adapters\MovementsIndexAdapter;
use App\Domain\Stock\Web\Adapters\MovementsCreateAdapter;
use App\Domain\Stock\Web\Adapters\Modals\TaskModalAdapter;
use App\Domain\Stock\Web\Adapters\ProcessActionAdapter;
use App\Domain\Stock\Web\Adapters\TasksAdapter;
use App\Domain\Stock\Web\Adapters\UpsertAdapter as Upsert;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::middleware('auth')->prefix('stock')->name('stock.')->group(function (): void {
    // Master Catalog
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/create/{id?}', fn (\Illuminate\Http\Request $req, ?string $id = null) => app(Create::class)->asController($req, 'stock', $id))->name('create');
});
