<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

final class PreventiveTaskResourceData extends Data
{
    public function __construct(
        public string $activity,
        public string $frequency,
        public int    $intervalo_semanas,
        public int    $semana_referencia,
        public bool   $is_vencido,
    ) {}

    public static function fromStdClass(object $row): self
    {
        $weeks = match ($row->frequency ?? '') {
            'Diaria'     => 1,
            'Semanal'    => 1,
            'Mensual'    => 4,
            'Trimestral' => 13,
            'Semestral'  => 26,
            'Anual'      => 52,
            default      => 1,
        };

        $lastDate = isset($row->last_performed_at) && $row->last_performed_at !== null
            ? Carbon::parse((string) $row->last_performed_at)
            : null;

        $refWeek   = $lastDate !== null ? (int) $lastDate->format('W') : 1;
        $isVencido = $lastDate !== null && $lastDate->copy()->addWeeks($weeks)->isPast();

        return new self(
            activity:          $row->activity ?? '---',
            frequency:         $row->frequency ?? '---',
            intervalo_semanas: $weeks,
            semana_referencia: $refWeek,
            is_vencido:        $isVencido,
        );
    }
}
