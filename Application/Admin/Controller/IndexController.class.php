<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $this->display();
    }
    public function welcome(){
    	$data = array(
			'操作系统' => PHP_OS,
			'运行环境' => $_SERVER["SERVER_SOFTWARE"],
			'PHP运行方式' => php_sapi_name(),
			'上传附件限制' => ini_get('upload_max_filesize'),
			'执行时间限制' => ini_get('max_execution_time').'秒',
			'服务器时间' => date("Y年n月j日 H:i:s"),
			'北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
			'服务器域名/IP' => $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
			'剩余空间' => round((@disk_free_space(".") / (1024*1024)), 2).'M',
		);
		$this->assign('data', $data);
    	$this->display();
    }
}