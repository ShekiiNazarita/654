<?php

namespace Filament\Facades;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StatefulGuard auth()
 * @method static array getBeforeCoreScripts()
 * @method static array getPages()
 * @method static array getNavigation()
 * @method static array getNavigationGroups()
 * @method static array getNavigationItems()
 * @method static array getResources()
 * @method static array getScripts()
 * @method static array getScriptData()
 * @method static array getStyles()
 * @method static string getThemeUrl()
 * @method static string | null getUrl()
 * @method static string | null getUserAvatarUrl(Authenticatable $user)
 * @method static string getUserName(Authenticatable $user)
 * @method static array getWidgets()
 * @method static void navigation(\Closure $builder)
 * @method static void registerNavigationGroups(array $groups)
 * @method static void registerNavigationItems(array $items)
 * @method static void registerPages(array $pages)
 * @method static void registerResources(array $resources)
 * @method static void registerScripts(array $scripts)
 * @method static void registerScriptData(array $data)
 * @method static void registerStyles(array $styles)
 * @method static void registerTheme(string $url)
 * @method static void registerWidgets(array $widgets)
 * @method static void serving(Closure $callback)
 *
 * @see \Filament\FilamentManager
 */
class Filament extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament';
    }
}
