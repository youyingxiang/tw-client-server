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

    protected $loginView = 'tw::login.login';

    public function getLogin()
    {
        if (\Tw::authLogin()->guard()->check()) {
            return redirect(\Tw::authLogin()->redirectPath());
        }
        return view($this->loginView);
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
        \Tw::authLogin()->loginLogic($request);
    }





    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
        \Tw::authLogin()->guard()->logout();

        $request->session()->invalidate();

        return redirect(config('tw.route.prefix'));
    }



}