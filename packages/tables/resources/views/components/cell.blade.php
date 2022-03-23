@props([
    'action' => null,
    'alignment' => null,
    'name',
    'record',
    'recordAction' => null,
    'recordUrl' => null,
    'shouldOpenUrlInNewTab' => false,
    'tooltip' => null,
    'url' => null,
])

<td
    {{ $attributes->class([
        'filament-tables-cell',
        'dark:text-white' => config('tables.dark_mode'),
        match ($alignment) {
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
            default => null,
        },
    ]) }}
>
    @if ($action || ((! $url) && $recordAction))
        <button
            wire:click="{{ $action ? "callTableColumnAction('{$name}', " : "{$recordAction}(" }}'{{ $record->getKey() }}')"
            wire:target="{{ $action ? "callTableColumnAction('{$name}', " : "{$recordAction}(" }}'{{ $record->getKey() }}')"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-70 cursor-wait"
            @if ($tooltip)
            x-data="tooltip(@js($tooltip))"
            x-tooltip="tooltip"
        @endif
            type="button"
            class="block text-left"
        >
            {{ $slot }}
        </button>
    @elseif ($url || $recordUrl)
        <a
            @if ($tooltip)
                 x-data="{ tooltip: {
                    content: @js($tooltip),
                    theme: $store.theme.isLight() ? 'dark' : 'light',
                 } }"
                x-tooltip="tooltip"
            @endif
            href="{{ $url ?: $recordUrl }}"
            {{ $shouldOpenUrlInNewTab ? 'target="_blank"' : null }}
            class="block"
        >
            {{ $slot }}
        </a>
    @else
        {{ $slot }}
    @endif
</td>
