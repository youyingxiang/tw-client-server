<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/13
 * Time: 5:05 PM
 * Desc: 支付
 */
namespace Tw\Server\Controllers;
use Tw\Server\Facades\Tw;
use Illuminate\Routing\Controller;
class PayOrderController extends Controller
{

    /**
     * @see 生成订单
     */
    public function storeOrder()
    {
        $aInput['type']        = (int)request()->post('type');     // 支付信息
        $aInput['pay_type']    = (int)request()->post('pay_type'); // 支付类型
        $aInput['activity_id'] = (int)request()->post('activity_id');
        return $this->Model()->generateOrder($aInput);
    }

    /**
     * 支付二维码
     */
    public function qrCode($order_no)
    {
        $aInput['order_no'] = $order_no;
        return $this->Model()->generateQrCode($aInput);
    }

    /**
     * @return object
     */
    public function Model():object
    {
        return Tw::moldelLogic(Tw::newModel("PayOrder"));
    }

    /**
     * 微信回调
     */
    public function wechatNotify()
    {
        dd('124');
    }

}