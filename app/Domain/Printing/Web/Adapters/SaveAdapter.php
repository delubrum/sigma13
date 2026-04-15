<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters;

use App\Domain\Printing\Actions\ParseWoExcelAction;
use App\Domain\Printing\Models\Wo;
use App\Domain\Printing\Models\WoItem;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Honeypot\ProtectAgainstSpam;
use Throwable;

final class SaveAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx'],
            'qr_image' => ['required', 'file', 'image'],
        ]);

        try {
            $parsed = ParseWoExcelAction::run($request->file('excel_file'));
        } catch (Throwable $e) {
            $this->hxNotify('Error al leer Excel: '.$e->getMessage(), 'error');

            return $this->hxResponse([], 422);
        }

        $code = $parsed['code'];

        if (Wo::where('code', $code)->exists()) {
            $this->hxNotify("WO {$code} ya existe.", 'error');

            return $this->hxResponse([], 409);
        }

        DB::transaction(function () use ($parsed, $code, $request): void {
            $wo = Wo::create([
                'code' => $code,
                'project' => $parsed['project'],
                'es_id' => $request->input('es_id'),
                'user_id' => Auth::id(),
            ]);

            foreach ($parsed['items'] as $item) {
                WoItem::create([
                    'wo_code' => $code,
                    'code' => $item['code'],
                    'description' => $item['description'],
                    'fuc' => $item['fuc'],
                    'qty' => $item['qty'],
                ]);
            }

            Storage::disk('public')->putFileAs(
                "print/{$code}",
                $request->file('qr_image'),
                'qr.png'
            );
        });

        $this->hxNotify("WO {$code} guardada correctamente");
        $this->hxRefreshTables(['dt_printing']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }

    public function asController(Request $request): JsonResponse
    {
        return $this->handle($request);
    }

    public function middleware(): array
    {
        return [ProtectAgainstSpam::class];
    }
}
