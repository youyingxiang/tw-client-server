<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/4
 * Time: 3:26 PM
 */
namespace Tw\Server\Tool;
class Button
{
    /**
     * @var null
     */
    protected $url = null;
    /**
     * @var null
     */
    protected $type = null;


    /**
     * Button constructor.
     */
    public function __construct($param)
    {
        $this->url  = $param['url'] ?? '';
        $this->type = $param['type'] ?? '';
        $this->id   = $param['id'] ?? '';
    }

    /**
     * @return string
     */
    public function create():string
    {

        if (!empty(request()->input('activity_id'))) {
            $this->url .= "?activity_id=".hash_encode(request()->input('activity_id'));
        }
        return "<a href='$this->url' class='btn btn-sm btn-primary'><i class='fa fa-save'></i> 增加</a>";
    }

    /**
     * @return string
     */
    public function edit():string
    {
        return "<a class='btn btn-primary btn-xs' href='$this->url'><i class='fa fa-edit'></i> 编辑</a>";
    }

    /**
     * @return string
     */
    public function delete():string
    {
        if (!empty(request()->input('activity_id'))) {
            $this->url .= "?activity_id=".hash_encode(request()->input('activity_id'));
        }
        return "<a class=\"btn btn-danger btn-xs delete-one\" href=\"javascript:void(0);\" data-url=\"".$this->url."\"  data-csrftoken=\"".csrf_token()."\"  data-id=\"".hash_encode($this->id)."\"><i class=\"fa fa-trash\"></i> 删除</a>";
    }

    /**
     * @return string
     */
    public function delete_all():string
    {
        if (!empty(request()->input('activity_id'))) {
            $this->url .= "?activity_id=".hash_encode(request()->input('activity_id'));
        }
        return  "<a class=\"btn btn-sm btn-danger delete-all\" data-csrftoken=\"".csrf_token()."\"  href=\"javascript:void(0);\" data-url=\"".$this->url."\" ><i class=\"fa fa-trash\"></i> 删除选中</a>";
    }

    /**
     * @return string
     * @see 推送
     */
    public function push():string
    {
        return "<a class=\"btn btn-primary btn-xs push\"  data-url=\"".$this->url."\"  data-id=\"".$this->id."\"  href=\"javascript:void(0);\"><i class=\"fa fa-sign-out\"></i> 推送大屏幕</a>";
    }


    /**
     * @return string
     */
    public function getbutton():string
    {
        $html = '';
        $param = $this->type;
        if (method_exists($this,$param)) {
            $html = call_user_func([$this,$param]);
        }
        return $html;
    }
}