<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/6
 * Time: 10:52 AM
 */
namespace Tw\Server\Controllers;
use Tw\Server\Facades\Tw;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;
class HomeController extends Controller
{
    /**
     * @param int $activity 大屏幕首页
     */
    public function index(int $activityId)
    {
        $oData = Tw::newModel('Activity')->getHomeActivity($activityId);
        if ((array)$oData) {
            $judges = $oData->judges;
            $player = get_push_player($activityId);
            $hScore = Redis::hgetall(config('tw.redis_key.h1').$player['id']);
            return view('tw::home.index',compact('oData','player','judges','hScore'));
        } else
            abort(404);
    }

    /**
     * @param int $judgesId
     * @see 评委打分页面
     */
    public function judges(int $judgesId)
    {
        // 获取当前打分选手
        $aPlayer = Tw::newModel('Judges')->getPlayerByRedis($judgesId);
        if ($aPlayer)
            return view('tw::home.judges',compact('aPlayer'));
        else
            abort(404);
    }

    /**
     * 进行打分操作
     */
    public function postScoring()
    {
        $data['score']       = sprintf("%.2f",(float)request()->post('score'));
        $data['player_id']   = (int)request()->post('player_id');
        $data['activity_id'] = (int)request()->post('activity_id');
        $data['judges_id']   = (int)request()->post('judges_id');
        if ($data['player_id'] && $data['activity_id'] && $data['judges_id']) {
            try {
                Tw::moldelLogic(Tw::newModel("Judges"))->storeScore($data);
                return Tw::ajaxResponse("is ok","1");
            } catch (\Exception $e) {
                return Tw::ajaxResponse($e->getMessage());
            }
        } else {
            return Tw::ajaxResponse("输入参数不正确！");
        }

    }

    /**
     * @see 打分排名
     */
    public function rank(int $activityId)
    {
        // 获取选手排名
        $aRank = Tw::newModel('Player')->getRank($activityId);
        if ($aRank)
            return view('tw::home.rank',compact('aRank'));
        else
            abort(404);
    }
}