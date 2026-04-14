<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Shared\Data\Config;
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

    public function hxTrigger(string $name, mixed $params = null): static
    {
        $this->hxTriggers[$name] = $params ?? true;

        return $this;
    }

    /** @param string[] $ids */
    public function hxCloseModals(array $ids): static
    {
        $this->hxTriggers['close-modals'] = ['ids' => $ids];

        return $this;
    }

    /** @param string[] $ids */
    public function hxRefresh(array $ids = []): static
    {
        $this->hxTriggers['refresh-divs'] = ['ids' => $ids];

        return $this;
    }

    /** @param string[] $ids */
    public function hxRefreshTables(array $ids = []): static
    {
        $this->hxTriggers['refresh-tables'] = ['ids' => $ids];

        return $this;
    }

    /** @param array{icon: string, title: string, subtitle: string|null} $header */
    public function hxModalHeader(array $header, string $suffix = ''): static
    {
        $this->hxTriggers['update-modal-header'.$suffix] = $header;

        return $this;
    }

    public function hxModalWidth(?string $width, string $suffix = ''): static
    {
        if ($width === null) {
            return $this;
        }

        $map = [
            '10' => 'max-w-[10%]', '20' => 'max-w-[20%]', '30' => 'max-w-[30%]', '40' => 'max-w-[40%]',
            '50' => 'max-w-[50%]', '60' => 'max-w-[60%]', '70' => 'max-w-[70%]', '80' => 'max-w-[80%]',
            '90' => 'max-w-[90%]', '98' => 'max-w-none', '100' => 'max-w-full',
            'xs' => 'max-w-xs', 'sm' => 'max-w-sm', 'md' => 'max-w-2xl', 'lg' => 'max-w-4xl',
            'xl' => 'max-w-6xl', '2xl' => 'max-w-7xl', 'full' => 'max-w-none',
        ];

        // Limpiar el valor (por si viene con %)
        $cleanWidth = str_replace('%', '', $width);
        $finalWidth = $map[$cleanWidth] ?? ($map[$width] ?? $width);

        $this->hxTriggers['set-modal-width'.$suffix] = ['width' => $finalWidth];

        return $this;
    }

    public function hxModalActions(string $html, string $suffix = ''): static
    {
        $this->hxTriggers['update-modal-actions'.$suffix] = ['html' => $html];

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
            'HX-Reswap' => 'none', // Evita que HTMX intente renderizar el JSON en el modal
        ]);
    }

    /**
     * @param  view-string|View|string  $view
     * @param  array<string, mixed>  $data
     */
    public function hxView(string|View $view, array $data = []): Response
    {
        // Obtener datos si es un objeto View
        $viewData = $view instanceof View ? $view->getData() : $data;
        $config = $viewData['config'] ?? null;
        $suffix = (string) ($viewData['suffix'] ?? '');

        // Si hay un objeto Config, automatizar cabeceras, anchos y ACCIONES (si no se han definido manualmente)
        if ($config instanceof Config) {
            $viewName = $view instanceof View ? $view->name() : $view;
            /** @var string $viewName */
            $viewName = str_replace(['::', '/'], '.', $viewName); // Normalizar
            $isModalView = str_contains($viewName, 'modal');

            // Automatizar cabecera solo si es vista de tipo modal y no se ha definido manualmente
            if ($isModalView && ! isset($this->hxTriggers['update-modal-header'.$suffix])) {
                $this->hxModalHeader([
                    'icon' => $config->icon,
                    'title' => $config->title,
                    'subtitle' => $config->subtitle,
                ], $suffix);
            }

            // Ancho inteligente (solo si no se ha definido manualmente y es una vista de tipo modal)
            // Solo automatizar si es explícitamente una vista de tipo modal
            if (! isset($this->hxTriggers['set-modal-width'.$suffix]) && $isModalView) {
                $defaultWidth = str_contains($viewName, 'detail-modal') ? '98' : 'md';
                $this->hxModalWidth($config->modalWidth ?? $defaultWidth, $suffix);
            }

            // Automatizar Menú de Acciones (Opciones) - Solo si no se ha definido manualmente
            if (count($config->options) > 0 && ! isset($this->hxTriggers['update-modal-actions'.$suffix])) {
                $actionsHtml = view('components::modal-actions', [
                    'options' => $config->options,
                    'id' => $viewData['id'] ?? null,
                    'suffix' => $suffix,
                ])->render();

                $this->hxModalActions($actionsHtml, $suffix);
            }
        }

        if ($view instanceof View) {
            $rendered = $view->render();
        } else {
            /** @var view-string $viewStr */
            $viewStr = $view;
            $rendered = view($viewStr, $data)->render();
        }

        return response($rendered)->withHeaders([
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
