<?php

use App\Livewire\Targets\Form as TargetForm;
use App\Livewire\Targets\Index as TargetIndex;
use App\Models\Target;
use App\Models\User;
use Livewire\Livewire;

it('allows authenticated users to create a target', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(TargetForm::class)
        ->set('name', 'API Production')
        ->set('host', '10.10.0.20')
        ->set('port', 22)
        ->set('user', 'deploy')
        ->set('auth_method', 'ssh_key')
        ->set('key_path', '/home/deploy/.ssh/id_ed25519')
        ->set('secret_ref', '')
        ->set('tagsJson', '{"env":"prod","role":"app"}')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('targets.index', absolute: false));

    $target = Target::query()->where('name', 'API Production')->firstOrFail();

    expect($target->tags)->toBeArray()
        ->and($target->tags['env'])->toBe('prod')
        ->and($target->is_active)->toBeTrue();
});

it('validates auth_method specific fields for targets', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(TargetForm::class)
        ->set('name', 'Missing Key Path')
        ->set('host', '10.0.0.1')
        ->set('port', 22)
        ->set('user', 'ops')
        ->set('auth_method', 'ssh_key')
        ->set('key_path', '')
        ->set('secret_ref', '')
        ->set('tagsJson', '')
        ->set('is_active', true)
        ->call('save')
        ->assertHasErrors(['key_path']);

    Livewire::test(TargetForm::class)
        ->set('name', 'Missing Secret Ref')
        ->set('host', '10.0.0.2')
        ->set('port', 22)
        ->set('user', 'ops')
        ->set('auth_method', 'password')
        ->set('key_path', '')
        ->set('secret_ref', '')
        ->set('tagsJson', '')
        ->set('is_active', true)
        ->call('save')
        ->assertHasErrors(['secret_ref']);
});

it('returns not found for invalid target edit route parameter', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get(route('targets.edit', ['target' => 'invalid-id']))
        ->assertNotFound();
});

it('allows activating and deactivating targets', function (): void {
    $this->actingAs(User::factory()->create());

    $target = Target::factory()->create([
        'is_active' => true,
    ]);

    Livewire::test(TargetIndex::class)
        ->call('deactivate', $target->id);

    expect($target->fresh()?->is_active)->toBeFalse();

    Livewire::test(TargetIndex::class)
        ->call('activate', $target->id);

    expect($target->fresh()?->is_active)->toBeTrue();
});
