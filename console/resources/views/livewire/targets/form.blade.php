<section class="w-full px-4 py-6 lg:px-8">
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ $this->pageTitle }}</flux:heading>
                <flux:text class="mt-1">{{ __('Define el acceso operativo para este target.') }}</flux:text>
            </div>

            <flux:button variant="ghost" :href="route('targets.index')" wire:navigate>
                {{ __('Volver') }}
            </flux:button>
        </div>

        <form wire:submit="save" class="space-y-6 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="name" :label="__('Nombre')" type="text" required />
                <flux:input wire:model="host" :label="__('Host')" type="text" required />
                <flux:input wire:model="port" :label="__('Puerto')" type="number" min="1" max="65535" required />
                <flux:input wire:model="user" :label="__('Usuario')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.live="auth_method" :label="__('Método de autenticación')">
                    <option value="ssh_key">ssh_key</option>
                    <option value="password">password</option>
                </flux:select>

                <flux:checkbox wire:model="is_active" :label="__('Activo')" />
            </div>

            @if ($auth_method === 'ssh_key')
                <flux:input wire:model="key_path" :label="__('Ruta de llave')" type="text" required />
            @endif

            @if ($auth_method === 'password')
                <flux:input wire:model="secret_ref" :label="__('Referencia de secreto')" type="text" required />
            @endif

            <flux:textarea
                wire:model="tagsJson"
                :label="__('Tags (JSON)')"
                rows="6"
                :placeholder="__('Ejemplo: {\"env\":\"prod\",\"role\":\"app\"}')"
            />

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit">
                    {{ __('Guardar') }}
                </flux:button>

                <flux:button variant="ghost" :href="route('targets.index')" wire:navigate>
                    {{ __('Cancelar') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>
