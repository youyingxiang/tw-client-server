<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/2
 * Time: 4:57 PM
 */
namespace Tw\Server\Traits;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
trait TwRequest
{
    protected  $rules;
    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();
        throw new HttpResponseException (Tw::ajaxResponse($error));
    }

    /**
     * 对传入参数个数不同的验证
     */
    public function ruleHook():void
    {
        if (!$this->rules) {
            $this->setRules();
        }
        $aInput  = $this->request->all();

        if (count ($aInput) == 4) {
            $aKey = array_keys(Arr::except($aInput,['_token','id','_method']));
            foreach ($aKey as $v) {
                $value[$v] = Arr::get($this->rules,$v);
            }
            $this->rules = $value;
        }
    }

    /**
     * @return array
     */
    public function getRules():array
    {
        $this->ruleHook();
        return $this->rules;
    }
}
