<?php

declare(strict_types=1);

use App\Domain\Improvement\Web\Adapters\CancelAdapter;
use App\Domain\Improvement\Web\Adapters\CloseAdapter;
use App\Domain\Improvement\Web\Adapters\IndexAdapter;
use App\Domain\Improvement\Web\Adapters\Modals\ActivityModalAdapter;
use App\Domain\Improvement\Web\Adapters\Modals\CauseModalAdapter;
use App\Domain\Improvement\Web\Adapters\Modals\CloseModalAdapter;
use App\Domain\Improvement\Web\Adapters\PatchAdapter;
use App\Domain\Improvement\Web\Adapters\RejectAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('improvement')->name('improvement.')->group(function (): void {

    // Index + data
    Route::get('/', IndexAdapter::class)->name('index');
    Route::get('/data', fn (Request $r): JsonResponse => (new IndexAdapter)->asData($r))->name('data');

    // Shared global orchestrators handle: create, upsert, detail, delete
    // GET  /improvement/create      → global.create (Shared/Web/Actions/Create.php)
    // POST /improvement/upsert      → global.upsert  (Shared/Web/Actions/Upsert.php)
    // GET  /improvement/{id}        → detail          (Shared/Web/Actions/Detail.php)
    // DELETE /improvement/{id}      → global.delete   (Shared/Web/Actions/Delete.php)

    // Inline patch (aim, goal, user_ids)
    Route::post('/{id}/patch', fn (int $id, Request $r): JsonResponse => (new PatchAdapter)->asController($id, $r))->name('patch');

    // Status transitions
    Route::post('/{id}/reject', fn (int $id, Request $r): JsonResponse => (new RejectAdapter)->asController($id, $r))->name('reject');
    Route::post('/{id}/cancel', fn (int $id, Request $r): JsonResponse => (new CancelAdapter)->asController($id, $r))->name('cancel');

    // Close modal (GET) + save (POST)
    Route::get('/{id}/close', fn (int $id): Response => (new CloseAdapter)->asController($id))->name('close');
    Route::post('/{id}/close', fn (int $id, Request $r): JsonResponse => (new CloseModalAdapter)->asController($id, $r))->name('close.save');

    // Causes
    Route::get('/{id}/causes/create', fn (int $id): Response => (new CauseModalAdapter)->asCreate($id))->name('causes.create');
    Route::get('/{id}/causes/{causeId}', fn (int $id, int $causeId): Response => (new CauseModalAdapter)->asShow($id, $causeId))->name('causes.show');
    Route::get('/{id}/causes/data', fn (int $id, Request $r): JsonResponse => (new CauseModalAdapter)->asData($id, $r))->name('causes.data');
    Route::post('/causes/upsert', fn (Request $r): JsonResponse => (new CauseModalAdapter)->upsert($r))->name('causes.upsert');
    Route::delete('/causes/{causeId}', fn (int $causeId): JsonResponse => (new CauseModalAdapter)->destroy($causeId))->name('causes.delete');

    // Activities
    Route::get('/{id}/activities/create', fn (int $id): Response => (new ActivityModalAdapter)->asCreate($id))->name('activities.create');
    Route::get('/{id}/activities/{actId}/edit', fn (int $id, int $actId): Response => (new ActivityModalAdapter)->asEdit($id, $actId))->name('activities.edit');
    Route::get('/{id}/activities/{actId}/close', fn (int $id, int $actId): Response => (new ActivityModalAdapter)->asClose($id, $actId))->name('activities.close');
    Route::get('/{id}/activities/data', fn (int $id, Request $r): JsonResponse => (new ActivityModalAdapter)->asData($id, $r))->name('activities.data');
    Route::post('/activities/upsert', fn (Request $r): JsonResponse => (new ActivityModalAdapter)->upsert($r))->name('activities.upsert');
    Route::delete('/activities/{actId}', fn (int $actId): JsonResponse => (new ActivityModalAdapter)->destroy($actId))->name('activities.delete');

});
