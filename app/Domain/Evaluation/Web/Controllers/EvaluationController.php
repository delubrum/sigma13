<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Web\Controllers;

use App\Domain\Evaluation\Actions\GetEvaluationsAction;
use App\Domain\Shared\Data\Column;
use App\Domain\Shared\Data\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EvaluationController
{
    public function index(): View
    {
        $config = new Config(
            title: 'Suppliers Evaluation',
            icon: 'ri-survey-line',
            subtitle: 'Overview of supplier performance and compliance',
            columns: [
                Column::make('Date', 'date', 150, 'center'),
                Column::make('User', 'user', 150),
                Column::make('Nit', 'nit', 120, 'center'),
                Column::make('Supplier', 'supplier', null, 'left'),
                Column::make('Type', 'type', 120, 'center'),
                Column::make('Result', 'result', 100, 'center', 'progress', ['min' => 0, 'max' => 100, 'legend' => true]),
            ]
        );

        return view('evaluation::index', [
            'config' => $config,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $result = GetEvaluationsAction::run(
            page: (int) $request->get('page', 1),
            size: (int) $request->get('size', 25),
            filters: $request->get('filter', [])
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }

    public function detail(int $id): View
    {
        // Placeholder for detail logic
        return view('evaluation::detail', ['id' => $id]);
    }
}
