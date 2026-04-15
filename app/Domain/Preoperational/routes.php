<?php

declare(strict_types=1);

use App\Domain\Preoperational\Web\Adapters\ChecklistAdapter as Checklist;
use App\Domain\Preoperational\Web\Adapters\CreateAdapter as Create;
use App\Domain\Preoperational\Web\Adapters\DetailAdapter as Detail;
use App\Domain\Preoperational\Web\Adapters\FinalizeAdapter as Finalize;
use App\Domain\Preoperational\Web\Adapters\IndexAdapter as Index;
use App\Domain\Preoperational\Web\Adapters\SaveAdapter as Save;
use App\Domain\Preoperational\Web\Adapters\UploadPhotoAdapter as UploadPhoto;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('preoperational')->name('preoperational.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', (new Index)->asData(...))->name('data');
    Route::get('/create', (new Create)->asController(...))->name('create');
    Route::get('/checklist', (new Checklist)->asController(...))->name('checklist');
    Route::get('/detail', (new Detail)->asController(...))->name('detail');
    Route::post('/save', (new Save)->asController(...))->name('save');
    Route::post('/finalize', (new Finalize)->asController(...))->name('finalize');
    Route::post('/upload-photo', (new UploadPhoto)->asController(...))->name('uploadPhoto');
});
