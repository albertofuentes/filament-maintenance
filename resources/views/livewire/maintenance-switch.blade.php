<div>
@if ($canManage)
    <div class="fi-filament-maintenance-switch">
        <x-filament::badge
            tag="button"
            type="button"
            size="sm"
            :color="$enabled ? 'danger' : 'success'"
            :icon="$enabled ? 'heroicon-o-wrench-screwdriver' : 'heroicon-o-check-circle'"
            :tooltip="$enabled ? 'Disable maintenance' : 'Enable maintenance'"
            wire:click="toggle"
            wire:target="toggle"
            aria-pressed="{{ $enabled ? 'true' : 'false' }}"
        >
            {{ $enabled ? 'Maintenance' : 'Operational' }}
        </x-filament::badge>
    </div>
@endif
</div>