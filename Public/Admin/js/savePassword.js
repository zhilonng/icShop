$(function(){
	var e = "<i class='fa fa-times-circle'></i> ";
	$("#signupForm").validate({
        rules: {
        	oldpwd: {
                required: !0
            },
            password: {
                required: !0,
                minlength: 5
            },
            confirm_password: {
                required: !0,
                minlength: 5,
                equalTo: "#password"
            }
        },
        messages: {
            oldpwd: {
                required: e + "请输入您的旧密码"
            },
            password: {
                required: e + "请输入您的新密码",
                minlength: e + "密码必须5个字符以上"
            },
            confirm_password: {
                required: e + "请再次输入新密码",
                minlength: e + "密码必须5个字符以上",
                equalTo: e + "两次输入的密码不一致"
            }
        }
    })
})