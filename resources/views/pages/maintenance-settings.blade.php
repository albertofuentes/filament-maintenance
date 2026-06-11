<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Estado del panel</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Activa el mantenimiento solo para este panel Filament.</p>
                </div>

                <label class="inline-flex cursor-pointer items-center gap-3">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Mantenimiento</span>
                    <input type="checkbox" wire:model="enabled" class="sr-only peer">
                    <span class="h-6 w-11 rounded-full bg-gray-200 after:mt-0.5 after:ml-0.5 after:block after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:bg-danger-600 peer-checked:after:translate-x-5 dark:bg-white/10"></span>
                </label>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Vista de mantenimiento</h2>

                <div class="mt-4 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Titulo</span>
                        <input type="text" wire:model="maintenanceTitle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5">
                        @error('maintenanceTitle') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Mensaje</span>
                        <textarea wire:model="message" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5"></textarea>
                        @error('message') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Vista Blade personalizada</span>
                        <input type="text" wire:model="customView" placeholder="filament-maintenance::maintenance.default" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5">
                        @error('customView') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Acceso durante mantenimiento</h2>

                <div class="mt-4 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">IPs o rangos CIDR permitidos</span>
                        <textarea wire:model="allowedIps" rows="5" placeholder="127.0.0.1&#10;192.168.1.0/24" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5"></textarea>
                        @error('allowedIps') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Roles Spatie que pueden entrar</span>
                        <textarea wire:model="allowedRoles" rows="3" placeholder="admin&#10;soporte" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5"></textarea>
                        @error('allowedRoles') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">Quien puede activar o desactivar</h2>

            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Usuarios gestores</span>
                    <select wire:model="managerUserIds" multiple class="mt-1 block min-h-36 w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5">
                        @foreach ($this->availableUsers as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('managerUserIds') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Roles Spatie gestores</span>
                    <textarea wire:model="managerRoles" rows="6" placeholder="admin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-white/10 dark:bg-white/5"></textarea>
                    @error('managerRoles') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Guardar configuracion
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
