<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HtmxOrchestrator
{
    /** @var array<string, mixed> */
    protected array $hxTriggers = [];

    public function hxNotify(string $message, string $type = 'success'): static
    {
        $this->hxTriggers['notify'] = ['type' => $type, 'message' => $message];

        return $this;
    }

    /** @param string[] $ids */
    public function hxCloseModals(array $ids): static
    {
        $this->hxTriggers['close-modals'] = ['ids' => $ids];

        return $this;
    }

    /** @param string[] $ids */
    public function hxRefresh(array $ids): static
    {
        $this->hxTriggers['refresh-divs'] = ['ids' => $ids];

        return $this;
    }

    /** @param string[] $ids */
    public function hxRefreshTables(array $ids): static
    {
        $this->hxTriggers['refresh-tables'] = ['ids' => $ids];

        return $this;
    }

    /** @param array{icon: string, title: string, subtitle: string} $header */
    public function hxModalHeader(array $header): static
    {
        $this->hxTriggers['update-modal-header'] = $header;

        return $this;
    }

    public function hxModalWidth(string $width): static
    {
        $this->hxTriggers['set-modal-width'] = ['width' => $width];

        return $this;
    }

    /** @param array<string, mixed> $data */
    public function hxResponse(array $data = [], int $status = 200): JsonResponse
    {
        $notify = $this->hxTriggers['notify'] ?? null;
        if ($notify !== null && is_array($notify) && $data === []) {
            $data = ['message' => $notify['message'] ?? ''];
        }

        return response()->json($data, $status)->withHeaders([
            'HX-Trigger' => json_encode($this->hxTriggers),
        ]);
    }

    public function hxView(View $view): Response
    {
        return response($view->render())->withHeaders([
            'HX-Trigger' => json_encode($this->hxTriggers),
        ]);
    }

    public function hxRedirect(string $to): JsonResponse
    {
        return response()->json(['redirect' => $to])->withHeaders([
            'HX-Redirect' => $to,
            'HX-Trigger' => json_encode($this->hxTriggers),
        ]);
    }
}
