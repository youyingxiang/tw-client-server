<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 3:01 PM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
use Tw\Server\Facades\Tw;
class TwServerController extends Controller
{
    public function index()
    {
        $user = Tw::authLogic()->guard()->user();
        $oOrder = $this->Model()->query();
        return view('tw::index.index',compact('user'),compact('oOrder'));
    }

    public function Model()
    {
        return Tw::moldelLogic(Tw::newModel("PayOrder"));
    }
}