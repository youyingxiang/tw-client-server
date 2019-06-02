<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/31
 * Time: 9:15 AM
 */
namespace Tw\Server\Controllers;
use Illuminate\Routing\Controller;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Arr;
use Tw\Server\Traits\Common;
use Tw\Server\Requests\AdminRequest;
use Tw\Server\Requests\UpdateAdminPost;
class AdminController extends Controller
{

    use Common;
    /**
     * 创建
     */
    public function getUserInfo()
    {
        $this->bindScript(['file_upload','icheck']);
        $user = Tw::authLogic()->guard()->user();
        return view('tw::user.info',compact('user'));
    }

    /**
     *
     */
    public function postUserInfo(AdminRequest $request)
    {
        $id = request()->post("id");
        $aData = $request->post();
        Arr::forget($aData,['id']);
        return $this->Model()->update($id,$aData);

    }

    public function Model():object
    {
       return Tw::moldelLogic(Tw::newModel("Admin"));
    }


}
