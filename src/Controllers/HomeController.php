<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/6
 * Time: 10:52 AM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
use Tw\Server\Facades\Tw;
class HomeController extends Controller
{
    /**
     * @param int $activity 大屏幕首页
     */
    public function index(int $activityId)
    {
        $oData = Tw::newModel('Activity')->getHomeActivity($activityId);
        if ((array)$oData) {
            $player = $oData->players()->where('push_state',1)->first();
            $judges = $oData->judges;
            return view('tw::home.index',compact('oData','player','judges'));
        } else
            abort(404);
    }
}