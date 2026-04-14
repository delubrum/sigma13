<?php

declare(strict_types=1);

use App\Domain\Evaluation\Web\Adapters\EvaluationsIndexAdapter as Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('evaluation')->name('evaluation.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    Route::get('/export', \App\Domain\Evaluation\Actions\ExportEvaluationsAction::class)->name('export');
});
