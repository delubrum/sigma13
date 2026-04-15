<?php

declare(strict_types=1);

use App\Domain\Printing\Web\Adapters\CreateAdapter;
use App\Domain\Printing\Web\Adapters\DeleteAdapter;
use App\Domain\Printing\Web\Adapters\DetailAdapter;
use App\Domain\Printing\Web\Adapters\IndexAdapter;
use App\Domain\Printing\Web\Adapters\Print\EsAdapter;
use App\Domain\Printing\Web\Adapters\Print\EsmAdapter;
use App\Domain\Printing\Web\Adapters\SaveAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('printing')->name('printing.')->group(function (): void {
    Route::get('/', IndexAdapter::class)->name('index');
    Route::get('/data', (new IndexAdapter)->asData(...))->name('data');
    Route::get('/create', (new CreateAdapter)->asController(...))->name('create');

    Route::post('/save', (new SaveAdapter)->asController(...))->name('save');

    Route::get('/{wo}', (new DetailAdapter)->asController(...))->name('show');
    Route::delete('/{wo}', (new DeleteAdapter)->asController(...))->name('delete');

    Route::post('/{wo}/print/esm', (new EsmAdapter)->asController(...))->name('print.esm');
    Route::post('/{wo}/print/es', (new EsAdapter)->asController(...))->name('print.es');
});
