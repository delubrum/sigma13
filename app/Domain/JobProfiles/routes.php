<?php

declare(strict_types=1);

use App\Domain\JobProfiles\Web\Adapters\IndexAdapter;
use App\Domain\JobProfiles\Web\Adapters\SaveItemAdapter;
use App\Domain\JobProfiles\Web\Adapters\SaveResourceAdapter;
use App\Domain\JobProfiles\Web\Adapters\TabAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('job-profiles')->name('job-profiles.')->group(function (): void {
    // Index + data
    Route::get('/', IndexAdapter::class)->name('index');
    Route::get('/data', static fn (Request $r): JsonResponse => (new IndexAdapter)->asData($r))->name('data');

    // Shared: create, upsert, detail, delete registered in Shared/routes.php

    // Tab routes — each must be a named route, used by Tabs::route in IndexAdapter
    Route::get('/{id}/tab/functions', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'functions'])))->name('tab.functions');
    Route::get('/{id}/tab/resources', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'resources'])))->name('tab.resources');
    Route::get('/{id}/tab/areas', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'areas'])))->name('tab.areas');
    Route::get('/{id}/tab/education', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'education'])))->name('tab.education');
    Route::get('/{id}/tab/training', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'training'])))->name('tab.training');
    Route::get('/{id}/tab/skills', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'skills'])))->name('tab.skills');
    Route::get('/{id}/tab/risk', static fn (int $id, Request $r): Response => (new TabAdapter)->asController($id, $r->merge(['tab' => 'risk'])))->name('tab.risk');

    // Item save (jspreadsheet JSON)
    Route::post('/save-item', [SaveItemAdapter::class,    'asController'])->name('save-item');
    Route::post('/save-resource', [SaveResourceAdapter::class, 'asController'])->name('save-resource');

    // Options endpoints for slimselect
    Route::get('/options/divisions', static function (): JsonResponse {
        $rows = DB::table('hr_db')->where('kind', 'division')->orderBy('name')->get(['id', 'name']);

        return response()->json($rows->map(static fn ($r): array => ['value' => $r->id, 'label' => $r->name]));
    })->name('options.divisions');

    Route::get('/options/positions', static function (): JsonResponse {
        $rows = DB::table('hr_db')->where('kind', 'position')->orderBy('name')->get(['id', 'name']);

        return response()->json($rows->map(static fn ($r): array => ['value' => $r->id, 'label' => $r->name]));
    })->name('options.positions');
});
