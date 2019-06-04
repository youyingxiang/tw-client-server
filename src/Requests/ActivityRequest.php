<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/3
 * Time: 5:37 PM
 */
namespace Tw\Server\Requests;
use Tw\Server\Traits\TwRequest;
use Illuminate\Validation\Rule;
use Tw\Server\Requests\TwRequest as TwRequestI;
use Illuminate\Foundation\Http\FormRequest;
class ActivityRequest extends FormRequest implements TwRequestI
{
    use TwRequest;
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
        return $this->getRules();
    }

    /**
     * 设置验证属性
     */
    public function setRules(): void
    {
        $this->rules = [
            'title' => 'required|max:256',
            'logo' => 'max:300',
            'banner'=> 'max:300',
            'days'  => 'integer',
            'level' => Rule::in([1, 2]),
            'score_type' => Rule::in([1, 2]),
        ];
    }
    /**
     * 获取已定义的验证规则的错误消息。
     *
     * @return array
     */
    public function messages() :array
    {
        return [
            'title.required' => '活动名称不能为空！',
            'titile.max'  => "活动名称长度不能超过128个字符！",
        ];
    }
    /**
     * @param 验证后钩子
     */
    public function withValidator($validator):void
    {
        $validator->after(function ($validator) {
            $this->request->remove('_token');
            $this->request->remove('_method');
        });
    }
}