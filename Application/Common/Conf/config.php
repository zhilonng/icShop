<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' =>array(
        '__ADMIN__' =>'/Public/Admin',
        '__HOME__'  =>'/Public/Home',
        '__USER__'  =>'/Public/User',
        '__COMMON__' => '/Public/Common',
        '__VENDORS__' =>'/Public/Vendors',
        '__UP_GOODS__'          =>'/Public/Uploads/Goods',
        '__UP_NEWS__'           =>'/Public/Uploads/News',
    ),
    //'DEFAULT_FILTER'        =>'removeXss',//默认参数过滤方法 用于I函数
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'nuoya',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'b_',    // 数据库表前缀

    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    'ATTR_TYPE'             =>array('唯一属性','单选属性','复选属性'),
    'ATTR_INPUT_TYPE'       =>array('手工录入','从列表中选择','多行文本框'),
    'RECOM'                 =>array('is_best'=>'精品','is_new'=>'新品','is_hot'=>'热销'),
    'THUMB'                 =>array(
        'SIZE'=>array('mdu','sma','tny'),
        //原图
        'orgw'=>800,
        'orgh'=>800,
        //中
        'mduw'=>500,
        'mduh'=>500,
        'smaw'=>300,
        'smah'=>300,
        'tnyw'=>100,
        'tnyh'=>100,
    ),
    //短信API配置
    'SMS'  =>array(
        'accountSid' => '8a216da8582e9f53015833aeaaa4029e',
        'accountToken' => '3cbe866515fe4731bbe93919f8a81afd',
        'appId' =>  '8a216da8582e9f53015833aeaaf302a3',
        'serverIP' => 'sandboxapp.cloopen.com',
        'serverPort' => '8883',
        'softVersion' => '2013-12-26'
    ),
    //邮箱API配置
    'EMAIL'=>array(
        'Host' => 'smtp.163.com',
        'Username' => 'zhengliuqiushuang',
        'Password' => '236638170q',
        'From' => 'zhengliuqiushuang@163.com',
        'FromName' => 'Heaven商城',
    ),
    'WEIXIN'=>array(
        'appID'=>'wx29adb17caf4d2d2d',
        'appsecret'=>'0af9af3dbe66a8bd5bb4015e4d60e662',
        'token'=>'token',

    ),
    'MAMIWEIXIN'=>array(
        'appID'=>'wx1f940de154cb2474',
        'appsecret'=>'49231ea45f0aaf47cd2db06412fba0c7',
        'token'=>'token',

    ),
    /* 用户信息首页 */
    'USER_INDEX_SEX'       =>array(
        '未知','男','女'),

);