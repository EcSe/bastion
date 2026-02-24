<?php

use App\Livewire\Recipes\Form as RecipeForm;
use App\Livewire\Recipes\Index as RecipeIndex;
use App\Models\Recipe;
use App\Models\User;
use Livewire\Livewire;

it('allows authenticated users to create a recipe', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(RecipeForm::class)
        ->set('slug', 'deploy-api')
        ->set('name', 'Deploy API')
        ->set('description', 'Deploy seguro a producción')
        ->set('risk_level', 2)
        ->set('timeout_sec', 900)
        ->set('parametersJson', '{"branch":{"type":"string","enum":["main","develop"]}}')
        ->set('steps', "- name: pull\n  run: git pull\n- name: migrate\n  run: php artisan migrate --force")
        ->set('version', '1.1.0')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('recipes.index', absolute: false));

    $recipe = Recipe::query()->where('slug', 'deploy-api')->firstOrFail();

    expect($recipe->parameters)->toBeArray()
        ->and($recipe->parameters['branch']['type'])->toBe('string')
        ->and($recipe->is_active)->toBeTrue();
});

it('validates slug uniqueness when creating recipes', function (): void {
    $this->actingAs(User::factory()->create());

    $existingRecipe = Recipe::factory()->create([
        'slug' => 'deploy-api',
    ]);

    Livewire::test(RecipeForm::class)
        ->set('slug', $existingRecipe->slug)
        ->set('name', 'Deploy Duplicate')
        ->set('description', 'Repite slug')
        ->set('risk_level', 1)
        ->set('timeout_sec', 120)
        ->set('parametersJson', '{}')
        ->set('steps', '- name: check\n  run: php artisan about')
        ->set('version', '1.0.0')
        ->set('is_active', true)
        ->call('save')
        ->assertHasErrors(['slug']);
});

it('validates parameters as JSON when creating recipes', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(RecipeForm::class)
        ->set('slug', 'invalid-params')
        ->set('name', 'Invalid Params')
        ->set('description', 'JSON inválido')
        ->set('risk_level', 1)
        ->set('timeout_sec', 120)
        ->set('parametersJson', '{"branch":')
        ->set('steps', '- name: check\n  run: php artisan about')
        ->set('version', '1.0.0')
        ->set('is_active', true)
        ->call('save')
        ->assertHasErrors(['parametersJson']);
});

it('does not allow whitespace-only steps when creating recipes', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(RecipeForm::class)
        ->set('slug', 'invalid-steps')
        ->set('name', 'Invalid Steps')
        ->set('description', 'Steps vacíos')
        ->set('risk_level', 1)
        ->set('timeout_sec', 120)
        ->set('parametersJson', '{}')
        ->set('steps', '   ')
        ->set('version', '1.0.0')
        ->set('is_active', true)
        ->call('save')
        ->assertHasErrors(['steps']);
});

it('returns not found for invalid recipe edit route parameter', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get(route('recipes.edit', ['recipe' => 'invalid-id']))
        ->assertNotFound();
});

it('allows activating and deactivating recipes', function (): void {
    $this->actingAs(User::factory()->create());

    $recipe = Recipe::factory()->create([
        'is_active' => true,
    ]);

    Livewire::test(RecipeIndex::class)
        ->call('deactivate', $recipe->id);

    expect($recipe->fresh()?->is_active)->toBeFalse();

    Livewire::test(RecipeIndex::class)
        ->call('activate', $recipe->id);

    expect($recipe->fresh()?->is_active)->toBeTrue();
});
