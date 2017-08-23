$(function(){
	var e = "<i class='fa fa-times-circle'></i> ";
	$("#addUserForm").validate({
        rules: {
        	username: {
                required: !0,
                minlength: 2
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
            username: {
                required: e + "请输入您的用户名",
                minlength: e + "用户名必须两个字符以上"
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