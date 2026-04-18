<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Web\Adapters\Modals;

use App\Domain\HelpDesk\Actions\CreateIssueTaskAction;
use App\Domain\HelpDesk\Data\TaskUpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class TaskModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title:      'Nueva Tarea',
            icon:       'ri-time-line',
            modalWidth: '30',
            multipart:  true,
            formFields: SchemaGenerator::toFields(TaskUpsertData::class),
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();
        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components::new-modal', [
            'route'           => "helpdesk/{$id}/tasks",
            'customPostRoute' => "/helpdesk/{$id}/tasks",
            'config'          => $config,
            'target'          => '#modal-body-2',
            'closeEvent'      => 'close-modal-2',
            'suffix'          => '-2',
            'data'            => ['issue_id' => $id],
        ]);
    }

    public function asController(Request $request, int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $data = TaskUpsertData::validateAndCreate(array_merge($request->except('files'), ['issue_id' => $id]));

        $item = CreateIssueTaskAction::run($data);

        $file = $request->file('files');
        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
            $dest = sys_get_temp_dir().'/'.uniqid().'.webp';
            $imagick = new \Imagick($file->getRealPath());
            $imagick->resizeImage(1280, 1280, \Imagick::FILTER_LANCZOS, 1, true);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(65);
            $imagick->writeImage($dest);
            $imagick->clear();
            $item->addMedia($dest)
                ->usingFileName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.webp')
                ->toMediaCollection('evidence');
        }

        $this->hxNotify('Tarea registrada correctamente');
        $this->hxRefreshTables(["dt_helpdesk_tasks_{$id}"]);
        $this->hxRefresh(['#sidebar-summary']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
