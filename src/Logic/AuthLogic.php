<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/30
 * Time: 10:02 AM
 */
namespace Tw\Server\Logic;
use Illuminate\Http\Request;
use Tw\Server\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthLogic
{
    use AuthenticatesUsers;
    /**
     * @var string
     */
    public $loginView  = 'tw::login.login';
    /**
     * @var string
     */
    public $redirectTo = "activity";
    /**
     * @var string
     */
    public $registerView ='tw::login.register';

    /**
     * @return string
     */
    public function showLoginForm():string
    {
        return $this->loginView;
    }

    /**
     * @return string
     */
    public function showRegister():string
    {
        return $this->registerView;
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

    /**
     * @param Request $request
     */
    public function logout(Request $request):void
    {
        $this->guard()->logout();

        $request->session()->invalidate();

    }

    /**
     * @param Request $request
     */
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
     * @see 验证注册
     */
    public function validateRegister(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|max:128',
                'password' => 'required|string|min:6',
                'phone' => 'required|max:20|unique:tw_admin',
                'code'  => 'required',
            ],
            [
                'name.required' => '用户昵称不能为空！',
                'name.max'  => "用户昵称长度不能超过128个字符！",
                'phone.required' => '手机号码不能为空！',
                'phone.unique' => '手机号码已经存在！',
                'phone.max'     => '手机号码长度不能超过20个字符！',
                "password.required"  => "密码不能为空",
                "password.min"       => "密码需要最少包含6个字符！",
                'code.required'      => "请输入验证码！",
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
     * @param Request $request
     */
    public function sendCodeErrResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'code' => ["手机验证码输入不正确！"],
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(Request $request)
    {
        $this->validateRegister($request);
        $code = $request->post('code');
        $phone = $request->post('phone');
        if (false == comparisonCode( $code,$phone)) {
            return $this->sendCodeErrResponse($request);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    /**
     * @param Request $request
     * @param $user
     */
    protected function registered(Request $request, $user)
    {
        //
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function create(array $data)
    {
        return Admin::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);
    }






}