<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/31
 * Time: 10:36 AM
 */
namespace Tw\Server\Traits;
use Tw\Server\Facades\Tw;
trait Common
{
    /**
     * @var string
     */
    protected $icheck = <<<EOT
$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue'
});
$('.checkbox-toggle').on('ifChecked', function(event){
    var _this = $(this);
    var _table = _this.closest('.table');
    _table.find("tr td input[type='checkbox']").iCheck("check");
});
$('.checkbox-toggle').on('ifUnchecked', function(event){
    var _this = $(this);
    var _table = _this.closest('.table');
    _table.find("tr td input[type='checkbox']").iCheck("uncheck");
});
EOT;

    /**
     * @var string $select2插件
     */
    protected $select2 = <<<EOT
$(".select2").select2({language:"zh-CN"});
EOT;
    /**
     * @var array
     */
    protected $file_upload_js     = [

    ];


    
    /**
     * @var string
     */
    protected $upUrl;


    /**
     * @param array $scriptNames
     */
    public function bindScript($scriptNames = []) :void
    {
        if (is_array($scriptNames)) {
            foreach ($scriptNames as $script) {
                if (method_exists($this,$script)) {
                    $this->$script();
                } else if (property_exists($this,$script)) {
                    Tw::script($this->$script);
                }
            }
        }

    }

    /**
     * 获取file_upload脚本 并加入一个hidden
     */
    public function file_upload():void
    {
        $token = csrf_token();
        $script = <<<EOT
$('#fileupload_').fileupload({
    dataType: 'json',
    formData: function (form) {
       return ([{name: '_token', value:"$token"}]);
    },
    done: function (e, data) {
        if (data.result.error == 0) {
            var up_url = data.result.url.trim();
            obj.parent().prev().val(up_url);
            if (obj.prev().children('img').length>0) {
                obj.prev().attr('href',up_url );
                obj.prev().find('img').attr('src',up_url );
                $.amaran({
                    content:{
                        title:'通知',
                        message: "上传成功",
                        info:'',
                        icon:'fa fa-check-square'
                    },
                    theme:'awesome ok',
                    position  :'top right'
                });
            }
        } else {
            $.amaran({
                content:{
                    title:'通知',
                    message:data.result.message,
                    info:'',
                    icon:'fa fa-warning'
                },
                theme:'awesome error',
                position  :'top right'
            });
        }
    }
});
$(".up_img").on('click',function(){
    obj = $(this);
    $('#fileupload_').trigger('click');
})
EOT;
        Tw::script($script);
    }

    /**
     * 表格修改
     */
    public function editable():void
    {
        $token = csrf_token();
        $script = <<<EOT
$('.editable').editable({
        emptytext: "empty",
        params: function(params) {      //参数
            var data = {};
            data['id'] = params.pk;
            data['_token'] = "$token";
            data['_method'] = 'PUT';
            data[params.name] = params.value;
            return data;
        },
        success: function(response, newValue) {
            if(response.status == 1){
            }else{
                return response.info;
            }
        }
    });
EOT;
        Tw::script($script);
    }

    /**
     * 推送
     */
    public function pushAjax():void
    {

        $token = hash_make(['push']);
        $script = <<<EOT
$('.push').on('click',function(){
    var url = $(this).attr('data-url').trim();
    var id  = $(this).attr('data-id').trim();
    $.ajax({
            url: url, 
            type:'get', 
            dataType: "json", 
            error:function(data){
                 $.amaran({'message':"服务器繁忙, 请联系管理员！"});
                 return;
            },  
            success:function(result){
                if(result.status == 1){
                    pushSwoole(id);
                    $.amaran({
                        content:{
                            title:'通知',
                            message: result.info,
                            info:'',
                            icon:'fa fa-check-square'
                        },
                        theme:'awesome ok',
                        position  :'top right'
                    });
                    $.pjax({url: result.url, container: '#pjax-container', fragment:'#pjax-container'})                   
                } else {
                    $.amaran({
                        content:{
                            title:'通知',
                            message:result.info,
                            info:'',
                            icon:'fa fa-warning'
                        },
                        theme:'awesome error',
                        position  :'top right'
                    });
                }
            },
    })
});
function pushSwoole(id)
{
   var wsUrl = "ws://$_SERVER[HTTP_HOST]:9502?page=push&token=$token";
   var ws = new WebSocket(wsUrl);
   ws.onopen= function (event) {
        ws.send('{"type":"1","player":"'+id+'"}');
   }
}

EOT;
        Tw::script($script);
    }

    /**
     * @return string
     */
    public function getUpUrl():string
    {
        return config("tw.upload_url");
    }






}