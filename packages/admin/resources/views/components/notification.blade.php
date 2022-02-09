@props([
    'message',
    'status',
])

<div
    x-data="{ isVisible: false }"
    x-init="() => {
        $nextTick(() => isVisible = true)
        setTimeout(() => isVisible = false, 5000)
        setTimeout(() => $el.remove(), 6000)
    }"
    x-cloak
    class="filament-notification"
>
    <div class="flex flex-col h-auto sm:max-w-xs max-w-screen mx-auto space-y-2 pointer-events-auto">
        <div
            x-show="isVisible"
            x-transition
            @class([
                'flex items-start px-3 py-2 space-x-2 rtl:space-x-reverse text-xs shadow ring-1 rounded-xl',
                match ($status) {
                    'danger' => 'bg-danger-50 ring-danger-200',
                    'success' => 'bg-success-50 ring-success-200',
                    'warning' => 'bg-warning-50 ring-warning-200',
                    default => \Illuminate\Support\Arr::toCssClasses([
                        'bg-white ring-gray-200',
                        'dark:bg-gray-700 dark:ring-gray-600' => config('filament.dark_mode'),
                    ]),
                },
            ])
        >
            <x-dynamic-component :component="match ($status) {
                'danger' => 'heroicon-o-x-circle',
                'success' => 'heroicon-o-check-circle',
                'warning' => 'heroicon-o-exclamation',
                default => 'heroicon-o-information-circle',
            }" :class="\Illuminate\Support\Arr::toCssClasses([
                'shrink-0 w-6 h-6',
                match ($status) {
                    'danger' => 'text-danger-600',
                    'success' => 'text-success-600',
                    'warning' => 'text-warning-600',
                    default => 'text-primary-600',
                },
            ])" />

            <div class="flex-1 space-y-1">
                <div class="flex items-center justify-between font-medium">
                    <p
                        @class([
                            'text-sm leading-6',
                            match ($status) {
                                'danger' => 'text-danger-900',
                                'success' => 'text-success-900',
                                'warning' => 'text-warning-900',
                                default => \Illuminate\Support\Arr::toCssClasses([
                                    'text-gray-900',
                                    'dark:text-gray-200' => config('filament.dark_mode'),
                                ]),
                            },
                        ])
                    >
                        {{ $message }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
