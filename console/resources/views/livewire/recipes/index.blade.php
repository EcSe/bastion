<section class="w-full px-4 py-6 lg:px-8">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Recipes') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Catálogo de recipes disponibles para la operación.') }}</flux:text>
            </div>

            <flux:button variant="primary" :href="route('recipes.create')" wire:navigate>
                {{ __('Nueva recipe') }}
            </flux:button>
        </div>

        @if (session('status') === 'recipe-saved')
            <div class="rounded-lg border border-zinc-200 px-4 py-3 text-sm dark:border-zinc-700">
                {{ __('Recipe guardada.') }}
            </div>
        @endif

        @if (session('status') === 'recipe-updated')
            <div class="rounded-lg border border-zinc-200 px-4 py-3 text-sm dark:border-zinc-700">
                {{ __('Recipe actualizada.') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100/60 dark:bg-zinc-900/60">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Slug') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Nombre') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Riesgo') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Timeout') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Estado') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($recipes as $recipe)
                        <tr wire:key="recipe-{{ $recipe->id }}" class="bg-white dark:bg-zinc-800/50">
                            <td class="px-4 py-3 text-sm font-medium">{{ $recipe->slug }}</td>
                            <td class="px-4 py-3 text-sm">{{ $recipe->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $recipe->risk_level }}</td>
                            <td class="px-4 py-3 text-sm">{{ $recipe->timeout_sec }}s</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $recipe->is_active ? __('Activa') : __('Inactiva') }}
                            </td>
                            <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <flux:button size="sm" variant="ghost" :href="route('executions.create', ['recipe_id' => $recipe->id])" wire:navigate>
                                {{ __('Ejecutar') }}
                            </flux:button>

                            <flux:button size="sm" variant="ghost" :href="route('recipes.edit', $recipe)" wire:navigate>
                                {{ __('Editar') }}
                            </flux:button>

                                    @if ($recipe->is_active)
                                        <flux:button size="sm" variant="subtle" wire:click="deactivate({{ $recipe->id }})">
                                            {{ __('Desactivar') }}
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" variant="subtle" wire:click="activate({{ $recipe->id }})">
                                            {{ __('Reactivar') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-zinc-500">
                                {{ __('Aún no hay recipes registradas.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $recipes->links() }}
        </div>
    </div>
</section>
