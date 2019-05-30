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
class AuthController extends Controller
{

    public function getLogin()
    {
        if (\Tw::authLogin()->guard()->check()) {
            return redirect(\Tw::authLogin()->redirectPath());
        }
        return view(\Tw::authLogin()->showLoginForm());
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
        return \Tw::authLogin()->login($request);
    }


    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
       \Tw::authLogin()->logout($request);
       return redirect(\Tw::authLogin()->redirectPath());
    }



}