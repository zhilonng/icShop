<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extends Controller{
    public function index()
    {
    	$list=getTree(M('Category')->select());
    	//p($list);
    	$this->list=$list;
    	$this->display();
    }
    public function info()
    {
    	if(IS_GET){
    		if(isset($_GET['cid'])){
	    		$cid=I('cid');
	    		$data=M('Category')->find($cid);
	    		$this->cate=getTree(M('Category')->select());
	    		$this->list=$data;
	    		$this->action='编辑';
	    		$this->display('info');
    		}else{
	    		$this->cate=getTree(M('Category')->select());
	    		$this->action='添加';
	    		$this->display();   	
    		}	
    	}
    	if(IS_POST){
    		if(I('post.category_id')){
    			$data=M('Category')->create();
    			if(M('Category')->save($data)!==false){
					$this->success('保存成功',U('Category/index'));
					die;
				}else{
					$this->error('保存失败');
				}
    		}else{
    			$cate=M('Category');
    			$data=$cate->create();
    			if($cate->add($data)){
					$this->success('保存成功',U('Category/index'));
					die;
    			}else{
					$this->error('保存失败');
    			}
    		}
    	}
    }
    public function del()
    {
    	$id=I('get.cid');
    	if(M('Category')->delete($id)){
    		$this->success('删除成功',U('Category/index'));
    	}else{
 			$this->error('删除失败');
    	}
    }
}