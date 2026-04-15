<?php

declare(strict_types=1);

use App\Domain\Ppe\Web\Adapters\MovementsTabAdapter;
use App\Domain\Ppe\Web\Adapters\Modals\NewDeliveryAdapter;
use App\Domain\Ppe\Web\Adapters\PpeAdminIndexAdapter;
use App\Domain\Ppe\Web\Adapters\PpeDeliveriesIndexAdapter;
use App\Domain\Ppe\Web\Adapters\PpeEntriesIndexAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {

    // PPE Admin (catalog)
    Route::prefix('ppe-admin')->name('ppe-admin.')->group(function (): void {
        Route::get('/',      PpeAdminIndexAdapter::class)->name('index');
        Route::get('/data',  [PpeAdminIndexAdapter::class, 'asData'])->name('data');
    });

    // PPE Entries (stock per item)
    Route::prefix('ppe-entries')->name('ppe-entries.')->group(function (): void {
        Route::get('/',      PpeEntriesIndexAdapter::class)->name('index');
        Route::get('/data',  [PpeEntriesIndexAdapter::class, 'asData'])->name('data');

        // Movements tab
        Route::get('/{id}/tab/movements',      MovementsTabAdapter::class)->name('tab.movements');
        Route::get('/{id}/tab/movements/data', [MovementsTabAdapter::class, 'asData'])->name('tab.movements.data');
    });

    // PPE Deliveries
    Route::prefix('ppe-deliveries')->name('ppe-deliveries.')->group(function (): void {
        Route::get('/',     PpeDeliveriesIndexAdapter::class)->name('index');
        Route::get('/data', [PpeDeliveriesIndexAdapter::class, 'asData'])->name('data');
    });

    // Shared PPE routes
    Route::prefix('ppe')->name('ppe.')->group(function (): void {
        // Options
        Route::get('/options/items', static function (): \Illuminate\Http\JsonResponse {
            $items = DB::table('epp_db')->orderBy('name')->get(['id', 'name']);

            return response()->json($items->map(fn ($i) => ['value' => $i->id, 'text' => $i->name]));
        })->name('options.items');

        Route::get('/options/employees', static function (): \Illuminate\Http\JsonResponse {
            $employees = DB::table('employees')->orderBy('name')->get(['id', 'name']);

            return response()->json($employees->map(fn ($e) => ['value' => $e->id, 'text' => $e->name]));
        })->name('options.employees');

        // Delivery modal
        Route::get('/deliveries/new',  [NewDeliveryAdapter::class, 'asGet'])->name('deliveries.new');
        Route::post('/deliveries/save', [NewDeliveryAdapter::class, 'asSave'])->name('deliveries.save');
    });
});
