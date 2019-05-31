<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:34 PM
 */
namespace Tw\Server;
use Illuminate\Support\ServiceProvider;
class TwServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $routeMiddleware = [
        'tw.auth' => Middleware\Authenticate::class
    ];
    /**
     * @var array
     */
    protected $middlewareGroups = [
        'tw' => [
            'tw.auth'
        ],
    ];

    public function register()
    {
        Tw::register();
        $this->commands(Tw::$commands);
        $this->registerRouteMiddleware();
    }

    public function boot()
    {
        $viewInfo = Tw::getLoadViewPath();
        $this->loadViewsFrom($viewInfo['path'],$viewInfo['alias']);

        if (config('tw.https') || config('tw.secure')) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'tw-server-config');
            $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/tw')], 'tw-server-assets');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'tw-migrations');
            $this->publishes([__DIR__.'/../database/seeds' => database_path('seeds')], 'seeds');
        }
        Tw::boot();
    }

    /**
     * 注册route中间件
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}