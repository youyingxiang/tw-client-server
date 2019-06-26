<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/2
 * Time: 4:22 PM
 */
namespace Tw\Server\Requests;
use Illuminate\Support\Arr;
use Tw\Server\Traits\TwRequest;
use Tw\Server\Requests\TwRequest as TwRequestI;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
class AdminRequest extends FormRequest implements TwRequestI
{
    use TwRequest;

    /**
     * @var array
     * @see 修改验证字段
     */
    protected $alone_validate  = [
        'tw.userinfo.update'=>['name','email','qq','wechat','phone'],
        'tw.sendmsg' => ['rphone']
    ];


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        $rules = $this->getRules();
        $aRoute = request()->route()->getAction();
        $sAs    = $aRoute['as']??'';
        $exceptKeys = [];
        if (!empty($sAs) && array_key_exists($sAs,$this->alone_validate)) {
            foreach ($rules as $key => $value) {
                if (!in_array($key,$this->alone_validate[$sAs])) {
                    $exceptKeys[] = $key;
                }
            }
        }
        if (!empty($exceptKeys))
            return Arr::except($rules,$exceptKeys);
        else
            return $rules;
    }
    /**
     * 设置验证属性
     */
    public function setRules(): void
    {
        $this->rules = [
            'name' => 'required|max:128',
            'phone' => 'required|max:20|unique:tw_admin,phone,'.$this->getInputId(),
            'rphone' => 'required|max:20|unique:tw_admin,phone',  // 前端发送验证码
            'password' => 'required|max:60|min:6',
            'repassword' => 'same:password',
            'email' => 'nullable|email',
            'img' => 'max:300',
            'qq' => 'max:60',
            'wechat' => 'max:60'
        ];
    }
    /**
     * @return int
     */
    public function getInputId():int
    {
        return (int)$this->request->get('id');
    }
    /**
     * 获取已定义的验证规则的错误消息。
     *
     * @return array
     */
    public function messages() :array
    {
        return [
            'name.required' => '用户昵称不能为空！',
            'name.max'  => "用户昵称长度不能超过128个字符！",
            'phone.required' => '手机号码不能为空！',
            'phone.unique' => '手机号码已经存在！',
            'phone.max'     => '手机号码长度不能超过20个字符！',
            'password.required' => '密码不能为空！',
            'password.max' => '密码长度不能超过60个字符！',
            'password.min' => '密码长度最小6位',
            'repassword.same' => '两次密码输入不一致！',
            'email.email'   => '请输入正确的邮箱地址！',
            'rphone.required' => '手机号码不能为空！',
            'rphone.unique' => '手机号码已经存在！',
            'rphone.max'     => '手机号码长度不能超过20个字符！',
        ];
    }
    /**
     * @param 验证后钩子
     */
    public function withValidator($validator):void
    {
        $validator->after(function ($validator) {
            $this->request->remove('_token');
            $this->request->remove('repassword');
            if (!empty($this->request->get('password'))){
                $this->request->set('password',Hash::make($this->request->get('password')));
            }
        });
    }
}