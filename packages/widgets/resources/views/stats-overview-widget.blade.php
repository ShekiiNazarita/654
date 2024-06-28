@php
    $columns = $this->getColumns();
    $sm = $columns['sm'] ?? 1;
    $md = $columns['md'] ?? 2;
    $lg = $columns['lg'] ?? 3;
    $xl = $columns['xl'] ?? 4;
    $twoxl = $columns['2xl'] ?? 5;
@endphp

<x-filament-widgets::widget class="fi-wi-stats-overview">
    <div
        @if ($pollingInterval = $this->getPollingInterval())
             wire:poll.{{ $pollingInterval }}
        @endif

        @class([
            'fi-wi-stats-overview-stats-ctn grid gap-6 grid-cols-3',
            'sm:grid-cols-' . $sm,
            'md:grid-cols-' . $md,
            'lg:grid-cols-' . $lg,
            'xl:grid-cols-' . $xl,
            '2xl:grid-cols-' . $twoxl,
        ])
    >
        @foreach ($this->getCachedStats() as $stat)
            {{ $stat }}
        @endforeach
    </div>
</x-filament-widgets::widget>
