<?php

namespace App\Livewire\Executions;

use App\Models\Execution;
use App\Models\Recipe;
use App\Models\Target;
use App\Services\CoreClient;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Form extends Component
{
    public ?int $recipe_id = null;

    public ?int $target_id = null;

    public string $paramsJson = '';

    public function mount(): void
    {
        $this->recipe_id = $this->toPositiveInt(request()->query('recipe_id'));
        $this->target_id = $this->toPositiveInt(request()->query('target_id'));
    }

    public function submit(): void
    {
        $validated = $this->validate($this->rules());
        $params = $this->decodeJsonObject($validated['paramsJson']);

        $execution = Execution::query()->create([
            'recipe_id' => $validated['recipe_id'],
            'target_id' => $validated['target_id'],
            'status' => 'queued',
            'requested_by' => auth()->id(),
            'params' => $params,
        ]);

        $coreClient = app(CoreClient::class);
        $coreResponse = $coreClient->execute(
            $validated['recipe_id'],
            $validated['target_id'],
            $params,
        );

        if (! $coreResponse['ok']) {
            $errorSummary = $coreResponse['error'] ?? __('No se pudo despachar la ejecución al core.');

            $execution->update([
                'status' => 'failed',
                'finished_at' => now(),
                'exit_code' => 1,
                'error_summary' => $errorSummary,
            ]);

            $execution->executionLogs()->create([
                'ts' => now(),
                'stream' => 'system',
                'line' => $errorSummary,
            ]);

            session()->flash('status', 'execution-failed');

            $this->redirect(route('executions.index', absolute: false), navigate: true);

            return;
        }

        $responseBody = $coreResponse['body'];

        if (is_array($responseBody)) {
            $executionStatus = $this->normalizeStatus($responseBody['status'] ?? null);
            $executionSummary = $responseBody['summary'] ?? null;

            $execution->update([
                'status' => $executionStatus,
            ]);

            if (is_string($executionSummary) && $executionSummary !== '') {
                $execution->update([
                    'error_summary' => $executionSummary,
                ]);
            }
        }

        session()->flash('status', 'execution-saved');

        $this->redirect(route('executions.index', absolute: false), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.executions.form', [
            'recipes' => Recipe::query()->where('is_active', true)->orderBy('name')->get(),
            'targets' => Target::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function getPageTitleProperty(): string
    {
        return __('Nueva ejecución');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'recipe_id' => [
                'required',
                'integer',
                Rule::exists('recipes', 'id')->where('is_active', true),
            ],
            'target_id' => [
                'required',
                'integer',
                Rule::exists('targets', 'id')->where('is_active', true),
            ],
            'paramsJson' => ['nullable', 'json'],
        ];
    }

    private function toPositiveInt(mixed $value): ?int
    {
        if (! is_numeric($value) || (int) $value <= 0) {
            return null;
        }

        return (int) $value;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonObject(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'paramsJson' => __('El campo debe ser un JSON válido con objeto o arreglo.'),
            ]);
        }

        return $decoded;
    }

    private function normalizeStatus(mixed $value): string
    {
        return in_array($value, ['queued', 'running', 'success', 'failed', 'cancelled'], true)
            ? $value
            : 'queued';
    }
}
