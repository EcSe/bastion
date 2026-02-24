<?php

use App\Livewire\Executions\Form as ExecutionForm;
use App\Models\Execution;
use App\Models\Recipe;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('creates a queued execution and dispatches request to core', function (): void {
    config()->set('services.bastion_core.url', 'http://127.0.0.1:8787');
    config()->set('services.bastion_core.token', 'unit-test-token');

    Http::fake([
        'http://127.0.0.1:8787/v1/execute' => Http::response([
            'status' => 'queued',
        ]),
    ]);

    $user = User::factory()->create();
    $recipe = Recipe::factory()->create([
        'is_active' => true,
    ]);
    $target = Target::factory()->create([
        'is_active' => true,
    ]);

    Livewire::actingAs($user)->test(ExecutionForm::class)
        ->set('recipe_id', $recipe->id)
        ->set('target_id', $target->id)
        ->set('paramsJson', '{"branch":"main"}')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('executions.index', absolute: false));

    $execution = Execution::query()
        ->where('recipe_id', $recipe->id)
        ->where('target_id', $target->id)
        ->firstOrFail();

    expect($execution->status)->toBe('queued')
        ->and($execution->params['branch'])->toBe('main')
        ->and($execution->requested_by)->toBe($user->id);

    Http::assertSent(function (Request $request) use ($recipe, $target): bool {
        return $request->url() === 'http://127.0.0.1:8787/v1/execute'
            && $request->method() === 'POST'
            && $request->data()['recipe_id'] === $recipe->id
            && $request->data()['target_id'] === $target->id
            && $request->data()['params']['branch'] === 'main';
    });
});
