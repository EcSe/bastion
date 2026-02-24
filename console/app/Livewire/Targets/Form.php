<?php

namespace App\Livewire\Targets;

use App\Models\Target;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Form extends Component
{
    public ?Target $target = null;

    public string $name = '';

    public string $host = '';

    public int $port = 22;

    public string $user = '';

    public string $auth_method = 'ssh_key';

    public string $key_path = '';

    public string $secret_ref = '';

    public string $tagsJson = '';

    public bool $is_active = true;

    public function mount(mixed $target = null): void
    {
        if ($target === null) {
            return;
        }

        if ($target instanceof Target) {
            $this->target = $target;
        } elseif (is_numeric($target)) {
            $this->target = Target::query()->findOrFail((int) $target);
        } else {
            abort(404);
        }

        $this->name = $this->target->name;
        $this->host = $this->target->host;
        $this->port = $this->target->port;
        $this->user = $this->target->user;
        $this->auth_method = $this->target->auth_method;
        $this->key_path = $this->target->key_path ?? '';
        $this->secret_ref = $this->target->secret_ref ?? '';
        $this->tagsJson = $this->target->tags !== null
            ? (string) json_encode($this->target->tags, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->is_active = $this->target->is_active;
    }

    public function updatedAuthMethod(string $value): void
    {
        if ($value === 'ssh_key') {
            $this->secret_ref = '';
        }

        if ($value === 'password') {
            $this->key_path = '';
        }
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());

        $tags = $this->decodeJsonArray($this->tagsJson, 'tagsJson');

        $payload = [
            'name' => $validated['name'],
            'host' => $validated['host'],
            'port' => $validated['port'],
            'user' => $validated['user'],
            'auth_method' => $validated['auth_method'],
            'key_path' => $validated['auth_method'] === 'ssh_key' ? $validated['key_path'] : null,
            'secret_ref' => $validated['auth_method'] === 'password' ? $validated['secret_ref'] : null,
            'tags' => $tags,
            'is_active' => $validated['is_active'],
        ];

        if ($this->target instanceof Target) {
            $this->target->update($payload);
        } else {
            Target::query()->create($payload);
        }

        session()->flash('status', 'target-saved');

        $this->redirect(route('targets.index', absolute: false), navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'user' => ['required', 'string', 'max:255'],
            'auth_method' => ['required', Rule::in(['ssh_key', 'password'])],
            'key_path' => ['nullable', 'string', 'max:255', 'required_if:auth_method,ssh_key'],
            'secret_ref' => ['nullable', 'string', 'max:255', 'required_if:auth_method,password'],
            'tagsJson' => ['nullable', 'json'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'key_path.required_if' => __('La ruta de llave es obligatoria para auth_method ssh_key.'),
            'secret_ref.required_if' => __('La referencia de secreto es obligatoria para auth_method password.'),
            'tagsJson.json' => __('Tags debe ser un JSON válido.'),
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
        return $this->target instanceof Target;
    }

    public function getPageTitleProperty(): string
    {
        if ($this->isEditing) {
            return __('Editar target');
        }

        return __('Nuevo target');
    }

    public function render(): View
    {
        return view('livewire.targets.form');
    }
}
