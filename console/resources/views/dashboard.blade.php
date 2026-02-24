<x-layouts::app :title="__('Dashboard')">
    @php
        $totalTargets = \App\Models\Target::count();
        $activeTargets = \App\Models\Target::where('is_active', true)->count();
        $totalRecipes = \App\Models\Recipe::count();
        $activeRecipes = \App\Models\Recipe::where('is_active', true)->count();
        $totalExecutions = \App\Models\Execution::count();
        $failedExecutions = \App\Models\Execution::where('status', 'failed')->count();
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="lg">{{ __('Targets') }}</flux:heading>
                <div class="mt-2 text-2xl font-semibold">{{ $activeTargets }} / {{ $totalTargets }}</div>
                <flux:text class="mt-2">{{ __('activos de total') }}</flux:text>
                <flux:button class="mt-4" size="sm" variant="primary" :href="route('targets.index')" wire:navigate>
                    {{ __('Ir a Targets') }}
                </flux:button>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="lg">{{ __('Recipes') }}</flux:heading>
                <div class="mt-2 text-2xl font-semibold">{{ $activeRecipes }} / {{ $totalRecipes }}</div>
                <flux:text class="mt-2">{{ __('activos de total') }}</flux:text>
                <flux:button class="mt-4" size="sm" variant="primary" :href="route('recipes.index')" wire:navigate>
                    {{ __('Ir a Recipes') }}
                </flux:button>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="lg">{{ __('Ejecuciones') }}</flux:heading>
                <div class="mt-2 text-2xl font-semibold">{{ $totalExecutions }}</div>
                <flux:text class="mt-2">
                    {{ __('Fallidas: :count', ['count' => $failedExecutions]) }}
                </flux:text>
                <flux:button class="mt-4" size="sm" variant="primary" :href="route('executions.index')" wire:navigate>
                    {{ __('Ver ejecuciones') }}
                </flux:button>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:heading size="lg">{{ __('Acciones rápidas') }}</flux:heading>
            <div class="mt-4 space-x-3">
                <flux:button variant="ghost" :href="route('executions.create')" wire:navigate>
                    {{ __('Lanzar ejecución') }}
                </flux:button>
                <flux:button variant="ghost" :href="route('targets.create')" wire:navigate>
                    {{ __('Nuevo target') }}
                </flux:button>
                <flux:button variant="ghost" :href="route('recipes.create')" wire:navigate>
                    {{ __('Nueva recipe') }}
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts::app>
