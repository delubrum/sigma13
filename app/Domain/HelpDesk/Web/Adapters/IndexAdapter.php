<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasPatch;
use App\Domain\HelpDesk\Actions\GetIssueDataAction;
use App\Domain\HelpDesk\Actions\GetIssueSidebarAction;
use App\Domain\HelpDesk\Data\ActionsData;
use App\Domain\HelpDesk\Data\SidebarData;
use App\Domain\HelpDesk\Data\TableData;
use App\Domain\HelpDesk\Data\TabsData;
use App\Domain\HelpDesk\Data\UpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Adapters\AbstractIndexAdapter;

final class IndexAdapter extends AbstractIndexAdapter implements HasDetail, HasPatch
{
    protected function route(): string
    {
        return 'helpdesk';
    }

    public function config(): Config
    {
        return new Config(
            title:          'Help Desk',
            icon:           'ri-customer-service-2-line',
            subtitle:       'Service Desk de Infraestructura y Mantenimiento',
            newButtonLabel: 'Nuevo Ticket',
            modalWidth:     '40',
            multipart:      true,
            columns:        SchemaGenerator::toColumns(TableData::class),
            formFields:     SchemaGenerator::toFields(UpsertData::class),
            tabs:           SchemaGenerator::toTabs(TabsData::class),
            options:        SchemaGenerator::toOptions(ActionsData::class),
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetIssueSidebarAction::run($id);

        return $result;
    }

    public function patchConfig(int $id): array
    {
        return [
            'table'  => 'issues',
            'fields' => [
                'priority'    => [],
                'status'      => [],
                'facility'    => [],
                'asset_id'    => [],
                'assignee_id' => ['#sidebar-summary', "#tab-helpdesk_tasks_{$id}-container"],
                'sgc_code'    => [],
                'root_cause'  => [],
                'rating'      => [],
            ],
        ];
    }

    protected function getData(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        /** @var PaginatedResult<TableData> $result */
        $result = GetIssueDataAction::run(filters: $filters, sorts: $sorts, page: $page, size: $size);

        return $result;
    }
}
