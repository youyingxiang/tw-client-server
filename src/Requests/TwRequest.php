<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/2
 * Time: 5:43 PM
 */
namespace Tw\Server\Requests;
interface TwRequest
{
    /**
     * 设置验证规则
     */
    public function setRules():void;

    /**
     * 获取验证规则
     * @return array
     */
    public function getRules():array;

}
