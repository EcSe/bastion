<section class="w-full px-4 py-6 lg:px-8">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Ejecuciones') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Historial operativo y estado de ejecución de recipes.') }}</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:button variant="ghost" wire:click="clearFilter" :disabled="$statusFilter === null">
                    {{ __('Todo') }}
                </flux:button>
                <flux:button variant="primary" :href="route('executions.create')" wire:navigate>
                    {{ __('Nueva ejecución') }}
                </flux:button>
            </div>
        </div>

        <div class="flex gap-2">
            <flux:button size="sm" variant="subtle" wire:click="filter('queued')">
                {{ __('Queued') }}
            </flux:button>
            <flux:button size="sm" variant="subtle" wire:click="filter('running')">
                {{ __('Running') }}
            </flux:button>
            <flux:button size="sm" variant="subtle" wire:click="filter('success')">
                {{ __('Success') }}
            </flux:button>
            <flux:button size="sm" variant="subtle" wire:click="filter('failed')">
                {{ __('Failed') }}
            </flux:button>
        </div>

        @if (session('status') === 'execution-saved')
            <div class="rounded-lg border border-zinc-200 px-4 py-3 text-sm dark:border-zinc-700">
                {{ __('Ejecución despachada al core.') }}
            </div>
        @endif

        @if (session('status') === 'execution-failed')
            <div class="rounded-lg border border-red-300 px-4 py-3 text-sm dark:border-red-700">
                {{ __('No se pudo despachar la ejecución al core. Se guardó el intento con estado failed.') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100/60 dark:bg-zinc-900/60">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('ID') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Recipe') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Target') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Usuario') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Estado') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Resumen') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">{{ __('Creada') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($executions as $execution)
                        <tr wire:key="execution-{{ $execution->id }}" class="bg-white dark:bg-zinc-800/50">
                            <td class="px-4 py-3 text-sm font-medium">#{{ $execution->id }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $execution->recipe?->name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $execution->target?->name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $execution->requestedBy?->name ?? __('Sistema') }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $execution->status }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ \Illuminate\Support\Str::limit((string) $execution->error_summary, 90) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $execution->created_at?->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-zinc-500">
                                {{ __('Aún no hay ejecuciones.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $executions->links() }}
        </div>
    </div>
</section>

