<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $this->display();
    }
    public function welcome(){
    	// 总订单数
    	$this->Order = M('Order')->count();

        //今日订单数量
        $a=strtotime(date('Y-m-d'));
        $b=time();
        $where['order_addtime']=array('between',array($a,$b));
        $count1=M('Order')->where($where)->count();
                //今日返单新增返单数量
        //$where['redo_addtime']=array('between',array($a,$b));
        //$count2=M('Redo')->where($where)->count();
        //$this->todayCount=$count1+$count2;
        $this->todayCount=$count1;

    	//  今日出货数
        $where['product_addtime']=array('between',array($a,$b));
    	$this->todayProduct=M('Product')->where($where)->count();

        //今日销售新增数量
        $where['sale_addtime']=array('between',array($a,$b));
        $this->todaysale=M('Sale')->where($where)->count();
    	// // 总用户数
    	// $this->userCount = M('user')->count();

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