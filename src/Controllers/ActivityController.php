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
        $aData = $this->Model()->find($id);
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

    public function Model():object
    {
        return Tw::moldelLogic(Tw::newModel("Activity"));
    }






}