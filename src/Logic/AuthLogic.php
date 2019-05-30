<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/30
 * Time: 10:02 AM
 */
namespace Tw\Server\Logic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Lang;
class AuthLogic
{
    use AuthenticatesUsers;
    /**
     * @var string
     */
    public $loginView  = 'tw::login.login';
    public $redirectTo = "tw-server";

    public function showLoginForm()
    {
        return $this->loginView;
    }

    /**
     * @return mixed
     */
    public function guard()
    {
        return Auth::guard('tw');
    }

    /**
     * @return string
     */
    public function username():string
    {
        return 'phone';
    }

    public function logout(Request $request):void
    {
        $this->guard()->logout();

        $request->session()->invalidate();

    }

    protected function validateLogin(Request $request)
    {
        $request->validate(
            [
                'phone' => 'required|string',
                'password' => 'required|string',
            ],
            [
                "phone.required" => "手机号不能为空！",
                "password.required"  => "密码不能为空",
            ]

        );
    }

    /**
     * @param Request $request
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => ["手机号与密码不匹配"],
        ]);
    }

    /**
     * @return string
     */
   public function redirectTo():string
   {
       return tw_base_path($this->redirectTo);
   }


    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );
        throw ValidationException::withMessages([
            $this->username() => ["登录尝试次数太多。请在: $seconds 秒后重试"],
        ])->status(429);
    }




}