<?php

declare(strict_types=1);

namespace App\Domain\Printing\Actions;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

final class FetchEsDataAction
{
    use AsAction;

    /**
     * @return list<array<string, mixed>>
     */
    public function handle(string $esId): array
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withoutVerifying()
            ->post('https://portalnova.eswllc.net/sv/api/fussion/OrderDetailESMetals', [
                'text'    => '',
                'idOrden' => $esId,
                'mcdId'   => 1,
                'token'   => config('services.es_metals.token'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException("ES API error for ID {$esId}: {$response->status()}");
        }

        $data = $response->json('data');

        return is_array($data) ? $data : [];
    }
}
