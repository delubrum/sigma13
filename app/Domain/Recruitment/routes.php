<?php

declare(strict_types=1);

use App\Domain\Recruitment\Actions\Create;
use App\Domain\Recruitment\Actions\Index;
use App\Domain\Recruitment\Actions\Tabs\Candidates;
use App\Domain\Recruitment\Actions\Tabs\Detail;
use App\Domain\Recruitment\Actions\Update;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    // Index + data
    Route::get('/recruitment', [Index::class,  'asController'])->name('recruitment.index');
    Route::get('/recruitment/data', [Index::class,  'asData'])->name('recruitment.data');

    // Create
    Route::get('/recruitment/create', [Create::class, 'asController'])->name('recruitment.create');
    Route::post('/recruitment', [Create::class, 'asStore'])->name('recruitment.store');

    // Inline update
    Route::post('/recruitment/{id}/update', [Update::class, 'asController'])->name('recruitment.update');

    // Detail tabs
    Route::get('/recruitment/{id}/detail', [Detail::class, 'asController'])->name('recruitment.detail');
    Route::get('/recruitment/{id}/candidates', [Candidates::class, 'asController'])->name('recruitment.candidates');
});
