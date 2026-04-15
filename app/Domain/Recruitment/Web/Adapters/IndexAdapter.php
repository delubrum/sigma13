<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Recruitment\Actions\GetCandidatesDataAction;
use App\Domain\Recruitment\Actions\GetRecruitmentDataAction;
use App\Domain\Recruitment\Actions\GetRecruitmentSidebarAction;
use App\Domain\Recruitment\Data\SidebarData;
use App\Domain\Recruitment\Data\TableData;
use App\Domain\Recruitment\Data\UpsertData;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class IndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'recruitment',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Reclutamiento',
            icon: 'ri-user-search-line',
            subtitle: 'Gestión de requisiciones y candidatos',
            newButtonLabel: 'Nueva Requisición',
            modalWidth: '70',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'candidates', label: 'Candidatos', icon: 'ri-group-line', route: 'recruitment.candidates', default: true),
            ],
            options: [
                new ActionOption(
                    label: 'Asignar Reclutador',
                    icon: 'ri-user-add-line',
                    route: 'recruitment/assign',
                    target: '#modal-body-2',
                    level: 2,
                    method: 'GET',
                ),
                new ActionOption(
                    label: 'Reenviar Aprobación',
                    icon: 'ri-mail-send-line',
                    route: 'recruitment/resend-approval',
                    target: '#modal-body-2',
                    level: 2,
                    method: 'GET',
                ),
                new ActionOption(
                    label: 'Rechazar',
                    icon: 'ri-close-circle-line',
                    route: 'recruitment/reject',
                    target: '#modal-body-2',
                    level: 2,
                    method: 'GET',
                ),
            ],
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetRecruitmentSidebarAction::run($id);

        return $result;
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts = $request->collect('sort')->pluck('dir', 'field')->toArray();

        /** @var PaginatedResult<TableData> $result */
        $result = GetRecruitmentDataAction::run(
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15),
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }

    public function asCandidatesData(int $id, Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $result = GetCandidatesDataAction::run(
            recruitmentId: $id,
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15),
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }
}
