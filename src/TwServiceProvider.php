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

    public function register()
    {
        Tw::register();
        $this->commands(Tw::$commands);
    }

    public function boot()
    {
        $viewInfo = Tw::getLoadViewPath();
        $this->loadViewsFrom($viewInfo['path'],$viewInfo['alias']);

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/tw')], 'tw-server-assets');
        }
        Tw::boot();
    }
}