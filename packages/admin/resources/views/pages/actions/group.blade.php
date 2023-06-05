<x-filament-support::actions.group
    :actions="$getActions()"
    :button="$isButton()"
    :dark-mode="config('filament.dark_mode')"
    :color="$getColor()"
    :icon="$getIcon()"
    :label="$getLabel()"
    :size="$getSize()"
    :tooltip="$getTooltip()"
/>
