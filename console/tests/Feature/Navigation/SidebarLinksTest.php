<?php

use App\Models\Recipe;
use App\Models\Target;
use App\Models\User;

it('shows targets and recipes links in the dashboard for authenticated users', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Targets')
        ->assertSee('Recipes');
});

it('requires authentication for inventory routes', function (): void {
    $target = Target::factory()->create();
    $recipe = Recipe::factory()->create();

    $this->get(route('targets.index'))->assertRedirect(route('login'));
    $this->get(route('targets.create'))->assertRedirect(route('login'));
    $this->get(route('targets.edit', $target))->assertRedirect(route('login'));

    $this->get(route('recipes.index'))->assertRedirect(route('login'));
    $this->get(route('recipes.create'))->assertRedirect(route('login'));
    $this->get(route('recipes.edit', $recipe))->assertRedirect(route('login'));
});

it('allows authenticated users to open inventory pages', function (): void {
    $user = User::factory()->create();
    $target = Target::factory()->create();
    $recipe = Recipe::factory()->create();

    $this->actingAs($user)->get(route('targets.index'))->assertOk();
    $this->actingAs($user)->get(route('targets.create'))->assertOk();
    $this->actingAs($user)->get(route('targets.edit', $target))->assertOk();

    $this->actingAs($user)->get(route('recipes.index'))->assertOk();
    $this->actingAs($user)->get(route('recipes.create'))->assertOk();
    $this->actingAs($user)->get(route('recipes.edit', $recipe))->assertOk();
});
