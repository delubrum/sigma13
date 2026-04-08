<?php

declare(strict_types=1);

namespace App\Actions\Recruitment;

use App\Data\Recruitment\CreateRecruitment;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Models\Recruitment;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class Create
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        $profiles = DB::table('job_profiles')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($p) => [(string) $p->id => $p->name])
            ->all();

        return new Config(
            title: 'New Recruitment',
            subtitle: 'HR / Recruitment',
            icon: 'ri-user-search-line',
            modalWidth: '50%',
            multipart: true,
            formFields: [
                new Field(name: 'profile_id', label: 'Profile', required: true, type: 'select', options: ['' => '—'] + $profiles, widget: 'slimselect', width: FieldWidth::Full),
                new Field(name: 'approver', label: 'Approver Email', required: true, type: 'email', placeholder: 'approver@example.com', width: FieldWidth::Half),
                new Field(name: 'city', label: 'City', required: false, width: FieldWidth::Half),
                new Field(name: 'qty', label: 'Quantity', required: true, type: 'number', width: FieldWidth::Half),
                new Field(name: 'contract', label: 'Contract Type', required: false, type: 'select', options: ['Fixed' => 'Fixed', 'Indefinite' => 'Indefinite', 'Apprentice' => 'Apprentice'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'work_mode', label: 'Work Mode', required: true, type: 'select', options: ['On-site' => 'On-site', 'Remote' => 'Remote', 'Hybrid' => 'Hybrid'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'cause', label: 'Reason/Cause', required: false, type: 'select', options: ['New Job Code' => 'New Job Code', 'Replacement' => 'Replacement', 'Extra Worker' => 'Extra Worker'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'srange', label: 'Salary Range', required: false, width: FieldWidth::Half),
                new Field(name: 'replaces', label: 'Replaces (If applicable)', required: false, width: FieldWidth::Half),
                new Field(name: 'start_date', label: 'Expected Start Date', required: false, type: 'date', width: FieldWidth::Half),
                new Field(name: 'others', label: 'Additional details', required: false, type: 'textarea'),
                new Field(name: 'file', label: 'Curriculum (ZIP)', type: 'file', widget: 'filepond'),
            ],
        );
    }

    public function handle(): View
    {
        $config = $this->config();

        $this->hxModalHeader([
            'icon'     => $config->icon,
            'title'    => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route'  => 'recruitment',
            'config' => $config,
        ]);
    }

    public function asController(Request $request): Response
    {
        return $this->hxView($this->handle());
    }

    public function asStore(Request $request): JsonResponse
    {
        $data = CreateRecruitment::from($request->all());

        $recruitment = Recruitment::create([
            'user_id'    => Auth::id(),
            'profile_id' => $data->profile_id,
            'approver'   => $data->approver,
            'city'       => $data->city,
            'qty'        => $data->qty,
            'contract'   => $data->contract,
            'work_mode'  => $request->input('work_mode'),
            'cause'      => $data->cause,
            'srange'     => $data->srange,
            'replaces'   => $data->replaces,
            'start_date' => $data->start_date ? \Carbon\Carbon::parse($data->start_date) : null,
            'others'     => $data->others,
            'complexity' => 15,
            'status'     => 'approval',
            'created_at' => now(),
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid() && $file->getClientOriginalExtension() === 'zip') {
                $dir = public_path('uploads/recruitment/candidates');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $file->move($dir, "{$recruitment->id}.zip");
            }
        }

        $this->hxNotify('Recruitment request created.');
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
