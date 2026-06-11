@if ($canManage)
    <button
        type="button"
        wire:click="toggle"
        wire:loading.attr="disabled"
        @class([
            'inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium transition',
            'bg-danger-600 text-white hover:bg-danger-500' => $enabled,
            'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20' => ! $enabled,
        ])
    >
        <span
            @class([
                'h-2 w-2 rounded-full',
                'bg-white' => $enabled,
                'bg-gray-500 dark:bg-gray-300' => ! $enabled,
            ])
        ></span>
        <span>{{ $enabled ? 'Mantenimiento' : 'Operativo' }}</span>
    </button>
@endif
