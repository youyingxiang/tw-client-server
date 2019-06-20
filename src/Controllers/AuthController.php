<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 7:21 PM
 */
namespace Tw\Server\Controllers;
use Tw\Server\Facades\Tw;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tw\Server\Requests\AdminRequest;
class AuthController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
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

    /**
     * @param Request $request
     */
    public function getRegister()
    {
        if (Tw::authLogic()->guard()->check()) {
            return redirect(Tw::authLogic()->redirectPath());
        }
        return view(Tw::authLogic()->showRegister());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function postRegister(Request $request)
    {
        return Tw::authLogic()->register($request);
    }

    /**
     * @param Request $request
     * @see 找回密码
     */
    public function getRestPassWord(Request $request)
    {
        if (Tw::authLogic()->guard()->check()) {
            return redirect(Tw::authLogic()->redirectPath());
        }
        return view("tw::login.resetpassword");
    }

    /**
     * @param Request $request
     */
    public function postRestPassWord(Request $request)
    {
        return Tw::authLogic()->reset($request);
    }
    /**
     * @see 发送短信接口
     */
    public function sendMsg(AdminRequest $request)
    {
        $to = $request->post('rphone');
        $bRes = Tw::authLogic()->phoneVerif($to);
        if (!$bRes)
            return Tw::ajaxResponse("手机号码格式不正确！");
        try {
            sendMsg($to);
            return Tw::ajaxResponse("send ok",'1');
        } catch (\Exception $e) {
            return Tw::ajaxResponse($e->getMessage());
        }

    }

    /**
     * @param AdminRequest $request
     * @see 重制密码短信
     */
    public function resetSendMsg( )
    {
        $to = request()->post('resetphone');
        $bRes = Tw::authLogic()->phoneVerif($to);
        $iConut = $bRes ? Tw::authLogic()->isExistsPhone($to) : 0;
        if (!$iConut)
            return Tw::ajaxResponse("未找到当前手机号的信息！请确认手机号是否正确");

        try {
            sendMsg($to);
            return Tw::ajaxResponse("send ok",'1');
        } catch (\Exception $e) {
            return Tw::ajaxResponse($e->getMessage());
        }
    }




}