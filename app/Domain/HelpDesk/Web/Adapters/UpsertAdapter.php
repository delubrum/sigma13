<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Web\Adapters;

use App\Domain\HelpDesk\Models\Issue;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpsertAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'kind'        => 'required|string|in:IT,Locative,Machinery,OHS',
            'facility'    => 'required|string|max:100',
            'asset_id'    => 'nullable|integer',
            'priority'    => 'required|string|in:High,Medium,Low',
            'description' => 'required|string',
        ]);

        $needsAsset = in_array($request->input('kind'), ['Machinery', 'Locative'], true);

        $issue = Issue::create([
            'kind'        => $request->input('kind'),
            'facility'    => $request->input('facility'),
            'asset_id'    => $needsAsset ? $request->integer('asset_id') : null,
            'priority'    => $request->input('priority'),
            'description' => $request->input('description'),
            'reporter_id' => Auth::id(),
            'status'      => 'Open',
        ]);

        $file = $request->file('files');
        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
            $dest = sys_get_temp_dir().'/'.uniqid().'.webp';
            $imagick = new \Imagick($file->getRealPath());
            $imagick->resizeImage(1280, 1280, \Imagick::FILTER_LANCZOS, 1, true);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(65);
            $imagick->writeImage($dest);
            $imagick->clear();
            $issue->addMedia($dest)
                ->usingFileName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.webp')
                ->toMediaCollection('photos');
        }

        $this->hxNotify('Ticket creado correctamente');
        $this->hxRefreshTables(['dt_helpdesk']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }

    public function asController(Request $request): JsonResponse
    {
        return $this->handle($request);
    }
}
