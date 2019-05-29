<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 10:50 AM
 */
namespace Tw\Server\Facades;
use Illuminate\Support\Facades\Facade;

class Tw extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Tw\Server\Tw::class;
    }
}