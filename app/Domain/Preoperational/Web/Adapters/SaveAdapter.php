<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Domain\Preoperational\Actions\SaveAnswersAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveAdapter
{
    use AsAction;

    public function handle(Request $request): Response
    {
        $id = $request->integer('id');

        if (! $id) {
            return response()->noContent();
        }

        SaveAnswersAction::run($id, $request->all());

        $headers = json_encode([
            'showMessage' => [
                'type' => 'success',
                'message' => 'Progreso guardado',
                'close' => '',
            ],
        ]);

        return response('')->header('HX-Trigger', $headers ?: '{}');
    }

    public function asController(Request $request): Response
    {
        return $this->handle($request);
    }
}
