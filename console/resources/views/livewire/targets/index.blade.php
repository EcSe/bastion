<section class="w-full px-4 py-6 lg:px-8">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Targets') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Inventario de servidores/hosts disponibles para recetas.') }}</flux:text>
            </div>

            <flux:button variant="primary" :href="route('targets.create')" wire:navigate>
                {{ __('Nuevo target') }}
            </flux:button>
        </div>

        @if (session('status') === 'target-saved')
            <div class="rounded-lg border border-zinc-200 px-4 py-3 text-sm dark:border-zinc-700">
                {{ __('Target guardado.') }}
            </div>
        @endif

        @if (session('status') === 'target-updated')
            <div class="rounded-lg border border-zinc-200 px-4 py-3 text-sm dark:border-zinc-700">
                {{ __('Target actualizado.') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100/60 dark:bg-zinc-900/60">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Nombre') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Host') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Método') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Estado') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($targets as $target)
                        <tr wire:key="target-{{ $target->id }}" class="bg-white dark:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $target->name }}</div>
                                <div class="text-sm text-zinc-500">{{ $target->user.'@'.$target->host.':'.$target->port }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $target->host }}</td>
                            <td class="px-4 py-3 text-sm">{{ $target->auth_method }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $target->is_active ? __('Activo') : __('Inactivo') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" :href="route('targets.edit', $target)" wire:navigate>
                                        {{ __('Editar') }}
                                    </flux:button>

                                    @if ($target->is_active)
                                        <flux:button size="sm" variant="subtle" wire:click="deactivate({{ $target->id }})">
                                            {{ __('Desactivar') }}
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" variant="subtle" wire:click="activate({{ $target->id }})">
                                            {{ __('Reactivar') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-zinc-500">
                                {{ __('Aún no hay targets registrados.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $targets->links() }}
        </div>
    </div>
</section>
