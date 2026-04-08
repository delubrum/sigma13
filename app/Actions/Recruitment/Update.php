<?php

declare(strict_types=1);

namespace App\Actions\Recruitment;

use App\Models\Recruitment;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Update
{
    use AsAction;
    use HtmxOrchestrator;

    public function asController(Request $request, int $id): JsonResponse
    {
        $field = $request->string('field')->toString();
        $recruitment = Recruitment::findOrFail($id);

        match ($field) {
            'profile_id' => $recruitment->update(['profile_id' => $request->integer('value')]),
            'approver'   => $recruitment->update(['approver' => $request->string('value')->toString()]),
            'assignee_id'=> $recruitment->update(['assignee_id' => $request->integer('value')]),
            'city'       => $recruitment->update(['city' => $request->string('value')->toString()]),
            'qty'        => $recruitment->update(['qty' => $request->integer('value')]),
            'contract'   => $recruitment->update(['contract' => $request->string('value')->toString()]),
            'cause'      => $recruitment->update(['cause' => $request->string('value')->toString()]),
            'srange'     => $recruitment->update(['srange' => $request->string('value')->toString()]),
            'replaces'   => $recruitment->update(['replaces' => $request->string('value')->toString()]),
            'others'     => $recruitment->update(['others' => $request->string('value')->toString()]),

            // State changes
            'approved_at' => $recruitment->update(['status' => 'approved', 'approved_at' => now()]),
            'rejected_at' => $recruitment->update(['status' => 'rejected', 'rejected_at' => now(), 'rejection' => $request->string('rejection')->toString()]),
            'closed_at'   => $recruitment->update(['status' => 'closed', 'closed_at' => now()]),

            default => abort(422, "Campo no permitido: {$field}"),
        };

        $this->hxNotify('Guardado');

        return $this->hxResponse();
    }
}
