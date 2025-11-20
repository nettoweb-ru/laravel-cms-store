<?php

namespace Netto;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Netto\Console\Commands\DeleteExpiredCarts;
use Netto\Http\Middleware\Cart;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DeleteExpiredCarts::class,
            ]);
            $this->registerPublishedPaths();
        }

        $this->registerMiddleware();
        $this->registerScheduledTasks();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/../lang');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms');
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cms-store.php', 'cms-store');
    }

    /**
     * @return void
     * @throws BindingResolutionException
     */
    private function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('cart', Cart::class);
        $router->pushMiddlewareToGroup('public', 'cart');
    }

    /**
     * @return void
     */
    private function registerPublishedPaths(): void
    {
        $this->publishes([
            __DIR__.'/../config/cms-store.php' => config_path('cms-store.php'),
            __DIR__.'/../stub/app' => app_path(),
        ], 'nettoweb-laravel-cms-store');

        $this->publishes([
            __DIR__.'/../stub/public/assets/js' => public_path('assets/js'),
        ], 'laravel-assets');
    }

    /**
     * @return void
     */
    private function registerScheduledTasks(): void
    {
        Schedule::command(DeleteExpiredCarts::class)->dailyAt(config('cms.schedule.daily', 1));
    }
}
