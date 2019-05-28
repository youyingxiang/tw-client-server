<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 3:01 PM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
class TwServerController extends Controller
{
    public function index()
    {
        return view('tw::index.index');
    }
}