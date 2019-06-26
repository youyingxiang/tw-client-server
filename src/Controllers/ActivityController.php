<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/3
 * Time: 3:08 PM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Facades\Redis;
use Tw\Server\Requests\ActivityRequest;
use Tw\Server\Traits\Common;
class ActivityController extends Controller
{
    /**
     * @author 游兴祥
     * @see 活动控制器
     * @describe 注意：防止越权
     * crud 操作where带admin_id
     * 流程 request(验证器)->controller(控制器)->logic(逻辑层)->model(模型层)
     */

    use Common;
    /**
     * 首页
     */
    public function index()
    {
        $this->bindScript(['icheck']);
        $aWhereData = request()->query();
        $aData = $this->Model()->query($aWhereData);
        return view('tw::activity.index',compact('aData'));
    }

    /**
     * 新增
     */
    public function create()
    {
        $this->bindScript(['select2','file_upload']);
        return view("tw::activity.form");
    }



    /**
     * @param $id
     */
    public function edit($id)
    {
        $aData = $this->Model()->find($id) ?? abort(404);
        $this->bindScript(['select2','file_upload']);
        return view("tw::activity.form",compact('aData'));
    }

    /**
     * @param Request $request
     */
    public function store(ActivityRequest $request)
    {
        $aData = $request->post();
        $aData['admin_id'] = Tw::authLogic()->guard()->id();
        // 添加高级活动要收费
        if (isset($aData['level']) && $aData['level'] == 2) {
            return $this->Model()->highLevelStore($aData);
        } else
            return $this->Model()->store($aData);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(ActivityRequest $request, $id)
    {
        $aData = $request->post();
        return $this->Model()->update($id,$aData);
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        $id = request()->post('id');
        return $this->Model()->destroy($id);
    }

    /**
     * @return object
     */
    public function Model():object
    {
        return Tw::moldelLogic(Tw::newModel("Activity"));
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @see 控制大屏幕
     */
    public function control(int $id)
    {
        $oAcitity = $this->Model()->find($id);
        if ($oAcitity)
            return view('tw::activity.control');
        else
            return tw_abort("活动不存在！",404);
    }

    /**
     * @see 更换下一位选手
     */
    public function nextPlayer(int $activity_id)
    {
        //下一位选手的id
        $nextPlayId = Tw::newModel("Activity")->getNextPushStateIdByActivityId($activity_id);
        if ($nextPlayId) {
            Tw::moldelLogic(Tw::newModel("Player"))->pushPlayer($nextPlayId);
            return Tw::ajaxResponse($nextPlayId,'1');
        } else {
            return Tw::ajaxResponse("已经没有可以推送的选手选择了！");
        }

    }

    /**
     * @param int $id
     * @see 发布活动
     */
    public function release(int $id)
    {
        if ($this->Model()->release($id))
            return Tw::ajaxResponse("发布成功",route('tw.activity.index'));
        else
            return Tw::ajaxResponse("发布失败");

    }

}