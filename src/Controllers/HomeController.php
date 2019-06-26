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
            $hScore = empty($player) ? [] :Redis::hgetall(config('tw.redis_key.h1').$player['id']);
            return view('tw::home.index',compact('oData','player','judges','hScore'));
        } else
            return tw_abort("活动不存在或者已经过期！",404);
    }

    /**
     * @param int $judgesId
     * @see 评委打分页面
     */
    public function judges(int $judgesId)
    {
        // 获取当前打分选手
        $oJudges     = Tw::newModel("Judges")->find($judgesId);
        if (!$oJudges) {
            return tw_abort("评委不存在！",404);
        } else if (!(array)Tw::newModel('Activity')->getHomeActivity($oJudges->activity_id)) {
            return tw_abort("活动不存在或者已经过期！",404);
        } else {
            $bRes = $oJudges->checkLinkState();
            if (!$bRes) return tw_abort("评委姓名为: $oJudges->name 已处于连接状态！",403);
            $sActivityId = $oJudges->activity_id;
            if ($sActivityId) {
                $aPlayer = get_push_player($sActivityId);
                return view('tw::home.judges', compact('aPlayer', 'sActivityId', 'oJudges'));
            } else {
                return tw_abort("没有找到当前评委所在活动！", 404);
            }
        }
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
        $oData = Tw::newModel('Activity')->getHomeActivity($activityId);
        if ((array)$oData) {
            // 获取选手排名
            $aRank = Tw::newModel('Player')->getRank($activityId);
            // 活动背景
            return view('tw::home.rank', compact('aRank','oData'));
        } else {
            return tw_abort("活动不存在或者已经过期！",404);
        }
    }
}