<?php

declare(strict_types=1);

use App\Domain\Shared\Actions\Create;
use App\Domain\Shared\Actions\Detail;
use App\Domain\Shared\Actions\Excel;
use App\Domain\Shared\Actions\Upload;
use Illuminate\Support\Facades\Route;

// SIGMA Shared Orchestrators
Route::get('{route}/export', Excel::class)->name('global.export')->where('route', '[a-zA-Z0-9_-]+');
Route::get('/{route}/create', Create::class);
Route::get('/{route}/{id}', [Detail::class, 'asController'])->name('detail');
Route::post('/{route}/{id}/upload', Upload::class)->name('shared.upload');
