function showErrMsg(msg) {
    layer.msg(msg, { icon: 5, time: 3000, shade: 0.5, shadeClose: true });
}

function showErrMsgTime(msg, times) {
    layer.msg(msg, { icon: 5, time: (times * 1000), shade: 0.5, shadeClose: true });
}

function showOk(msg) {
    layer.msg(msg, { icon: 1, time: 3000, shade: 0.5, shadeClose: true });
}

function showOkTime(msg,times) {
    layer.msg(msg, { icon: 1, time: (times * 1000), shade: 0.5, shadeClose: true });
}


//function showOk(msg, times) {
//    layer.msg(msg, { icon: 1, time: (times * 1000), shade: 0.5, shadeClose: true });
//}
/*=======================手机端封装方法开始===========================================*/
function MobileError(Msg) {
    //信息框
    layer.open({
        content: '' + Msg + '',
        time: 3,
        skin: 'msg'
    });
}

function MobileInfoTime(Msg,times) {
    //信息框
    layer.open({
        content: '' + Msg + '',
        time: times,
        skin: 'msg'
    });
}

function MobileShowInfo(Msg) {
    //信息框
    layer.open({
        content: '' + Msg + '',
        time: 2,
        skin: 'msg'
    });
}
function MobileConfimUrl(msg, url) {
    layer.open({
        content: msg,
        time: 3,
        skin: 'msg',
        end: function () {
            location.href = url;
        }
    });
}

function MobileShowInfoTimesAndUrl(times, msg, url) {
    layer.open({
        content: msg,
        time: times,
        skin: 'msg',
        end: function () {
            location.href = url;
        }
    });
}

function MobileShowAndLogin() {
    layer.open({
        content: '该操作需登录后才可继续，是否立即登录？',
        btn: ['&nbsp;立即登录&nbsp;', '&nbsp;暂不登录&nbsp;'],
        shadeClose: true,
        yes: function () {
            location.href = "/Mobile/Passport/Login.aspx?return_link=" + encodeURIComponent(location.href);
        }, no: function () {
            return false;
        }
    });

}
/*========================手机端封装方法结束============================================================*/









//读Cookie
function getCookie(objName) {//获取指定名称的cookie的值
    var arrStr = document.cookie.split("; ");
    for (var i = 0; i < arrStr.length; i++) {
        var temp = arrStr[i].split("=");
        if (temp[0] == objName) return unescape(temp[1]);
    }
    return "";
}

//删除cookie
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString()+"; path=/;domain=www.zkteco.com";
}



/*=================================================*/
function PCShowInfo(msg) {
    layer.msg(msg, { shade: [0.3], shadeClose: true, time: 2000 });
}

function PCShowConfirmToUrl(msg, btnYes, btnNo, url) {
    layer.open({
        content: msg,
        btn: [btnYes, btnNo],
        shadeClose: true,
        yes: function (index) {
            location.href = url;
        }, no: function () {
        }
    });
}


function ShowCloseWindow(msg, url) {

    layer.alert('' + msg + '', {
        closeBtn: 0
    }, function () {
        location.href = url;
    });
}


function PCUpdateMobileLogin(msg, btnYes, url) {
    layer.open({
        content: msg,
        btn: [btnYes],
        shadeClose: true,
        yes: function (index) {
            location.href = url;
        }, no: function () {
        }
    });
}

function showAndLogin() {
    layer.open({
        content: '该操作需登录后才可继续，是否立即登录？',
        btn: ['&nbsp;立即登录&nbsp;', '&nbsp;暂不登录&nbsp;'],
        shadeClose: true,
        yes: function () {
            location.href = "/login.html?return_link=" + encodeURIComponent(location.href);
        }, no: function () {
            return false;
        }
    });

}

function PCShowInfoAndJumpInfo(times, msg, url) {
    layer.msg(msg, {
        time: (times*1000),
        shade: [0.3],
        shadeClose: true,
        end: function (index) {
            location.href = url;
        }
    });
}

function PCShowInfoAndJump(msg, url) {
    layer.msg(msg, {
        time: 3000,
        shade: [0.3],
        shadeClose: true,
        end: function (index) {
            location.href = url;
        }
    });
}
function PCShowInfoAndRefresh(msg) {
    layer.msg(msg, {
        time: 3000,
        shade: [0.3],
        shadeClose: true,
        end: function (index) {
            location.reload();
        }
    });
}
/*=================================================*/

function GetRequest() {
    var url = location.search;
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = decodeURI(strs[i].split("=")[1]);
        }

    }
    return theRequest;
}

function inputCheck(input_type, input) {
    switch (input_type) {
        case "num": //验证是否是数字
            if (!(/^\d+$/.test(input))) {
                return false;
            }
            return true;
        case "qq": //验证QQ号码
            if (!(/^[1-9]{1}[0-9]{4,}$/.test(input))) {
                return false;
            }
            return true;
        case "email": //验证Email
            if (!(/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(input))) {
                return false;
            }
            return true;
        case "mobile": //验证手机号码
            if (!(/^(13[0-9]|14[57]|15[0-9]|17[0-9]|18[0-9])[0-9]{8}$/.test(input))) {
                return false;
            }
            return true;
        case "phone": //验证电话号码
            if (/^(400|800)([0-9\\-]{7,10})?$/.test(input)) {
                return true;
            }
            if (/^(\d{4}-|\d{3}-)?(\d{8}|\d{7})?$/.test(input)) {
                return true;
            }
            return false;
        case "fax": //验证传真号码
            if (/^(400|800)([0-9\\-]{7,10})?$/.test(input)) {
                return true;
            }
            if (/^(\d{4}-|\d{3}-)?(\d{8}|\d{7})?$/.test(input)) {
                return true;
            }
            return true;
        case "postalCode": //验证邮编
            if (!(/^\d{6}$/.test(input))) {
                return false;
            }
            return true;
        case "username": //验证用户名
            if (input.length < 4 || input.length > 20) { //用户名必须是4-20位
                return false;
            }
            if (!(/^[A-Za-z]+|[A-Za-z].*[0-9]|[0-9].*[A-Za-z]$/.test(input))) { //用户名中不能是纯数字
                return false;
            }
            return true;
        case "money":
            if (!(/^[0-9]+(.[0-9]{1,2})?$/.test(input))) {
                return false;
            }
            return true;
        case "password": //验证密码
            if (input.length < 8 || input.length > 20) {//密码必须是8-20位
                return false;
            }
            if (!(/^[A-Za-z].*[0-9]|[0-9].*[A-Za-z]$/.test(input))) {//密码中数字和英文字母必须同时存在
                return false;
            }
            return true;
        case "en":
            if (!(/^[A-Za-z ]+$/.test(input))) { //用户名中不能是纯数字
                return false;
            }
            return true;

        case "enandnumber":
            if (!(/^[A-Za-z\d ]+$/.test(input))) { //用户名中不能是纯数字
                return false;
            }
            return true;
            
        default:
            return true;
    }
}
