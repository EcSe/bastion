<section class="w-full px-4 py-6 lg:px-8">
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ $this->pageTitle }}</flux:heading>
                <flux:text class="mt-1">{{ __('Define parámetros, riesgo y pasos de la recipe.') }}</flux:text>
            </div>

            <flux:button variant="ghost" :href="route('recipes.index')" wire:navigate>
                {{ __('Volver') }}
            </flux:button>
        </div>

        <form wire:submit="save" class="space-y-6 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="slug" :label="__('Slug')" type="text" required />
                <flux:input wire:model="name" :label="__('Nombre')" type="text" required />
                <flux:select wire:model="risk_level" :label="__('Nivel de riesgo')">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </flux:select>
                <flux:input wire:model="timeout_sec" :label="__('Timeout (segundos)')" type="number" min="1" max="3600" required />
                <flux:input wire:model="version" :label="__('Versión')" type="text" required />
                <flux:checkbox wire:model="is_active" :label="__('Activa')" />
            </div>

            <flux:textarea wire:model="description" :label="__('Descripción')" rows="3" />

            <flux:textarea
                wire:model="parametersJson"
                :label="__('Parámetros (JSON)')"
                rows="8"
                :placeholder="__('Ejemplo: {\"branch\":{\"type\":\"string\",\"enum\":[\"main\",\"develop\"]}}')"
            />

            <flux:textarea
                wire:model="steps"
                :label="__('Steps (YAML/texto)')"
                rows="10"
                :placeholder="__('Ejemplo: - name: pull\n  run: git pull')"
                required
            />

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit">
                    {{ __('Guardar') }}
                </flux:button>

                <flux:button variant="ghost" :href="route('recipes.index')" wire:navigate>
                    {{ __('Cancelar') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>
