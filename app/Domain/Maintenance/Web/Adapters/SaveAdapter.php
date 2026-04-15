<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters;

use App\Domain\Maintenance\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveAdapter
{
    use AsAction;

    public function handle(Request $request): Response
    {
        $userId = Auth::id() ?? 93;

        $data = $request->except(['id', 'files', '_token', 'kind']);
        array_walk_recursive($data, function (&$item) {
            if (is_string($item)) {
                $item = htmlspecialchars(trim($item));
            }
        });

        $data['user_id'] = $userId;
        $data['status'] = 'Open';
        $data['kind'] = $request->input('kind', 'Machinery');

        $maintenance = Maintenance::create($data);

        if ($request->hasFile('files')) {
            $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'jfif'];
            $carpeta = "uploads/mnt/userpics/{$maintenance->id}/";
            $path = public_path($carpeta);

            if (! is_dir($path)) {
                mkdir($path, 0777, true);
            }

            foreach ($request->file('files') as $file) {
                if ($file->isValid() && in_array(strtolower($file->getClientOriginalExtension()), $allowedTypes)) {
                    $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
                    $file->move($path, $fileName);
                }
            }
        }

        $message = [
            'type' => 'success',
            'message' => 'Maintenance saved',
            'close' => 'closeNewModal',
        ];

        return response('')->header('HX-Trigger', json_encode([
            'eventChanged' => true,
            'showMessage' => $message,
        ]));
    }

    public function asController(Request $request): Response
    {
        $request->validate([
            'facility' => 'required|string',
            'asset_id' => 'required|integer',
            'priority' => 'required|string',
            'description' => 'required|string',
        ]);

        return $this->handle($request);
    }
}
