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

    /**
     * @return array{
     *     ok: bool,
     *     status: int|null,
     *     body: array<string, mixed>|null,
     *     error: string|null,
     * }
     */
    public function execute(int $recipeId, int $targetId, array $params = []): array
    {
        try {
            $response = $this->request()
                ->post('/v1/execute', [
                    'recipe_id' => $recipeId,
                    'target_id' => $targetId,
                    'params' => $params,
                ]);

            $body = $response->json();

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'body' => is_array($body) ? $body : null,
                'error' => $response->successful() ? null : $response->body(),
            ];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'status' => null,
                'body' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl((string) config('services.bastion_core.url'))
            ->acceptJson()
            ->withToken((string) config('services.bastion_core.token'))
            ->timeout(10);
    }
}
