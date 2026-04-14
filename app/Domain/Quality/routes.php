<?php

declare(strict_types=1);

use App\Domain\Quality\Web\Adapters\DocumentsAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('quality/documents', DocumentsAdapter::class)->name('quality.documents');
    Route::get('quality/documents/data', [DocumentsAdapter::class, 'asData'])->name('quality.documents.data');
});
