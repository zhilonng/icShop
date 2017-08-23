<?php
namespace Admin\Controller;
use Think\Controller;
class RbacController extends CommonController{
	
	// 管理员列表
	public function index(){
		$admin = D('admin');
		$list = $admin->relation(true)->select();
		$this->assign('list', $list);
		$this->display();
	}


	// 添加管理员
	public function addUser(){
		if(IS_POST){
			$data = array(
				'username' => I('username'),
				'password' => md5(I('password')),
				'lock' => I('lock', 0)
				);
			$_result = M('admin')->where(array('username'=>$data['username']))->find();
			if($_result) $this->error('该用户名已存在', U('rbac/addUser'));
			if($user_id = M('admin')->add($data)){
				$data = array(
					'role_id' => I('rid'),
					'user_id' => $user_id
					);
				M('role_user')->add($data);
				$this->success('管理员添加成功', U('rbac/addUser'));
			}else{
				$this->error('管理员添加失败', U('rbac/addUser'));
			}
		}else{
			$this->role = M('role')->select();
			$this->display();
		}
	}


	// 编辑管理员
	public function editUser(){
		if(IS_POST){
			$data = array(
				'admin_id' => I('admin_id'),
				'lock' => I('lock', 0)
				);
			if($_POST['password']) $data['password'] = md5($_POST['password']);
			M('admin')->save($data);
			M('role_user')->where('user_id='.$data['admin_id']);
			$data = array(
				'role_id' => I('rid'),
				'user_id' => $data['admin_id']
				);
            M('role_user')->where(array('user_id'=>$data['user_id']))->delete();
			M('role_user')->add($data);
			$this->success('管理员编辑成功', U('rbac/index'));	
		}else{
			$id = I('id', 0);
			$_result = D('admin')->relation(true)->find($id);
			$this->role = M('role')->select();
			$this->assign('data', $_result);
			$this->display();
		}
	}


	// 管理员激活、冻结
	public function lock(){
		$id = I('id', 0);
		$lock = I('lock', 0);
		$data = array(
			'admin_id' => $id,
			'lock' => $lock
			);
		if(M('admin')->save($data)){
			$this->success('操作成功', U('rbac/index'));
		}else{
			$this->error('操作失败', U('rbac/index'));
		}
	}



	// 删除用户
	public function delUser(){
		$id = I('id', 0);
		$_result = M('admin')->find($id);
		if($_result){
			if(M('admin')->delete($id)){
				$this->success('管理员删除成功', U('rbac/index'));
			}else{
				$this->error('管理员删除失败', U('rbac/index'));
			}
		}else{
			$this->error('指定用户不存在', U('rbac/index'));
		}
	}


	// 修改密码
	public function savePassword(){
		if(IS_POST){
			$oldpwd = I('oldpwd');
			$password = I('password');
			if(empty($oldpwd)) $this->error('请输入旧密码', U('rbac/savePassword'));
			if(empty($password)) $this->error('请输入新密码', U('rbac/savePassword'));
			$admin = M('admin');
			$_result = $admin->find($_SESSION[C('USER_AUTH_KEY')]);
			if(md5($oldpwd) != $_result['password']){
				$this->error('旧密码错误', U('rbac/savePassword'));
			}else{
				$data = array(
					'admin_id' => $_SESSION[C('USER_AUTH_KEY')],
					'password' => md5($password)
					);
				if($admin->save($data)){
					session(null);
					$this->success('密码修改成功', U('index/index'));
				}else{
					$this->error('密码修改失败', U('rbac/savePassword'));
				}
			}
		}else{
			$this->display();
		}
	}


	// 角色列表
	public function role(){
		$role = M('role')->select();
		$this->assign('role', $role);
		$this->display();
	}


	// 添加角色
	public function addRole(){
		if(IS_POST){
			$post= I('post.');
			if(M('role')->add($post)){
				$this->success('添加成功', U('rbac/addRole'));
			}else{
				$this->error('添加失败请稍后再试');
			}
		}else{
			$this->display();
		}
		
	}

	// 编辑角色
	public function editRole(){
		if(IS_POST){
			$data = array(
				'id' => $_POST['id'],
				'name' => $_POST['name'],
				'remark' => $_POST['remark'],
				'status' => $_POST['status']
				);
			if(M('role')->save($data)){
				$this->success('角色修改成功', U('rbac/role'));
			}else{
				$this->error('角色修改失败', U('rbac/role'));
			}
		}else{	
			$rid = I('rid', 0);
			$_result = M('role')->find($rid);
			if($_result){
				$this->assign('data', $_result);
				$this->display();
			}else{
				$this->error('未找到指定的角色', U('rbac/role'));
			}			
		}
	}

	// 删除角色
	public function delRole(){
		$rid = I('rid', 0);
		$_result = M('role')->find($rid);
		if($_result){
			if(M('role')->delete($rid)){
				// 删除用户表和角色的中间表
				M('role_user')->where('role_id='.$rid)->delete();

				// 删除权限表
				M('access')->where('role_id='.$rid)->delete();

				$this->success('角色删除成功', U('rbac/role'));
			}else{
				$this->error('角色删除失败', U('rbac/role'));
			}
		}else{
			$this->error('未找到指定的角色', U('rbac/role'));
		}
	}


	// 节点列表
	public function node(){
		$node = M('node')->field('id,name,title,pid')->select();
		$this->node = node_merge($node);
		$this->display();
	}


	// 添加节点
	public function addNode(){
		if(IS_POST){
			$post = I('post.');
			if(M('node')->add($post)){
				$this->success('添加成功', U('rbac/addNode', array('pid'=>$post['pid'],'level'=>$post['level'])));
			}else{
				$this->error('添加失败', U('rbac/addNode', array('pid'=>$post['pid'],'level'=>$post['level'])));
			}
		}else{
			$this->pid = I('pid', 0);
			$this->level = I('level', 1);
			switch ($this->level) {
				case 1:
					$this->type = '应用';
					break;
				case 2:
					$this->type = '控制器';
					break;
				case 3:
					$this->type = '动作方法';
					break;
			}
			$this->display();
		}
	}
	

	// 修改节点
	public function editNode(){
		$node = M('node');
		if(IS_POST){
			$post = I('post.');
			if(M('node')->save($post)){
				$this->success('修改成功', U('rbac/node'));
			}else{
				$this->error('修改失败', U('rbac/node'));
			}
		}else{
			$id = I('id');
			$data = $node->find($id);
			switch ($data['level']) {
				case 1:
					$this->type = '应用';
					break;
				case 2:
					$this->type = '控制器';
					break;
				case 3:
					$this->type = '动作方法';
					break;
			}
			$this->assign('data', $data);
			$this->display();
		}
	}

	// 删除节点
	public function delNode(){
		$id = I('id', 0);
		$_result = M('node')->find($id);
		if($_result){
			if(M('node')->delete($id)){
				$this->success('节点删除成功', U('rbac/node'));
			}else{
				$this->error('节点删除失败', U('rbac/node'));
			}
		}else{
			$this->error('节点不存在', U('rbac/node'));
		}
	}


	// 配置权限
	public function access(){
		$rid = I('rid', 0);
		$node = M('node')->field('id,name,title,pid')->select();
		$access = M('access')->where(array('role_id'=>$rid))->getField('node_id', true);
		$this->node = node_merge($node,$access);
		// p($this->node);
		$this->assign('rid', $rid);
		$this->display();
	}

	// 修改权限
	public function setAccess(){
		$rid = I('rid', 0);
		$access = M('access');
		$access->where(array('role_id' => $rid))->delete();
		$data = array();
		foreach ($_POST['access'] as $value) {
			$tmp = explode('_', $value);
			$data[] = array(
				'role_id' => $rid,
				'node_id' => $tmp[0],
				'level' => $tmp[1]
				);
		}
		if($access->addAll($data)){
			$this->success('修改成功', U('rbac/role'));
		}else{
			$this->error('修改失败', U('rbac/role'));
		}
	}

	public function editPassword()
	{	
		if(IS_POST){
			//p($_POST);
			$data['password']=I('password');
			$data['id']=1;
			if(M('password')->save($data)!==false){
				$this->success('修改成功', U('rbac/editPassword'));
			}else{
				$this->error('修改失败');
			}
		}else{
			$data=M('password')->where("type='delete'")->find();
			$this->data=$data;
			$this->display();
		}
	}

}

?>