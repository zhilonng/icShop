<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function _initialize(){
		// 判断用户是否登录
        if(!isset($_SESSION[C('USER_AUTH_KEY')])){
            $this->redirect('public/login');
        }

        $notAuth = in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE'))) || in_array(ACTION_NAME, explode(',', C('NOT_AUTH_ACTION'))) ;
        if(C('USER_AUTH_ON') && !$notAuth){
        	$rbac=new \Org\Util\Rbac();  
		    //检测是否登录，没有登录就打回设置的网关  
		    $rbac::checkLogin();  
		    //检测是否有权限没有权限就做相应的处理  
			if(!$rbac::AccessDecision()){  
			    echo '<script type="text/javascript">alert("没有权限");</script>';  
			    die();  
			}  
        }
	}
}
?>