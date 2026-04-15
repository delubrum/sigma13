<?php

declare(strict_types=1);

use App\Domain\Extrusion\Web\Adapters\AdminIndexAdapter;
use App\Domain\Extrusion\Web\Adapters\IndexAdapter;
use App\Domain\Extrusion\Web\Adapters\PatchAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {

    // ── Main index ──────────────────────────────────────────────────────────
    Route::prefix('extrusion')->name('extrusion.')->group(function (): void {

        Route::get('/',             IndexAdapter::class)->name('index');
        Route::get('/data',         [IndexAdapter::class, 'asData'])->name('data');
        Route::get('/filter-options', [IndexAdapter::class, 'filterOptions'])->name('filter-options');
        Route::get('/ai-search',    [IndexAdapter::class, 'aiSearch'])->name('ai-search');
        Route::get('/ai-data',      [IndexAdapter::class, 'aiData'])->name('ai-data');

        // Options endpoints (slimselect)
        Route::get('/options/companies', static function (): \Illuminate\Http\JsonResponse {
            $rows = DB::table('matrices')
                ->distinct()->orderBy('company_id')
                ->whereNotNull('company_id')->where('company_id', '!=', '')
                ->pluck('company_id');

            return response()->json($rows->map(fn ($v) => ['value' => $v, 'text' => $v]));
        })->name('options.companies');

        Route::get('/options/categories', static function (): \Illuminate\Http\JsonResponse {
            $rows = DB::table('matrices_db')
                ->where('kind', 'Category')->orderBy('name')->pluck('name');

            return response()->json($rows->map(fn ($v) => ['value' => $v, 'text' => $v]));
        })->name('options.categories');

        // Patch / upload / delete-file (inline sidebar edits)
        Route::post('/{id}/patch',       [PatchAdapter::class, 'asController'])->name('patch');
        Route::post('/{id}/upload',      [PatchAdapter::class, 'uploadFile'])->name('upload');
        Route::post('/{id}/delete-file', [PatchAdapter::class, 'deleteFile'])->name('delete-file');
    });

    // ── Admin (matrices_db) ──────────────────────────────────────────────────
    Route::prefix('extrusion-admin')->name('extrusion-admin.')->group(function (): void {
        Route::get('/',     AdminIndexAdapter::class)->name('index');
        Route::get('/data', [AdminIndexAdapter::class, 'asData'])->name('data');
    });
});
