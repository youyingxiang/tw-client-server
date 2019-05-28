<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:34 PM
 */
namespace Tw\Server;
use Illuminate\Support\ServiceProvider;
class VoteServiceProvider extends ServiceProvider
{

    public function register()
    {
        Vote::register();
        $this->commands(Vote::$commands);
    }

    public function boot()
    {
        $viewInfo = Vote::getLoadViewPath();
        $this->loadViewsFrom($viewInfo['path'],$viewInfo['alias']);
        Vote::boot();
    }
}