<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Form extends Component
{
    public ?Recipe $recipe = null;

    public string $slug = '';

    public string $name = '';

    public string $description = '';

    public int $risk_level = 0;

    public int $timeout_sec = 300;

    public string $parametersJson = '';

    public string $steps = '';

    public string $version = '1.0.0';

    public bool $is_active = true;

    public function mount(mixed $recipe = null): void
    {
        if ($recipe instanceof Recipe) {
            $this->recipe = $recipe;
        } elseif (is_numeric($recipe)) {
            $this->recipe = Recipe::query()->findOrFail((int) $recipe);
        } else {
            return;
        }

        $this->slug = $this->recipe->slug;
        $this->name = $this->recipe->name;
        $this->description = $this->recipe->description ?? '';
        $this->risk_level = $this->recipe->risk_level;
        $this->timeout_sec = $this->recipe->timeout_sec;
        $this->parametersJson = $this->recipe->parameters !== null
            ? (string) json_encode($this->recipe->parameters, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->steps = $this->recipe->steps;
        $this->version = $this->recipe->version;
        $this->is_active = $this->recipe->is_active;
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());

        $parameters = $this->decodeJsonArray($this->parametersJson, 'parametersJson');

        $payload = [
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'description' => $validated['description'] !== '' ? $validated['description'] : null,
            'risk_level' => $validated['risk_level'],
            'timeout_sec' => $validated['timeout_sec'],
            'parameters' => $parameters,
            'steps' => trim($validated['steps']),
            'version' => $validated['version'],
            'is_active' => $validated['is_active'],
        ];

        if ($this->recipe instanceof Recipe) {
            $this->recipe->update($payload);
        } else {
            Recipe::query()->create($payload);
        }

        session()->flash('status', 'recipe-saved');

        $this->redirect(route('recipes.index', absolute: false), navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'slug' => [
                'required',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique(Recipe::class, 'slug')->ignore($this->recipe?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'risk_level' => ['required', 'integer', 'between:0,3'],
            'timeout_sec' => ['required', 'integer', 'between:1,3600'],
            'parametersJson' => ['nullable', 'json'],
            'steps' => ['required', 'string', 'min:1'],
            'version' => ['required', 'string', 'max:32'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'parametersJson.json' => __('Parameters debe ser un JSON válido.'),
            'slug.unique' => __('Ya existe una recipe con este slug.'),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonArray(?string $value, string $field): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                $field => __('El campo debe representar un objeto o arreglo JSON.'),
            ]);
        }

        return $decoded;
    }

    public function getIsEditingProperty(): bool
    {
        return $this->recipe instanceof Recipe;
    }

    public function getPageTitleProperty(): string
    {
        if ($this->isEditing) {
            return __('Editar recipe');
        }

        return __('Nueva recipe');
    }

    public function render(): View
    {
        return view('livewire.recipes.form');
    }
}
