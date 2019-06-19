<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/31
 * Time: 3:47 PM
 */
namespace Tw\Server\Logic;
use Closure;
use EasyWeChat;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Contracts\Support\Renderable;
class ModelLogic implements Renderable
{
    /**
     * @var
     */
    protected $model;
    /**
     * @var
     */
    protected $oModelResult;
    /**
     * @var
     */
    protected $aDataScore;

    /**
     * ModelLogic constructor.
     * @param $model
     * @param Closure|null $callback
     */
    public function __construct($model, Closure $callback = null)
    {
        $this->model = $model;
        if ($callback instanceof Closure) {
            $callback($this);
        }
    }
    /**
     * @return object
     */
    public function getModel():object
    {
        return $this->model;
    }

    /**
     * @param string $activity_id
     * @return bool
     * @param $flag 1增加判断  2 修改判断
     * @人数限制
     */
    public function checkRestrict(string $activity_id,int $flag):bool
    {
        $bFlag = true;
        if (method_exists($this->model,'restrict')) {
            $bFlag = call_user_func_array([$this->model, 'restrict'],[$activity_id,$flag]);
        }
        return $bFlag;
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update(string $id, array $aData = [],array $aWhereData = [])
    {
        if (!empty($aData['activity_id']) && $this->checkRestrict($aData['activity_id'],2) == false) {
            return Tw::ajaxResponse("添加超过限制！请升级为高级活动");
        }

        $bSaveRes = false;
        $where   = $this->getWhere($aWhereData);
        $this->oModelResult = $this->model->where($where)->findOrFail($id);
        DB::transaction(function () use ($aData,&$bSaveRes,$id) {
            if (method_exists($this->model,'beforeUpdate')) {
                call_user_func([$this->model,'beforeUpdate'],$id);
            }
            $aUpdates = $this->prepare($aData);
            foreach ($aUpdates as $column => $value) {
                /* @var Model $this->model */
                $this->oModelResult->setAttribute($column, is_null($value)?'':$value);
            }
            $bSaveRes = $this->oModelResult->save();

            if (method_exists($this->model,'afterUpdate')) {
                call_user_func([$this->model,'afterUpdate'],$this->oModelResult);
            }
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->oModelResult->getIndexUrl());
        else
            return Tw::ajaxResponse("操作失败");

    }

    /**
     * @param array $aData
     */
    public function store(array $aData = [])
    {
        if (!empty($aData['activity_id']) && $this->checkRestrict($aData['activity_id'],1) == false) {
            return Tw::ajaxResponse("添加超过限制！请升级为高级活动");
        }
        $bSaveRes = false;
        DB::transaction(function ()use($aData,&$bSaveRes) {
            foreach ($aData as $column => $value) {
                /* @var Model $this->model */
                $this->model->setAttribute($column, is_null($value)?'':$value);
            }
            $bSaveRes =  $this->model->save();
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->model->getIndexUrl());
        else
            return Tw::ajaxResponse("操作失败");
    }

    /**
     * @param array $whereAdata
     */
    public function query(array $aWhereData = []):object
    {
        $where   = $this->getWhere($aWhereData);
        $orWhere = $this->getOrWhere($aWhereData);
        if (!empty($aWhereData['_sort'])) {
            $aOrder =  explode(',', $aWhereData['_sort']);
        } else {
            $aOrder = ['id','desc'];
        }
//        DB::enableQueryLog();
        $aData = $this->model->where($where)->where($orWhere)->orderBy($aOrder[0],$aOrder[1])->paginate($this->getPage());
//        dd((DB::getQueryLog()));
        return $aData;
    }

    /**
     * @return int
     */
    public function getPage():int
    {
        if (property_exists($this->model,'query_page')) {
            $page = $this->model->query_page;
        } else {
            $page = config("tw.page.default");
        }
        return $page;
    }


    /**
     * @param array $aWhereData
     */
    public function find(int $id,array $aWhereData = [])
    {
        $where   = $this->getWhere($aWhereData);
        $orWhere = $this->getOrWhere($aWhereData);
        $oData = $this->model->where($where)->where($orWhere)->find($id);
        return $oData;
    }

    /**
     * @param string $id
     * @param $aWhereData
     */
    public function destroy(string $id,$aWhereData = [])
    {
        $bSaveRes = false;
        if (!empty($id)) {
            $ids = explode(',',$id);
            $ids = array_map(function ($id){return hash_decode($id)??$id;},$ids);
        }
        $where = $this->getWhere($aWhereData);
        DB::transaction(function ()use($where,$ids,&$bSaveRes) {
            if (method_exists($this->model,'beforeDelete')) {
                call_user_func([$this->model,'beforeDelete'],$ids);
            }
            $bSaveRes = $this->model->where($where)->whereIn('id',$ids)->delete();
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->model->getIndexUrl());
        else
            return Tw::ajaxResponse("操作失败");
    }

    /**
     * @param $search
     * @return Closure
     */
    public function getWhere(array $aWhere):Closure
    {

        $where = function ($query) use ($aWhere) {
            /**
             * 当前项目归属谁
             */
            if (method_exists($this->model,'parentFlag') && !empty($this->model->parentFlag())) {
                foreach ($this->model->parentFlag() as $pk=> $pv) {
                    $query->where($pk,$pv);
                }
            }
            // and where
            if (method_exists($this->model,'getAndFieds') && $aWhere &&
                $this->model->getAndFieds()
            ) {
                foreach ($aWhere as $field => $value) {
                    if (in_array($field,$this->model->getAndFieds()) && isset($value)) {
                        $query->where($field,$value);
                    }

                }
            }
        };
        return $where;
    }


    /**
     * @param $aWhere
     * @return Closure
     */
    public function getOrWhere($aWhere) :Closure
    {
        $where = function ($query) use ($aWhere) {
            // or where
            if (method_exists($this->model,'getOrFields') && !empty($aWhere['search']) &&
                $this->model->getOrFields()
            ) {
                foreach ($this->model->getOrFields() as $field) {
                    $query->orWhere($field, "like", "%" . $aWhere['search'] . "%");
                }
            }
        };
        return $where;
    }

    /**
     * @param array $aInput
     * @return mixed
     * @see 生成订单
     */
    public function generateOrder(array $aInput)
    {
        if(
            !empty($aInput['type'])
            && in_array($aInput['type'],[1,2])
            && !empty($aInput['pay_type'])
            && !empty($aInput['activity_id'])
        ) {
            if ($aInput['type'] == 1 && isset($aInput['level']) && $aInput['level'] == 1) {
                $aData['type'] = 1;
                $aData['order_info'] = "开通高级活动";
                $aData['pay_amount'] = config('tw.pay_amount_base.senior');
            } else if ($aInput['type'] == 1 && isset($aInput['level']) && $aInput['level'] == 2) {
                return Tw::ajaxResponse("当前活动已经是高级活动了！");
            } else if ($aInput['type'] == 2 && !empty($aInput['days'])) {
                $aData['type'] = 2;
                $aData['days'] = $aInput['days'];
                $aData['order_info'] = "购买天数".$aInput['days']."天";
                $aData['pay_amount'] = config('tw.pay_amount_base.oneday')*$aInput['days'];
            }
            if ($aData['order_info']) {
                $aData['order_no'] = get_order_no();
                $aData['pay_type'] = $aInput['pay_type'];
                $aData['admin_id'] = Tw::authLogic()->guard()->id();
                $aData['activity_id'] = $aInput['activity_id'];
                return $this->store($aData);
            } else {
                return Tw::ajaxResponse("操作失败！");
            }
        } else {
            return Tw::ajaxResponse("操作失败！");
        }
    }

    /**
     * @param array $aInput
     * @return string
     * @根据订单生成二维码
     */
    public function generateQrCode(array $aInput):string
    {
        $sData = "";
        $aData     = $this->query($aInput);
        $orderInfo = $aData['0']??'';

        if ($orderInfo) {
            $sCodeUrl = $this->wechatPay($orderInfo);
            if ($sCodeUrl)
                $sData = $this->model->getQrCodeByUrl($sCodeUrl);
        }
        return $sData;
    }

    /**
     * @param object $orderInfo
     * @return string
     * @获取微信扫码支付二维码地址
     */
    public function wechatPay(object $orderInfo):string
    {
        $sData = "";

        $app  = EasyWeChat::payment();
        $result = $app->order->unify([
            'body'         => $orderInfo->order_info,
            'out_trade_no' => $orderInfo->order_no,
            'total_fee'    => ($orderInfo->pay_amount)*100,
            'notify_url'   => route("tw.payorder.notify"), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'NATIVE',
            'product_id'   => $orderInfo->activity_id
        ]);
        if (!empty($result['code_url'])) {
            $sData = $result['code_url'];
        }
        return $sData;
    }




    /**
     * @param array $aData
     * @return array
     */
    protected function prepare($aData = [])
    {
        $this->relations = $this->getRelationInputs($aData);
        $aData = Arr::except($aData,['id']);
        return Arr::except($aData, array_keys($this->relations));
    }

    /**
     * @param array $inputs
     * @return array
     */
    protected function getRelationInputs($aData = []):array
    {
        $relations = [];

        foreach ($aData as $column => $value) {
            if (!method_exists($this->oModelResult, $column)) {
                continue;
            }

            $relation = call_user_func([$this->oModelResult, $column]);

            if ($relation instanceof Relations\Relation) {
                $relations[$column] = $value;
            }
        }

        return $relations;
    }

    /**
     * @param array $aData
     * @see 为防止redis key过多
     * 在没有得分时候 score:player:选手id  field=评委id   value=评委打出的分数
     * 获取得分以后   score:activity:活动id  field=选手id  value = json(key="评委id",value="评委打出的分数"）
     */
    public function storeScore($aData = []):void
    {
        $this->aDataScore = $aData;
        $this->playerKey = config('tw.redis_key.h1').$this->aDataScore['player_id'];
        $field     = $this->aDataScore['judges_id'];
        $score     = $this->aDataScore['score'];
        // 判断是否已经进行进行过评分
        if (Redis::hexists($this->playerKey,$field))
            throw new \Exception("您已经对当前选手进行过评分！无需再次评分");
        if ($score < 0)
            throw new \Exception("请输入正确的分数！");
        Redis::hset($this->playerKey,$field,$score);
        // 获取有多少评委
        $iSumJudges = $this->model->where('activity_id',$this->aDataScore['activity_id'])->count();
        // 当前所有评委都已经打分完毕
        if ($iSumJudges > 0 && $iSumJudges == Redis::hlen($this->playerKey)) {
            $this->scoreType();
//            $activityKey = "score:activity:".$this->aDataScore['activity_id'];
//            $aValue = Redis::hgetall($this->playerKey);
//            Redis::hset($activityKey,$this->aDataScore['player_id'],json_encode($aValue,true));
//            Redis::del($this->playerKey);
        }
    }

    /**
     * @see 设置分数
     */
    public function scoreType():void
    {
        $oData = Tw::newModel("Activity")->find($this->aDataScore['activity_id']);
        $scoreType = $oData['score_type'];
        // 1 取平均 2 去掉最大最小
        if ($scoreType == 1) {
            $score = $this->avgScore();
        } elseif ($scoreType == 2) {
            $score = $this->removeMinAndMaxScore();
        }
        Tw::newModel("Player")->where('id',$this->aDataScore['player_id'])->update(['score'=>$score]);
    }

    /**
     * @param array $aData
     * @see 平均算法
     */
    public function avgScore():float
    {
        $aScores = Redis::hvals($this->playerKey);
        return round(array_sum($aScores)/count($aScores),2);
    }
    /**
     * @param array $aData
     * @see 去掉最大值最小值算法
     */
    public function removeMinAndMaxScore():float
    {
        $aScores = Redis::hvals($this->playerKey);
        sort($aScores);
        array_pop($aScores);
        array_shift($aScores);
        return round(array_sum($aScores)/count($aScores),2);
    }

    /**
     * @param string $order_no
     * @see 微信回调订单逻辑
     */
    public function wecahtNotifyOrderLogic(array $message):bool
    {
        $oOrder = $this->model->isExistsOrderNo($message['out_trade_no']);
        if (!$oOrder || $oOrder['pay_state'] != 0) {
            $bRes = true;
        } else {
            // 用户是否支付成功
            if (array_get($message, 'result_code') === 'SUCCESS') {
                $oOrder->pay_state = 1;//支付成功
            } elseif (array_get($message, 'result_code') === 'FAIL') {
                $oOrder->pay_state = 2;       //支付失败
            }
            $bRes = $this->model->changeOrderState($oOrder);
        }
        return $bRes;
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (method_exists($this->model,$name)) {
            $jResult = call_user_func([$this->model, $name],$arguments);
            return $jResult;
        } else {
            abort(404);
        }

    }

    /**
     * @return string|void
     */
    public function render()
    {
        dd("this is render func");
    }



}
