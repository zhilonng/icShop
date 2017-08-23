<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller{

	// 登录
	public function login(){
		$this->display();
	}

	// 登录表单处理
	public function dologin(){
		if(IS_POST){	
			$post = I('post.');
			if(empty($post['username'])) $this->error('请输入用户名', U('public/login'), 1);
			if(empty($post['password'])) $this->error('请输入密码', U('public/login'), 1);
			$_result = M('admin')->where(array('username'=>$post['username']))->find();
			if($_result){
				if($_result['password'] == md5($post['password'])){
					if($_result['lock']){
						$this->error('该用户已被冻结！', U('public/login'), 1);
					}else{
						$data = array(
							'admin_id' => $_result['admin_id'],
							'logintime' => time(),
							'loginip' => ip2long(get_client_ip())
							);
						M('admin')->save($data);
						session(C('USER_AUTH_KEY'), $_result['admin_id']);
						session('username', $_result['username']);

						// 超级管理员识别
						if($_result['username'] == C('RBAC_SUPERADMIN')){
							session(C('ADMIN_AUTH_KEY'), true);
						}
						$_SESSION[C('USER_AUTH_KEY')];
						
						$rbac=new \Org\Util\Rbac();  
						//取出用户权限信息  
						$rbac::saveAccessList($_result['admin_id']);  
						// p($_SESSION);

						$this->success('登录成功！', U('Admin/index/index'));
					}
				}else{
					$this->error('用户名或密码不正确！', U('public/login'), 1);
				}
			}else{
				$this->error('用户不存在！', U('public/login'), 1);
			}
		}else{
			$this->error('非法操作！', U('public/login'), 1);
		}
	}


	// 退出
	public function logout(){
        session(null);
        $this->success('注销成功！', U('public/login'), 1);
    }
}

?>