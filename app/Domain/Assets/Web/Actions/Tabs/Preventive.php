<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Domain\Maintenance\Models\MntPreventiveForm;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Preventive
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(?Asset $asset = null): Config
    {
        return new Config(
            title: $asset ? "Cronograma: <span class='opacity-50'>{$asset->serial}</span>" : 'Cronograma Preventivo',
            subtitle: $asset ? $asset->name : '',
            icon: 'ri-calendar-check-line',
            columns: [],
            formFields: []
        );
    }

    public function handle(Asset $asset): Response
    {
        $automations = MntPreventiveForm::where('asset_id', $asset->id)->get()->map(function (MntPreventiveForm $task): object {
            $frecuenciaTexto = strtolower(trim((string) $task->frequency));

            /** @var array{int, int} $params */
            $params = match ($frecuenciaTexto) {
                'weekly' => [7, 1],
                'monthly' => [30, 4],
                'quarterly' => [90, 13],
                'semiannual' => [180, 26],
                'annual' => [365, 52],
                'annualx2' => [730, 104],
                'annualx3' => [1095, 156],
                'annualx5' => [1825, 260],
                default => is_numeric($task->frequency)
                    ? [(int) $task->frequency, (int) ceil(((int) $task->frequency) / 7)]
                    : [30, 4],
            };

            [$dias, $intervaloSemanas] = $params;

            $val = $task->last_performed_at;
            $fechaLastStr = $val instanceof \DateTimeInterface
                ? $val->format('Y-m-d')
                : (is_string($val) ? $val : date('Y-m-d'));

            $fechaLast = new DateTime($fechaLastStr);
            $fechaNext = clone $fechaLast;
            $fechaNext->modify("+{$dias} days");

            $semanaReferencia = (int) $fechaLast->format('W');
            $fechaProxima = $fechaNext->format('d/m/Y');

            $hoy = new DateTime;
            $isVencido = ($fechaNext < $hoy);
            $color = $isVencido ? 'bg-red-500' : 'bg-indigo-600';

            return (object) [
                'activity' => $task->activity,
                'frequency' => $task->frequency,
                'intervalo_semanas' => $intervaloSemanas,
                'semana_referencia' => $semanaReferencia,
                'fecha_proxima' => $fechaProxima,
                'is_vencido' => $isVencido,
                'color' => $color,
            ];
        });

        return $this->hxView('assets::tabs.preventive', [
            'asset' => $asset,
            'automations' => $automations,
            'config' => $this->config($asset),
        ]);
    }

    public function asController(Asset $asset): Response
    {
        return $this->handle($asset);
    }
}
