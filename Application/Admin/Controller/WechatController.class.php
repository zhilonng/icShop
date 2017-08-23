<?php
namespace Admin\Controller;
use Think\Controller;
class WechatController extends CommonController{

	// 微信公众号信息
	public function info(){
		if(IS_POST){
			$data = I('post.');
            // 写入配置
            if (toConf('wechat', $data)) {
                $this->success('修改成功', '', 1);
            } else {
                $this->error('修改失败', '', 1);
            }
		}else{
			$this->assign('wechat', C('wechat'));
			$this->display();
		}
		
	}

	// 菜单列表
	public function menu(){
		$menu = M('wechatMenu')->select();
		$this->assign('menu', menuForLevel($menu));
		$this->display();
	}

	// 添加菜单
	public function addMenu(){
		if(IS_POST){
			$post = I('post.');
			$count = M('wechatMenu')-> where(array('pid'=>$post['pid']))->count();
			if($post['pid'] == 0){				
				if($count>=3){
					$this->error('一级菜单最多添加三个', U('wechat/addMenu'), 1);
				}
			}else{
				if($count>=5){
					$this->error('二级菜单最多添加五个', U('wechat/addMenu'), 1);
				}
			}
			if(M('wechatMenu')->add($post)){
				$this->success('菜单添加成功', U('wechat/menu'));
			}else{
				$this->error('菜单添加失败', U('wechat/menu'));
			}
		}else{
			$this->menu = M('wechatMenu')-> where(array('pid'=>0))->select();
			$this->display();
		}
	}


	// 编辑菜单
	public function editMenu(){
		$menu = M('wechatMenu');
		if(IS_POST){
			$post = I('post.');
			if($menu->save($post)){
				$this->success('编辑成功', U('wechat/menu'));
			}else{
				$this->error('编辑失败', U('wechat/menu'));
			}
		}else{
			$id = I('id', 0);
			$data = $menu->find($id);
			$this->menu = M('wechatMenu')-> where(array('pid'=>0))->select();
			$this->assign('data', $data);
			$this->display();
		}
	}

	// 删除菜单
	public function delMenu(){
		$id = I('id', 0);
		if(M('wechatMenu')->delete($id)){
			$this->success('删除成功', U('wechat/menu'));
		}else{
			$this->error('删除失败', U('wechat/menu'));
		}
	}

	// 生成菜单
	public function upMenu(){
		$menu = M('wechatMenu')->select();
		$menu = menuForLayer($menu, 'sub_button');
		$menu = urldecode(json_encode(array('button'=>$menu)));
	    $access_token = getAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
		$httplib = new \Common\Util\Httplib();
        $httplib->set_useragent('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)');
        $httplib->set_header('X-Requested-With','XMLHttpRequest');
        $httplib->request($url, $menu);
        if ($httplib->get_statcode() == 200) {
           	$res = json_decode($httplib->get_data(), true);
           	if($res['errcode'] == 0){
           		$this->success('菜单设置成功', U('wechat/menu'));
           	}else{
           		$this->error('菜单设置失败', U('wechat/menu'));
           	}
        }
	}

}
?>