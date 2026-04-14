<?php

declare(strict_types=1);

use App\Domain\Operations\Web\Adapters\DocumentsAdapter;
use App\Domain\Operations\Web\Adapters\EquipmentAdapter;
use App\Domain\Operations\Web\Adapters\EvaluationAdapter;
use App\Domain\Operations\Web\Adapters\ImprovementAdapter;
use App\Domain\Operations\Web\Adapters\InspectionsAdapter;
use App\Domain\Operations\Web\Adapters\PpeAdapter;
use App\Domain\Operations\Web\Adapters\PrintAdapter;
use App\Domain\Operations\Web\Adapters\TicketsAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Improvement
    Route::get('operations/improvement', ImprovementAdapter::class)->name('operations.improvement');
    Route::get('operations/improvement/data', [ImprovementAdapter::class, 'asData'])->name('operations.improvement.data');

    // Inspections
    Route::get('operations/inspections', InspectionsAdapter::class)->name('operations.inspections');
    Route::get('operations/inspections/data', [InspectionsAdapter::class, 'asData'])->name('operations.inspections.data');

    // Equipment
    Route::get('operations/equipment', EquipmentAdapter::class)->name('operations.equipment');
    Route::get('operations/equipment/data', [EquipmentAdapter::class, 'asData'])->name('operations.equipment.data');

    // PPE
    Route::get('operations/ppe', PpeAdapter::class)->name('operations.ppe');
    Route::get('operations/ppe/data', [PpeAdapter::class, 'asData'])->name('operations.ppe.data');

    // Evaluation
    Route::get('operations/evaluation', EvaluationAdapter::class)->name('operations.evaluation');
    Route::get('operations/evaluation/data', [EvaluationAdapter::class, 'asData'])->name('operations.evaluation.data');

    // Documents
    Route::get('operations/documents', DocumentsAdapter::class)->name('operations.documents');
    Route::get('operations/documents/data', [DocumentsAdapter::class, 'asData'])->name('operations.documents.data');

    // Tickets
    Route::get('operations/tickets', TicketsAdapter::class)->name('operations.tickets');
    Route::get('operations/tickets/data', [TicketsAdapter::class, 'asData'])->name('operations.tickets.data');

    // Print
    Route::get('operations/print', PrintAdapter::class)->name('operations.print');
    Route::get('operations/print/data', [PrintAdapter::class, 'asData'])->name('operations.print.data');
});
