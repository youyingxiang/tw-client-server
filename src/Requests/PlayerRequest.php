<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/5
 * Time: 10:50 AM
 */
namespace Tw\Server\Requests;
use Tw\Server\Traits\TwRequest;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Tw\Server\Requests\TwRequest as TwRequestI;
class PlayerRequest extends FormRequest implements TwRequestI
{
    use TwRequest;

    /**
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
            'name' => 'required|max:128',
            'img' => 'max:300',
            'score' => 'numeric|max:100',
            'activity_id' => 'required|integer',
            'push_state' => Rule::in([0, 1]),
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
            'name.required' => '选手姓名不能为空！',
            'name.max'  => "选手姓名长度不能超过128个字符！",
            'score.numeric' => '得分必须是一个数字',
            'score.max' => '得分不能超过100！'
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