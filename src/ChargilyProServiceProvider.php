<?php

namespace Chargily\ChargilyProLaravel;

use Chargily\ChargilyProLaravel\Http\Middlewares\ValidateWebhookMiddleware;
use Chargily\ChargilyProLaravel\Services\ChargilyProTopUpService;
use Chargily\ChargilyProLaravel\Services\ChargilyProVoucherService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ChargilyProServiceProvider extends ServiceProvider
{
    /**
     * Register any services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/chargily-pro.php',
            'chargily-pro'
        );
    }
    /**
     * Bootstrap any services.
     *
     * @return void
     */
    public function boot()
    {
        /// ====================
        /// Alias Middlewares ==
        /// ====================
        $this->app->make(Router::class)
            ->aliasMiddleware('chargily-pay.validate-webhook', ValidateWebhookMiddleware::class);

        /// ==============
        /// Load routes ==
        /// ==============
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        /// ==================
        /// Load migrations ==
        /// ==================
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        /// ================
        /// Bind services ==
        /// ================
        $this->app->singleton('chargily-pro-voucher-service', function () {
            return new ChargilyProVoucherService();
        });
        $this->app->singleton('chargily-pro-topup-service', function () {
            return new ChargilyProTopUpService();
        });
        /// ==========
        /// Publish ==
        /// ==========
        $this->publishes([
            __DIR__ . '/config/chargily-pro.php' => config_path('chargily-pro.php'),
        ], "chargily-pro-config");
    }
}
