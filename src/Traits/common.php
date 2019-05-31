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
    protected $icheck = <<<EOT
$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue'
});
EOT;
    /**
     * @var string
     */
    protected $upUrl;
    /**
     * @var array
     */
    protected $file_upload_js     = [
        'vendor/tw/global/fileupload/jquery.ui.widget.js',
        'vendor/tw/global/fileupload/jquery.iframe-transport.js',
        'vendor/tw/global/fileupload/jquery.fileupload.js',
    ];
    /**
     * @param array $scriptNames
     */
    public function bindScript($scriptNames = []) :void
    {
        if (is_array($scriptNames)) {
            foreach ($scriptNames as $script) {
                if ($script == "file_upload") {
                    $this->getFileUploadScript();
                } else {
                    Tw::script($this->$script);
                }
            }
        }

    }

    /**
     * 获取file_upload脚本 并加入一个hidden
     */
    public function getFileUploadScript():void
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
                $.amaran({'message':'上次成功！'});
            }
        } else {
            $.amaran({'message':data.result.info});
        }
    }
});
$(".up_img").on('click',function(){
    obj = $(this);
    $('#fileupload_').trigger('click');
})
EOT;
        Tw::script($script);
        Tw::setFileUploadUrl($this->getUpUrl());
        Tw::js($this->file_upload_js);
    }

    /**
     * @return string
     */
    public function getUpUrl():string
    {
        return config("tw.upload_url");
    }




}