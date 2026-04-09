<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions\Tabs;

use App\Domain\Recruitment\Models\Recruitment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class Candidates
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $recruitment = Recruitment::findOrFail($id);

        $candidates = DB::table('recruitment_candidates')
            ->where('recruitment_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return view('recruitment.tabs.candidates', ['id' => $id, 'recruitment' => $recruitment, 'candidates' => $candidates]);
    }
}
