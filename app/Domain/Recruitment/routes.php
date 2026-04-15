<?php

declare(strict_types=1);

use App\Domain\Recruitment\Web\Adapters\AssignAdapter;
use App\Domain\Recruitment\Web\Adapters\IndexAdapter;
use App\Domain\Recruitment\Web\Adapters\Modals\CandidateModalAdapter;
use App\Domain\Recruitment\Web\Adapters\Modals\CandidateStatusAdapter;
use App\Domain\Recruitment\Web\Adapters\PatchAdapter;
use App\Domain\Recruitment\Web\Adapters\RejectAdapter;
use App\Domain\Recruitment\Web\Adapters\ResendApprovalAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('recruitment')->name('recruitment.')->group(function (): void {

    // Index + data
    Route::get('/', IndexAdapter::class)->name('index');
    Route::get('/data', fn (Request $r): JsonResponse => (new IndexAdapter)->asData($r))->name('data');

    // Shared global orchestrators handle: create, upsert, detail, delete
    // GET  /recruitment/create  → global.create
    // POST /recruitment/upsert  → global.upsert
    // GET  /recruitment/{id}    → global.detail
    // DELETE /recruitment/{id}  → global.delete

    // Inline patch
    Route::post('/{id}/patch', fn (int $id, Request $r): JsonResponse => (new PatchAdapter)->asController($id, $r))->name('patch');

    // Status actions
    Route::post('/{id}/reject', fn (int $id, Request $r): JsonResponse => (new RejectAdapter)->asController($id, $r))->name('reject');

    // Assign recruiter
    Route::get('/{id}/assign', fn (int $id): Response => (new AssignAdapter)->asController($id))->name('assign');
    Route::post('/{id}/assign', fn (int $id, Request $r): JsonResponse => (new AssignAdapter)->save($id, $r))->name('assign.save');

    // Resend approval email
    Route::post('/{id}/resend-approval', fn (int $id): JsonResponse => (new ResendApprovalAdapter)->asController($id))->name('resend-approval');

    // Candidates tab data
    Route::get('/{recruitmentId}/candidates/data',
        fn (int $recruitmentId, Request $r): JsonResponse => (new IndexAdapter)->asCandidatesData($recruitmentId, $r)
    )->name('candidates.data');

    // Candidate CRUD
    Route::get('/{recruitmentId}/candidates/create',
        fn (int $recruitmentId): Response => (new CandidateModalAdapter)->asCreate($recruitmentId)
    )->name('candidates.create');

    Route::get('/{recruitmentId}/candidates/{candidateId}/edit',
        fn (int $recruitmentId, int $candidateId): Response => (new CandidateModalAdapter)->asEdit($recruitmentId, $candidateId)
    )->name('candidates.edit');

    Route::post('/candidates/upsert',
        fn (Request $r): JsonResponse => (new CandidateModalAdapter)->upsert($r)
    )->name('candidates.upsert');

    Route::delete('/candidates/{candidateId}',
        fn (int $candidateId): JsonResponse => (new CandidateModalAdapter)->destroy($candidateId)
    )->name('candidates.delete');

    // Candidate status patch
    Route::post('/candidates/{candidateId}/status',
        fn (int $candidateId, Request $r): JsonResponse => (new CandidateStatusAdapter)->asController($candidateId, $r)
    )->name('candidates.status');

});
