$(function(){
	var e = "<i class='fa fa-times-circle'></i> ";
	$("#addExpertForm").validate({
        rules: {
            nickname: {
                required: !0
            },
            tel: {
                required: !0
            },
        	username: {
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
            nickname: {
                required: e + "请输入姓名"
            },
            tel: {
                required: e + "请输入联系方式"
            },
            username: {
                required: e + "请输入账号"
            },
            password: {
                required: e + "请输入您的密码",
                minlength: e + "密码必须5个字符以上"
            },
            confirm_password: {
                required: e + "请再次输入密码",
                minlength: e + "密码必须5个字符以上",
                equalTo: e + "两次输入的密码不一致"
            }
        }
    })
})