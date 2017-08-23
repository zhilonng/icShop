<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>后台登录</title>
        <meta name="author" content="DeathGhost" />
        <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" />
        <link rel="stylesheet" type="text/css" href="/Public/Admin/css/login.css" />
        <style>
        body{height:100%;background:#16a085;overflow:hidden;}
        canvas{z-index:-1;position:absolute;}
        </style>
        <script src="/Public/Admin/js/jquery.js"></script>
        <script src="/Public/Admin/js/Particleground.js"></script>
        <script>
        $(document).ready(function() {
            //粒子背景特效
            $('body').particleground({
                dotColor: '#5cbdaa',
                lineColor: '#5cbdaa'
            });
            $('#username').focus();
        });
        </script>
    </head>
    <body>
        <form action="<?php echo U('public/dologin');?>" method="post">
            <dl class="admin_login">
                <dt>
                    <strong>站点后台管理系统</strong>
                    <em>Management System</em>
                </dt>
                <dd class="user_icon">
                <input type="text" placeholder="账号" name="username" id="username" class="login_txtbx" autocomplete="off" />
                </dd>
                <dd class="pwd_icon">
                    <input type="password" placeholder="密码" name="password" class="login_txtbx" autocomplete="off"/>
                </dd>
                <dd>
                    <input type="submit" value="立即登陆" class="submit_btn"/>
                </dd>
            </dl>
        </form>
    </body>
</html>