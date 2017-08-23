<?php
namespace Admin\Controller;
use Think\Controller;
class SystemController extends CommonController{
	public function wxpay(){
		if(IS_POST){
			$data = I('post.');
            // 写入配置
            if (toConf('wxpay', $data)) {
                $this->success('修改成功', U('system/wxpay'), 1);
            } else {
                $this->error('修改失败', U('system/wxpay'), 1);
            }
		}else{
			$this->assign('wxpay', C('wxpay'));
			$this->display();
		}
	}

	public function sms(){
		if(IS_POST){
			$data = I('post.');
            // 写入配置
            if (toConf('sms', $data)) {
                $this->success('修改成功', U('system/sms'), 1);
            } else {
                $this->error('修改失败', U('system/sms'), 1);
            }
		}else{
			$this->assign('sms', C('sms'));
			$this->display();
		}
	}
	public function redis()
	{
		//phpinfo();
		$redis= new \Redis();
		$redis->connect('119.23.75.93',6379);
		echo 'ok';
	}
}

?>