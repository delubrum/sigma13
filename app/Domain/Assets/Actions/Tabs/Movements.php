<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Tabs;

use App\Contracts\HasModule;
use App\Domain\Assets\Data\Tabs\MovementTable;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Movements implements HasModule
{
    use AsAction;

    public function config(): Config
    {
        return new Config(
            title: 'Movimientos del Activo',
            subtitle: '',
            icon: 'ri-arrow-left-right-line',
            newButtonLabel: '',
            columns: MovementTable::columns(),
        );
    }

    public function handle(int $id): View
    {
        $asset = Asset::findOrFail($id);

        $customRoute = null;
        $btnClass = null;

        if ($asset->status === 'available') {
            $customRoute = route('assets.assignments.create', $id);
        } elseif ($asset->status === 'assigned') {
            $customRoute = route('assets.returns.create', $id);
            $btnClass = 'px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider flex items-center space-x-1.5 transition-all outline-none bg-red-600 text-white hover:bg-red-700';
        }

        return view('components.tab-index', [
            'config' => $this->config(),
            'parentId' => $id,
            'route' => 'assets.movements',
            'customCreateRoute' => $customRoute,
            'newButtonClass' => $btnClass,
        ]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }

    public function asData(Request $request, int $id): JsonResponse
    {
        $size = max(1, $request->integer('size', 10));
        $page = max(1, $request->integer('page', 1));

        $paginator = AssetEvent::query()
            ->where('asset_id', $id)
            ->whereIn('kind', ['assignment', 'return'])
            ->with(['employee'])
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        $isFirstPage = $page === 1;

        return response()->json([
            'data' => $paginator->map(
                fn (AssetEvent $event, int $i): MovementTable => MovementTable::fromModel($event, $isFirstPage && $i === 0)
            )->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
