<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/5
 * Time: 9:52 AM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
use Tw\Server\Facades\Tw;
use Tw\Server\Requests\PlayerRequest;
use Tw\Server\Traits\Common;
class PlayerController extends Controller
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
        $this->bindScript(['icheck','editable','pushAjax','select2']);
        $aWhereData = request()->query();
        $aData = $this->Model()->query($aWhereData);
        return view('tw::player.index',compact('aData'));
    }

    /**
     * 增加
     */
    public function create()
    {
        $this->bindScript(['select2','file_upload']);
        $oActivitys = Tw::authLogic()->guard()->user()->activitys;
        return view("tw::player.form",compact('oActivitys'));
    }

    /**
     * @param $id
     * 修改
     */
    public function edit($id)
    {

        $this->bindScript(['select2','file_upload']);
        $aData = $this->Model()->find($id) ?? abort(404);
        $oActivitys = Tw::authLogic()->guard()->user()->activitys;
        return view("tw::player.form",compact('aData','oActivitys'));
    }

    /**
     * @param JudgesRequest $request
     * 存储
     */
    public function store(PlayerRequest $request)
    {
        $aData = $request->post();
        $aData['admin_id'] = Tw::authLogic()->guard()->id();
        return $this->Model()->store($aData);
    }
    /**
     * @param Request $request
     * @param $id
     */
    public function update(PlayerRequest $request, $id)
    {
        $aData = $request->post();
        return $this->Model()->update($id,$aData);
    }

    /**
     * @param $id
     * @return mixed
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
        return Tw::moldelLogic(Tw::newModel("Player"));
    }

    /**
     * 推送大屏幕
     */
    public function push(int $id)
    {
        return $this->Model()->pushPlayer($id);
    }

    /**
     * @see 清除选手得分
     */
    public function clearScoreAll()
    {
        return $this->Model()->clearScoreAll();
    }
}