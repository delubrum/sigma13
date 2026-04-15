<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Web\Adapters;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class TabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(int $id, Request $request): Response
    {
        $tab = (string) $request->query('tab', 'functions');
        $kind = $this->kindForTab($tab);

        $row = DB::table('job_profiles')->where('id', $id)->first();
        if ($row === null) {
            abort(404);
        }

        $itemRow = DB::table('job_profile_items')
            ->where('jp_id', $id)
            ->where('kind', $kind)
            ->first();

        $content = $itemRow ? $itemRow->content : '[]';
        $canEdit = Auth::check();

        $extra = [];
        if ($tab === 'resources') {
            $extra['assets'] = DB::table('hr_db')
                ->where('kind', 'asset')
                ->orderBy('name')
                ->pluck('name')
                ->toArray();
        }

        if ($tab === 'risk') {
            $area = DB::table('hr_db')->where('id', $row->division_id)->value('area') ?? '';
            $extra['risks'] = DB::table('hr_db')
                ->where('kind', 'risk')
                ->where(static function ($q) use ($area): void {
                    $q->where('area', $area)->orWhere('area', 'ALL');
                })
                ->orderBy('name')
                ->pluck('name')
                ->toArray();
        }

        return $this->hxView("job-profiles::tabs.{$tab}", array_merge([
            'jpId' => $id,
            'kind' => $kind,
            'content' => $content,
            'canEdit' => $canEdit,
        ], $extra));
    }

    private function kindForTab(string $tab): string
    {
        return match ($tab) {
            'functions' => 'Funciones',
            'resources' => 'Recursos',
            'areas' => 'Responsabilidades SGI',
            'education' => 'Educación',
            'training' => 'Formación',
            'skills' => 'Competencias',
            'risk' => 'Riesgos',
            default => $tab,
        };
    }
}
