<?php

use App\Services\CoreClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('sends bearer token to the core health endpoint', function (): void {
    config()->set('services.bastion_core.url', 'http://127.0.0.1:8787');
    config()->set('services.bastion_core.token', 'unit-test-token');

    Http::fake([
        'http://127.0.0.1:8787/v1/health' => Http::response([
            'status' => 'ok',
            'version' => 'v1',
            'db' => ['status' => 'ok'],
        ]),
    ]);

    $response = app(CoreClient::class)->health();

    expect($response['status'])->toBe('ok')
        ->and($response['version'])->toBe('v1')
        ->and($response['db']['status'])->toBe('ok');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'http://127.0.0.1:8787/v1/health'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer unit-test-token');
    });
});
