<?php
return array(
	// ====================Rbac权限控制配置==========================
	'RBAC_SUPERADMIN'	=> 'admin',			// 超级管理员名称
	'ADMIN_AUTH_KEY'	=> 'superadmin',	// 超级管理员识别
	'USER_AUTH_ON'		=> true,			// 是否开启验证
	'USER_AUTH_TYPE'	=> 2, 				// 验证类型 1：登录验证，2：时时验证
	'USER_AUTH_KEY' 	=> 'uid',  			// 用户认证识别号
	'NOT_AUTH_MODULE' 	=> 'Index,Public', 				// 无需认证的控制器
	'NOT_AUTH_ACTION' 	=> 'savePassword', 				// 无需认证的方法
	'RBAC_ROLE_TABLE' 	=> 'b_role', 		// 角色表名称
	'RBAC_USER_TABLE' 	=> 'b_role_user', 	// 角色与用户的中间表名称
	'RBAC_ACCESS_TABLE' => 'b_access', 		// 权限表名称
	'RBAC_NODE_TABLE' 	=> 'b_node', 		// 节点表名称




    // 修改__PUBLIC__的默认位置
    'TMPL_PARSE_STRING'  => array(
        '__PUBLIC__' => __ROOT__.'/Public/Admin',
        '__VENDORS__' =>'/Public/Vendors',
        '__UP_ORDER__'          =>'/Public/Uploads/Admin',
        ),

    // 伪静态设置
    'URL_HTML_SUFFIX'	=> '',
    'ARTICLE_CATE' =>array(
    	'1'=>'案例',
   		'2'=>'新闻',
    	),
);