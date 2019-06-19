<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/13
 * Time: 5:09 PM
 */
namespace Tw\Server\Models;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\SoftDeletes;
class PayOrder extends Model
{

    /**
     * 软删除
     */
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['delete_at'];

    /**
     * @var array and 条件查询字段
     */
    protected $and_fields = ['order_no'];
    /**
     * @var int
     * @see 分页
     */
    public  $query_page = 5;

    /**
     * PayOrder constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('tw.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('tw_pay_order');

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('Tw\Server\Models\Admin');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity()
    {
        return $this->belongsTo('Tw\Server\Models\Activity');
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function getQrCodeByUrl(string $url)
    {
        return QrCode::size(400)->generate($url);
    }

    /**
     * @return string
     */
    public function getGoPayAttribute():string
    {
        $sData = '';
        if (!$this->pay_state) {
            $url = $this->getIndexUrl();
            $sData = "<a href=".$url." >去完成支付</a>";
        } else if ($this->pay_state == 1) {
            $sData = "<a href='javascript:void(0)' >已完成支付</a>";
        }
        return $sData;
    }

    /**
     * @param $value
     * @return string
     */
    public function getSPayStateAttribute():string
    {
        $sRes = '';
        if($this->pay_state == 0)
            $sRes = "未支付";
        else if ($this->pay_state == 1)
            $sRes = "已支付";
        else if ($this->pay_state == 2)
            $sRes = "支付失败";
        return $sRes;
    }

    public function getSPayTypeAttribute():string
    {
         $type = '';
         if ($this->pay_type == 1)
             $type = "微信支付";
         else if ($this->pay_type == 2)
             $type = "支付宝支付";
         return $type;
    }



    /**
     * 标示 当前活动属于哪个项目
     */
    public function parentFlag():array
    {
        return ['admin_id'=>Tw::authLogic()->guard()->id()];
    }

    /**
     * @return string
     */
    public function getIndexUrl(): string
    {
        $url = route("tw.payorder.qrcode",$this->order_no??'');
        return $url;
    }

    /**
     * @return array
     */
    public function getAndFieds():array
    {
        return $this->and_fields??[];
    }

    /**
     * @param string $order
     * @see 订单号是否存在
     */
    public function isExistsOrderNo(string $order_no):object
    {
        $awhere['order_no'] = $order_no;
        $order = $this->where($awhere)->first();
        return $order;
    }

    /**
     * @param object $order
     * 改变订单状态
     */
    public function changeOrderState(object $order):bool
    {
        // 乐观锁处理
        $awhere['order_no'] = $order['order_no'];
        $awhere['version']  = $order['version'];
        $aInput['pay_state'] = 1;
        $aInput['version'] = $order['version']+1;
        $bRes = DB::transaction(function () use ($order,$awhere,$aInput){
            $bRes = $this->where($awhere)->update($aInput);
            if ($bRes) {
                // 开通高级活动
                if ($order['type'] == 1) {
                    $this->OrderStateLevel2($order);
                } else if ($order['type'] == 2) {  //续费天数
                    $this->OrderStateLevel1($order);
                }
            }
            return $bRes;
        });
        if ($bRes)
            $this->pushUserOrderStatus($order['admin_id'],$order['order_no']);
        return (bool)$bRes;
    }

    /**
     * 推送订单状态
     */
    public function pushUserOrderStatus($adminId,$orderNo)
    {
        $url = $this->getPushUrl($adminId,$orderNo);
        curl_get($url);
    }

    /**
     * @return string
     * @获取推送url
     */
    public function getPushUrl($adminId,$orderNo):string
    {
        $token   = hash_make(['order',$adminId,$orderNo]);
        return $_SERVER['HTTP_HOST'].":9502?page=order&admin_id=$adminId&order=$orderNo&token=".$token;
    }

    /**
     * @param object $order
     * 升级为高级活动
     */
    public function OrderStateLevel2(object $order)
    {
        $order->activity->level = 2;
        return $order->activity->save();
    }

    /**
     * @param object $order
     * @天数续费
     */
    public function OrderStateLevel1(object $order)
    {
        return $order->activity->increment('days',$order['days']);
    }



}