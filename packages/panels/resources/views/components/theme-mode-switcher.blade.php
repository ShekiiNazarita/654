<div
    x-data="{
        theme: null,

        init: function () {
            this.theme = localStorage.getItem('theme') || 'system'

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', (event) => {
                    if (this.theme !== 'system') {
                        return
                    }

                    if (
                        event.matches &&
                        ! document.documentElement.classList.contains('dark')
                    ) {
                        document.documentElement.classList.add('dark')
                    } else if (
                        ! event.matches &&
                        document.documentElement.classList.contains('dark')
                    ) {
                        document.documentElement.classList.remove('dark')
                    }
                })

            $watch('theme', () => {
                localStorage.setItem('theme', this.theme)

                if (
                    this.theme === 'dark' &&
                    ! document.documentElement.classList.contains('dark')
                ) {
                    document.documentElement.classList.add('dark')
                } else if (
                    this.theme === 'light' &&
                    document.documentElement.classList.contains('dark')
                ) {
                    document.documentElement.classList.remove('dark')
                } else if (this.theme === 'system') {
                    if (
                        this.isSystemDark() &&
                        ! document.documentElement.classList.contains('dark')
                    ) {
                        document.documentElement.classList.add('dark')
                    } else if (
                        ! this.isSystemDark() &&
                        document.documentElement.classList.contains('dark')
                    ) {
                        document.documentElement.classList.remove('dark')
                    }
                }

                $dispatch('theme-changed', this.theme)
            })
        },

        isSystemDark: function () {
            return window.matchMedia('(prefers-color-scheme: dark)').matches
        },
    }"
    class="filament-theme-switcher flex items-center divide-x divide-gray-950/5 border-b border-gray-950/5 dark:divide-gray-700 dark:border-gray-700"
>
    <button
        type="button"
        aria-label="{{ __('filament::layout.buttons.light_theme.label') }}"
        x-tooltip="'{{ __('filament::layout.buttons.light_theme.label') }}'"
        x-on:click="theme = 'light'"
        x-bind:class="theme === 'light' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
        class="flex flex-1 items-center justify-center p-2 hover:bg-gray-500/10 focus:bg-gray-500/10"
    >
        <x-filament::icon
            name="heroicon-m-sun"
            alias="panels::theme.light"
            size="h-5 w-5"
        />
    </button>

    <button
        type="button"
        aria-label="{{ __('filament::layout.buttons.dark_theme.label') }}"
        x-tooltip="'{{ __('filament::layout.buttons.dark_theme.label') }}'"
        x-on:click="theme = 'dark'"
        x-bind:class="theme === 'dark' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
        class="flex flex-1 items-center justify-center p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
    >
        <x-filament::icon
            name="heroicon-m-moon"
            alias="panels::theme.dark"
            size="h-5 w-5"
        />
    </button>

    <button
        type="button"
        aria-label="{{ __('filament::layout.buttons.system_theme.label') }}"
        x-tooltip="'{{ __('filament::layout.buttons.system_theme.label') }}'"
        x-on:click="theme = 'system'"
        x-bind:class="theme === 'system' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
        class="flex flex-1 items-center justify-center p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
    >
        <x-filament::icon
            name="heroicon-m-computer-desktop"
            alias="panels::theme.system"
            size="h-5 w-5"
        />
    </button>
</div>
