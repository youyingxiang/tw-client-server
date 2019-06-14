<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/13
 * Time: 5:09 PM
 */
namespace Tw\Server\Models;
use Tw\Server\Facades\Tw;
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
     * @param string $url
     * @return mixed
     */
    public function getQrCodeByUrl(string $url)
    {
        return QrCode::size(400)->color(255,0,255)
            ->backgroundColor(255,255,0)
            ->generate($url);
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
        } else {
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
        return $this->pay_state?"已支付":"未支付";
    }

    public function getSPayTypeAttribute():string
    {
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
}