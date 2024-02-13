<?php

namespace Netto;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Netto\Events\OrderStatusChanged;
use App\Listeners\OrderStatusChangeLogic;
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
            $this->publishes([
                __DIR__.'/../stub/app/Http/Requests' => app_path('Http/Requests'),
                __DIR__.'/../stub/app/Models' => app_path('Models'),
                __DIR__.'/../stub/app/Listeners' => app_path('Listeners'),
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-store');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'cms-store');

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('cart', Cart::class);
        $router->pushMiddlewareToGroup('public', 'cart');

        Event::listen(OrderStatusChanged::class, OrderStatusChangeLogic::class);
    }
}
