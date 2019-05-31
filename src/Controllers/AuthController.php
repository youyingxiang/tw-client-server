<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 7:21 PM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Tw\Server\Facades\Tw;
class AuthController extends Controller
{

    public function getLogin()
    {
        if (Tw::authLogic()->guard()->check()) {
            return redirect(Tw::authLogic()->redirectPath());
        }
        return view(Tw::authLogic()->showLoginForm());
    }

    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        return Tw::authLogic()->login($request);
    }


    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
       Tw::authLogic()->logout($request);
       return redirect(Tw::authLogic()->redirectPath());
    }



}