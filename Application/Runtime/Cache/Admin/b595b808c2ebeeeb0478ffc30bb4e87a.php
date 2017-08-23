<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="renderer" content="webkit">
<meta http-equiv="Cache-Control" content="no-siteapp" />
<title>广州市羿于臣贸易有限公司</title>
<!--[if lt IE 9]>
<meta http-equiv="refresh" content="0;ie.html" />
<![endif]-->
<link rel="shortcut icon" href="/Public/Admin/img/favicon.ico">
<link href="/Public/Admin/css/bootstrap.min.css" rel="stylesheet">
<link href="/Public/Admin/css/font-awesome.min.css" rel="stylesheet">
<link href="/Public/Admin/css/animate.min.css" rel="stylesheet">
<link href="/Public/Admin/css/style.min.css" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg  pace-done fixed-nav" style="overflow:hidden">
<div id="wrapper">
<!--左侧导航开始-->
<nav style="width:160px" class="navbar-default navbar-static-side" role="navigation">
<div class="nav-close"><i class="fa fa-times-circle"></i>
</div>
<div class="sidebar-collapse">
<ul class="nav" id="side-menu"><li><a href="<?php echo U('index/index');?>"><i class="fa fa-home"></i><span class="nav-label">主页</span></a></li>

<li><a href="javascript:;"><i class="fa fa-edit"></i> <span class="nav-label">生产进度管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('Order/index');?>">生产进度列表</a></li>
<li><a class="J_menuItem" href="<?php echo U('Order/AddOrder');?>">添加生产订单</a>
</li></ul>
</li>

<li><a href="javascript:;"><i class="fa fa-columns"></i> <span class="nav-label">出货记录管理</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('Product/index');?>">出货记录列表</a></li><li><a class="J_menuItem" href="<?php echo U('Product/add');?>">添加出货记录</a></li></ul></li>


<li><a href="javascript:;"><i class="fa fa-edit"></i> <span class="nav-label">针织生产进度</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('ZhenzhiOrder/index');?>">针织进度列表</a></li>
<li><a class="J_menuItem" href="<?php echo U('ZhenzhiOrder/AddOrder');?>">添加针织订单</a>
</li></ul>
</li>

<li><a href="javascript:;"><i class="fa fa-columns"></i> <span class="nav-label">针织出货记录</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('ZhenzhiProduct/index');?>">针织出货记录</a></li><li><a class="J_menuItem" href="<?php echo U('ZhenzhiProduct/add');?>">添加针织出货</a></li></ul></li>
<!-- <li><a href="javascript:;"><i class="fa fa-th-large"></i> <span class="nav-label">销售记录管理</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('Sale/index');?>">销售记录列表</a></li><li><a class="J_menuItem" href="<?php echo U('Sale/add');?>">添加销售记录</a></li></ul></li>
 --><li><a href="javascript:;"><i class="fa fa-desktop"></i> <span class="nav-label">选项管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('Designer/index');?>">设计师列表</a></li><li><a class="J_menuItem" href="<?php echo U('Designer/add');?>">添加设计师</a></li>
<li><a class="J_menuItem" href="<?php echo U('Paper/index');?>">纸样师列表</a></li><li><a class="J_menuItem" href="<?php echo U('Paper/add');?>">添加纸样师</a></li>
<li><a class="J_menuItem" href="<?php echo U('Factory/index');?>">工厂列表</a></li><li><a class="J_menuItem" href="<?php echo U('Factory/add');?>">添加工厂</a></li>
<li><a class="J_menuItem" href="<?php echo U('Color/index');?>">颜色列表</a></li><li><a class="J_menuItem" href="<?php echo U('Color/add');?>">添加颜色</a></li>
<li><a class="J_menuItem" href="<?php echo U('Size/index');?>">尺码列表</a></li><li><a class="J_menuItem" href="<?php echo U('Size/add');?>">添加尺码</a></li>
<li><a class="J_menuItem" href="<?php echo U('Logo/index');?>">品牌列表</a></li><li><a class="J_menuItem" href="<?php echo U('Logo/add');?>">添加品牌</a></li>
</ul>
<!-- 
<li><a href="javascript:;"><i class="fa fa-desktop"></i> <span class="nav-label">知识管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('article/index');?>">知识库</a></li><li><a class="J_menuItem" href="<?php echo U('article/add');?>">添加知识</a></li>
<li><a class="J_menuItem" href="<?php echo U('article/category');?>">分类管理</a></li><li><a class="J_menuItem" href="<?php echo U('article/addCate');?>">添加分类</a></li></ul></li>
<li><a href="javascript:;"><i class="fa fa-file-text"></i> <span class="nav-label">常见问题</span><span class="fa arrow"></span></a><ul class="nav nav-second-level">
<li><a class="J_menuItem" href="<?php echo U('help/index');?>">常见问题</a></li><li><a class="J_menuItem" href="<?php echo U('help/add');?>">添加问题</a></li><li><a class="J_menuItem" href="<?php echo U('help/category');?>">分类管理</a></li><li><a class="J_menuItem" href="<?php echo U('help/addCate');?>">添加分类</a></li></ul></li>
<li><a href="javascript:;"><i class="fa fa fa-bar-chart-o"></i><span class="nav-label">专家管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('expert/index');?>">专家列表</a></li><li><a class="J_menuItem" href="<?php echo U('expert/add');?>">添加专家</a></li><li><a class="J_menuItem" href="<?php echo U('expert/level');?>">职位管理</a></li></ul></li>
<li><a href="javascript:;"><i class="fa fa-wrench"></i> <span class="nav-label">服务管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('service/index');?>">服务列表</a></li><li><a class="J_menuItem" href="<?php echo U('service/add');?>">添加服务</a></li><li><a class="J_menuItem" href="<?php echo U('service/category');?>">服务分类</a></li><li><a class="J_menuItem" href="<?php echo U('service/addCate');?>">添加分类</a></li><li><a class="J_menuItem" href="<?php echo U('service/lunbo');?>">轮播管理</a></li></ul></li>
<li><a href="<?php echo U('finance/index');?>" class="J_menuItem"><i class="fa fa-magic"></i> <span class="nav-label">财务管理</span></a></li> 
<li><a href="javascript:;"><i class="fa fa-flask"></i> <span class="nav-label">微信管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('wechat/info');?>">公众号信息</a></li><li><a class="J_menuItem" href="<?php echo U('wechat/menu');?>">自定义菜单</a></li></ul></li>-->
<li><a href="javascript:;"><i class="fa fa-table"></i> <span class="nav-label">权限管理</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('rbac/index');?>">操作人列表</a></li><li><a class="J_menuItem" href="<?php echo U('rbac/addUser');?>">添加操作人</a></li><li><a class="J_menuItem" href="<?php echo U('rbac/role');?>">角色列表</a></li><li><a class="J_menuItem" href="<?php echo U('rbac/addRole');?>">添加角色</a></li>
<li><a class="J_menuItem" href="<?php echo U('rbac/savePassword');?>">修改用户密码</a></li>
<li><a class="J_menuItem" href="<?php echo U('rbac/editPassword');?>">修改删除密码</a></li>

</ul>
<!-- <li><a class="J_menuItem" href="<?php echo U('rbac/node');?>">节点列表</a></li><li><a class="J_menuItem" href="<?php echo U('rbac/addNode');?>">添加节点</a></li>
</li>
 -->
<!-- <li><a href="javascript:;"><i class="fa fa-cog"></i> <span class="nav-label">系统设置</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li><a class="J_menuItem" href="<?php echo U('system/sms');?>">短信设置</a></li></ul></li>  -->

</ul>
</div>
</nav>
<!--左侧导航结束-->
<!--右侧部分开始-->
<div id="page-wrapper" style="margin-left: 200px" class="gray-bg dashbard-1">
<div class="row border-bottom">
<nav class="navbar navbar-fixed-top" role="navigation" ><div class="navbar-header"><a href="<?php echo U('index/index');?>">
<img src="/Public/Admin/img/mylogo1.png" alt=""></a></div>
</nav>
</div>
<div class="row content-tabs">
<button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
</button>
<nav class="page-tabs J_menuTabs"><div class="page-tabs-content"><a href="javascript:;" class="active J_menuTab" data-id="<?php echo U('index/welcome');?>">首页</a></div>
</nav>
<button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
</button>
<div class="btn-group roll-nav roll-right"><button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span></button><ul role="menu" class="dropdown-menu dropdown-menu-right"><li class="J_tabShowActive"><a>定位当前选项卡</a></li><li class="divider"></li><li class="J_tabCloseAll"><a>关闭全部选项卡</a></li><li class="J_tabCloseOther"><a>关闭其他选项卡</a></li></ul>
</div>
<a href="<?php echo U('public/logout');?>" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
</div>
<div class="row J_mainContent" id="content-main">
<iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="<?php echo U('index/welcome');?>" frameborder="0" data-id="<?php echo U('index/welcome');?>" seamless></iframe>
</div>
<div class="footer">
<div class="pull-right">&copy; 2017 <a href="#" target="_blank">管理员联系电话:13265357557</a>
</div>
</div>
</div>
<!--右侧部分结束-->
</div>
<script src="/Public/Admin/js/jquery.min.js?"></script>
<script src="/Public/Admin/js/bootstrap.min.js"></script>
<script src="/Public/Admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/Public/Admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="/Public/Admin/js/plugins/layer/layer.js"></script>
<script src="/Public/Admin/js/hplus.min.js"></script>
<script src="/Public/Admin/js/contabs.min.js"></script>
<script src="/Public/Admin/js/plugins/pace/pace.min.js"></script>
</body>
</html>