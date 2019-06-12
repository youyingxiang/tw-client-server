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
        $aWhereData['level'] = 1;
        $aData1 = $this->Model()->query($aWhereData);
        $aWhereData['level'] = 2;
        $aData2 = $this->Model()->query($aWhereData);
        return view('tw::activity.index',compact('aData1','aData2'));
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

    public function control($id)
    {
        return view('tw::activity.control');
    }

}