<x-filament-tables::cell
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
>
    <div class="fi-ta-cell-content whitespace-nowrap px-3 py-4">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
