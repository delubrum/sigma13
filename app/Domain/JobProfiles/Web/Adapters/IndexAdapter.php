<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\JobProfiles\Actions\GetJobProfileDataAction;
use App\Domain\JobProfiles\Actions\GetJobProfileSidebarAction;
use App\Domain\JobProfiles\Data\SidebarData;
use App\Domain\JobProfiles\Data\TableData;
use App\Domain\JobProfiles\Data\UpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class IndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'job-profiles',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Perfiles de Cargo',
            icon: 'ri-briefcase-line',
            subtitle: 'Gestión de perfiles y competencias del cargo',
            newButtonLabel: 'Nuevo Perfil',
            modalWidth: '70',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'functions', label: 'Funciones', icon: 'ri-file-list-line', route: 'job-profiles.tab.functions', default: true),
                new Tabs(key: 'resources', label: 'Recursos', icon: 'ri-tools-line', route: 'job-profiles.tab.resources', default: false),
                new Tabs(key: 'areas', label: 'Resp. SGI', icon: 'ri-layout-line', route: 'job-profiles.tab.areas', default: false),
                new Tabs(key: 'education', label: 'Educación', icon: 'ri-graduation-cap-line', route: 'job-profiles.tab.education', default: false),
                new Tabs(key: 'training', label: 'Formación', icon: 'ri-book-open-line', route: 'job-profiles.tab.training', default: false),
                new Tabs(key: 'skills', label: 'Competencias', icon: 'ri-medal-line', route: 'job-profiles.tab.skills', default: false),
                new Tabs(key: 'risk', label: 'Riesgos', icon: 'ri-alert-line', route: 'job-profiles.tab.risk', default: false),
            ],
            options: [],
        );
    }

    public function sidebarData(int $id): Data
    {
        /** @var SidebarData $result */
        $result = GetJobProfileSidebarAction::run($id);

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
        $result = GetJobProfileDataAction::run(
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
