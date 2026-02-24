<section class="w-full px-4 py-6 lg:px-8">
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ $this->pageTitle }}</flux:heading>
                <flux:text class="mt-1">{{ __('Despacha una ejecución contra un target activo con una recipe activa.') }}</flux:text>
            </div>

            <flux:button variant="ghost" :href="route('executions.index')" wire:navigate>
                {{ __('Volver') }}
            </flux:button>
        </div>

        <form wire:submit="submit" class="space-y-6 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model="recipe_id" :label="__('Recipe')">
                    <option value="">{{ __('Selecciona una recipe') }}</option>
                    @foreach ($recipes as $recipe)
                        <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="target_id" :label="__('Target')">
                    <option value="">{{ __('Selecciona un target') }}</option>
                    @foreach ($targets as $target)
                        <option value="{{ $target->id }}">
                            {{ $target->name }} ({{ $target->user }}@{{ $target->host }})
                        </option>
                    @endforeach
                </flux:select>
            </div>

            <flux:textarea
                wire:model="paramsJson"
                :label="__('Parámetros (JSON)')"
                rows="12"
                :placeholder="__('Ejemplo: {&quot;branch&quot;:&quot;main&quot;,&quot;force&quot;:false}')"
            />

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit">
                    {{ __('Lanzar') }}
                </flux:button>

                <flux:button variant="ghost" :href="route('executions.index')" wire:navigate>
                    {{ __('Cancelar') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>

