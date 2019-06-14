<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/13
 * Time: 5:05 PM
 * Desc: 支付
 */
namespace Tw\Server\Controllers;
use EasyWeChat;
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
        $aInput['days']        = (int)request()->post('days');     // 活动续费天数
        return $this->Model()->generateOrder($aInput);
    }

    /**
     * 支付二维码
     */
    public function qrCode($order_no)
    {
        $aInput['order_no'] = $order_no;
        $qrcode = $this->Model()->generateQrCode($aInput);
        return view("tw::activity.qrcode",compact('qrcode'));
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
        $app = EasyWeChat::payment();
        $response = $app->handlePaidNotify(function($message, $fail){
            //$order = $message['out_trade_no'];
            file_put_contents("wecahtinfo.log","\r\n".json_encode($fail),FILE_APPEND);
//            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
//                // 用户是否支付成功
//                if (array_get($message, 'result_code') === 'SUCCESS') {
//
//                } elseif (array_get($message, 'result_code') === 'FAIL') {
//                    $order->status = 'paid_fail';
//                }
//            } else {
//                return $fail('通信失败，请稍后再通知我');
//            }
//            return true; // 返回处理完成
        });
        return $response;
    }

    /**
     * @param $order_no
     * @see 检测订单支付状态
     */
    public function checkOrderState($order_no)
    {
        $aInput['order_no'] = $order_no;
        $aData = $this->Model()->query();
        if (isset($aData[0]['pay_state'])) {
            $iState =  $aData[0]['pay_state'];
            if ($iState == 1) {
                return Tw::ajaxResponse("支付成功！",route("tw.index.index"));
            } elseif  ($iState == 0) {
                return Tw::ajaxResponse("支付未完成！");
            } else {
                return Tw::ajaxResponse("支付失败！");
            }
        } else {
            return Tw::ajaxResponse("订单不存在！");
        }

    }


}