<?php

declare(strict_types=1);

use App\Domain\Engineering\Web\Adapters\CbmAdapter;
use App\Domain\Engineering\Web\Adapters\FastenersAdapter;
use App\Domain\Engineering\Web\Adapters\LibraryAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Technical Library
    Route::get('engineering/library', LibraryAdapter::class)->name('engineering.library');
    Route::get('engineering/library/data', [LibraryAdapter::class, 'asData'])->name('engineering.library.data');

    // CBM Calculator
    Route::get('engineering/cbm', CbmAdapter::class)->name('engineering.cbm');
    Route::get('engineering/cbm/data', [CbmAdapter::class, 'asData'])->name('engineering.cbm.data');

    // Fasteners
    Route::get('engineering/fasteners', FastenersAdapter::class)->name('engineering.fasteners');
    Route::get('engineering/fasteners/data', [FastenersAdapter::class, 'asData'])->name('engineering.fasteners.data');

    // Extrusion
    Route::get('engineering/extrusion', \App\Domain\Engineering\Web\Adapters\ExtrusionAdapter::class)->name('engineering.extrusion');
    Route::get('engineering/extrusion/data', [\App\Domain\Engineering\Web\Adapters\ExtrusionAdapter::class, 'asData'])->name('engineering.extrusion.data');
});
