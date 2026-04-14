<?php

declare(strict_types=1);

use App\Domain\Docs\Web\Adapters\DocsIndexAdapter as Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('docs')->name('docs.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
