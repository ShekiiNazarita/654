<x-filament-actions::group
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    :group="$group"
    dynamic-component="filament::button"
    :outlined="$isOutlined()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="fi-ac-btn-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
