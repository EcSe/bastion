<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CoreClient
{
    /**
     * @return array{status: string, version: string, db: array{status: string}}
     */
    public function health(): array
    {
        /** @var array{status: string, version: string, db: array{status: string}} $response */
        $response = $this->request()
            ->get('/v1/health')
            ->throw()
            ->json();

        return $response;
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl((string) config('services.bastion_core.url'))
            ->acceptJson()
            ->withToken((string) config('services.bastion_core.token'))
            ->timeout(10);
    }
}
