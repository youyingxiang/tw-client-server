<?php

namespace Tw\Server\Traits;

trait HasAssets
{
    /**
     * @var array
     */
    public static $script = [];

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * @var array
     */
    public static $headerJs = [];
    /**
     * @var string
     */
    protected static $file_upload_url = "";




    /**
     * @var array
     */
    public static $baseCss = [
        'vendor/tw/global/bootstrap/css/bootstrap.min.css',
        'vendor/tw/global/bootstrap/css/font-awesome.min.css',
        'vendor/tw/system/iCheck/minimal/blue.css',
        'vendor/tw/system/select2/select2.min.css',
        'vendor/tw/system/dist/css/AdminLTE.min.css',
        'vendor/tw/system/dist/css/skins/skin-blue.min.css',
        'vendor/tw/system/kindeditor/themes/default/default.css',
        'vendor/tw/system/editable/bootstrap-editable.css',
        'vendor/tw/global/nprogress/nprogress.css',
        'vendor/tw/global/jQuery-gDialog/animate.min.css',
        'vendor/tw/global/Amaranjs/amaran.min.css',
        'vendor/tw/global/bootstrap/js/bootstrap-dialog.min.css',
        'vendor/tw/system/datetimepicker/bootstrap-datetimepicker.min.css',
        'vendor/tw/global/cropper/cropper.min.css',
        'vendor/tw/global/cropper/cropper.main.css',
    ];

    /**
     * @var array
     */
    public static $baseJs = [
        'vendor/tw/global/bootstrap/js/bootstrap.min.js',
        'vendor/tw/system/slimScroll/jquery.slimscroll.min.js',
        'vendor/tw/system/dist/js/app.min.js',
        'vendor/tw/global/jQuery/jquery.pjax.js',
        'vendor/tw/system/kindeditor/kindeditor-all.js',
        'vendor/tw/system/kindeditor/lang/zh-CN.js',
        'vendor/tw/global/jQuery/jquery.form.js',
        'vendor/tw/system/editable/bootstrap-editable.js',
        'vendor/tw/global/nprogress/nprogress.js',
        'vendor/tw/global/Amaranjs/jquery.amaran.min.js',
        'vendor/tw/global/bootstrap/js/bootstrap-dialog.min.js',
        'vendor/tw/system/datetimepicker/moment-with-locales.min.js',
        'vendor/tw/system/datetimepicker/bootstrap-datetimepicker.min.js',
        'vendor/tw/system/multiselect/multiselect.min.js',
        'vendor/tw/system/iCheck/icheck.min.js',
        'vendor/tw/system/select2/select2.min.js',
        'vendor/tw/system/select2/i18n/zh-CN.js',
        'vendor/tw/global/cropper/cropper.min.js',
        'vendor/tw/global/cropper/cropper.main.js',
        'vendor/tw/system/chart/Chart.min.js',
        'vendor/tw/system/dist/js/common.js',
    ];

    /**
     * @var string
     */
    public static $jQuery = 'vendor/tw/global/jQuery/jquery-2.2.3.min.js';

    /**
     * Add css or get all css.
     *
     * @param null $css
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function css($css = null)
    {
        if (!is_null($css)) {
            return self::$css = array_merge(self::$css, (array) $css);
        }

        $css = array_merge(static::$css, static::baseCss());
        $css = array_filter(array_unique($css));
        return view('tw::layout.css', compact('css'));
    }

    /**
     * @param null $css
     *
     * @return array|null
     */
    public static function baseCss($css = null)
    {
        if (!is_null($css)) {
            return static::$baseCss = $css;
        }
        return static::$baseCss;
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function js($js = null)
    {
        if (!is_null($js)) {
            return self::$js = array_merge(self::$js, (array) $js);
        }


        $js = array_merge(static::baseJs(), static::$js);

        $js = array_filter(array_unique($js));
        return view('tw::layout.js', compact('js'));
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function headerJs($js = null)
    {
        if (!is_null($js)) {
            return self::$headerJs = array_merge(self::$headerJs, (array) $js);
        }

        return view('tw::layout.js', ['js' => array_unique(static::$headerJs)]);
    }

    /**
     * @param null $js
     *
     * @return array|null
     */
    public static function baseJs($js = null)
    {
        if (!is_null($js)) {
            return static::$baseJs = $js;
        }

        return static::$baseJs;
    }

    /**
     * @param string $script
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function script($script = '')
    {
        if (!empty($script)) {
            return self::$script = array_merge(self::$script, (array) $script);
        }

        return view('tw::layout.script', ['script' => array_unique(self::$script)]);
    }

    /**
     * @param string $upType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function fileUpload()
    {
        if (self::$file_upload_url)
            return view("tw::layout.fileupload",['url'=>self::$file_upload_url]);
    }

    /**
     * @param string $upType
     */
    public static  function setFileUploadUrl(string $upUrl):void
    {
        self::$file_upload_url = $upUrl;
    }

    /**
     * @return string
     */
    public static function getFileUploadUrl():string
    {
        return self::$file_upload_url;
    }

    /**
     * @return string
     */
    public function jQuery()
    {
        return asset(static::$jQuery);
    }

}
