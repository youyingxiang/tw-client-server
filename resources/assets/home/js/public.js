// 验证手机号
function isPhoneNo(phone) {
    var pattern = /^1[349578]\d{9}$/;
    return pattern.test(phone);
}
