---
title: Appearance
---

## Changing the brand logo

By default, Filament will use your app's name as a brand logo in the admin panel.

You may create a `resources/views/vendor/filament/components/brand.blade.php` file to provide a custom logo:

```blade
<img src="{{ asset('/images/logo.svg') }}" alt="Logo" class="h-10">
```

## Building themes

Filament allows you to change the fonts and color scheme used in the UI, by compiling a custom stylesheet to replace the default one. This custom stylesheet is called a "theme".

Themes use [Tailwind CSS](https://tailwindcss.com), the Tailwind Forms plugin, and the Tailwind Typography plugin. You may install these through NPM:

```bash
npm install tailwindcss @tailwindcss/forms @tailwindcss/typography --save-dev
```

To finish installing Tailwind, you must create a new `tailwind.config.js` file in the root of your project. The easiest way to do this is by running `npx tailwindcss init`.

In `tailwind.config.js`, register the plugins you installed, and add custom colors used by the form builder:

```js
const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php', // [tl! focus]
    ],
    theme: {
        extend: {
            colors: { // [tl! focus:start]
                danger: colors.rose,
                primary: colors.blue,
                success: colors.green,
                warning: colors.yellow,
            }, // [tl! focus:end]
        },
    },
    plugins: [
        require('@tailwindcss/forms'), // [tl! focus:start]
        require('@tailwindcss/typography'), // [tl! focus:end]
    ],
}
```

You may specify your own colors, which will be used throughout the admin panel.

In your `webpack.mix.js` file, Register Tailwind CSS as a PostCSS plugin :

```js
const mix = require('laravel-mix')

mix.postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'), // [tl! focus]
])
```

In `/resources/css/app.css`, import `filament/forms` vendor CSS and [TailwindCSS](https://tailwindcss.com):

```css
@import '../../vendor/filament/forms/dist/module.esm.css';

@tailwind base;
@tailwind components;
@tailwind utilities;
```

Now, you may register the theme file in a service provider's `boot()` method:

```php
use Filament\Facades\Filament;

Filament::serving(function (): void {
    Filament::registerTheme(mix('css/app.css'));
});
```

## Changing the maximum content width

Filament exposes a configuration option that allows you to change the maximum content width of all pages.

You must [publish the configuration](installation#publishing-the-configuration) in order to access this feature.

In `config/filament.php`, set the `layouts.max_content_width` to any value between `xl` and `7xl`, or `full` for no max width:

```php
'layout' => [
    'max_content_width' => 'full',
],
```

The default is `7xl`.

## Including frontend assets

You may register your own scripts and styles using the `registerScripts()` and `registerStyles()` methods in a service provider's `boot()` method:

```php
use Filament\Facades\Filament;

Filament::registerScripts([
    asset('js/my-script.js'),
]);

Filament::registerStyles([
    'https://unpkg.com/tippy.js@6/dist/tippy.css',
    asset('css/my-styles.css'),
]);
```

You may pass `true` as a parameter to `registerScripts()` to load it before Filament's core JavaScript. This is useful for registering Alpine.js plugins from a CDN:

```php
Filament::registerScripts([
    'https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-tooltip@0.x.x/dist/cdn.min.js',
], true);
```
