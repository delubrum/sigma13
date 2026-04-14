<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Domain\Recruitment\Data\JobProfileTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class JobProfilesAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'recruitment.profiles',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'SGC / Job Profiles',
            subtitle:       'Definición de perfiles de cargo',
            icon:           'ri-profile-line',
            newButtonLabel: 'New Profile',
            modalWidth:     '80',
            columns:        SchemaGenerator::toColumns(JobProfileTableData::class),
            formFields:     [],
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        return response()->json([
            'data'      => [],
            'last_page' => 1,
            'last_row'  => 0,
        ]);
    }
}
