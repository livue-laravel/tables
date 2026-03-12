<?php

namespace Primix\Tables;

use Illuminate\Support\ServiceProvider;
use LiVue\Facades\LiVueAsset;
use LiVue\Features\SupportAssets\Css;
use LiVue\Features\SupportAssets\Js;
use Primix\Support\AssetVersion;
use Primix\Support\ComponentTypeRegistry;

class PrimixTablesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'primix-tables');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'primix-tables');

        $this->registerAssets();
        $this->registerComponentTypes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/primix-tables'),
            ], 'primix-tables-views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => lang_path('vendor/primix-tables'),
            ], 'primix-tables-translations');

            $assets = [
                __DIR__ . '/../dist/primix-tables.css' => public_path('vendor/livue/primix/tables/primix-tables.css'),
                __DIR__ . '/../dist/primix-tables.js' => public_path('vendor/livue/primix/tables/primix-tables.js'),
                __DIR__ . '/../dist/primix-tables.js.map' => public_path('vendor/livue/primix/tables/primix-tables.js.map'),
            ];

            $this->publishes($assets, 'primix-assets');
            $this->publishes($assets, 'livue-assets');
            $this->publishes($assets, 'laravel-assets');

            $this->commands([
                Commands\MakeFilterCommand::class,
            ]);
        }
    }

    protected function registerComponentTypes(): void
    {
        $registry = $this->app->make(ComponentTypeRegistry::class);
        $registry->discoverInPath('Primix\\Tables\\Columns', __DIR__ . '/Columns');
        $registry->discoverInPath('Primix\\Tables\\Filters', __DIR__ . '/Filters');
    }

    protected function registerAssets(): void
    {
        $assetVersion = AssetVersion::resolve();
        $assetsBasePath = '/' . trim(config('livue.assets_path', 'vendor/livue'), '/');

        LiVueAsset::register([
            Css::make('primix-tables', "{$assetsBasePath}/primix/tables/primix-tables.css")->onRequest()->version($assetVersion),
            Js::make('primix-tables', "{$assetsBasePath}/primix/tables/primix-tables.js")->module()->onRequest()->version($assetVersion),
        ], 'primix/tables');
    }
}
