<?php
namespace Admin\Controller;
class ArticleController extends CommonController
{
    public function anli()
    {
        $count = M('Anli')->count();
        $Page=new \Think\Page($count,10);
       // p($Page);
        $show=$Page->show();
    	$list=M('Anli')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        $this->page=$show;
    	$this->list=$list;
    	$this->display();
    }
    public function anliInfo()
    {
    	if(IS_GET){
    		if(isset($_GET['id'])){
	    		$cid=I('id');
	    		$data=M('Anli')->find($cid);
	    		$data['pub_time']=date('Y-m-d',$data['pub_time']);
	    		//p($data);
	    		$this->list=$data;
	    		$this->action='编辑';
	    		$this->display();
    		}else{
	    		$this->action='添加';
	    		$this->display();   	
    		}	
    	}
    	if(IS_POST){
    		if(I('post.id')){
    			$data=M('Anli')->create();
    			$data['pub_time']=strtotime($data['pub_time']);
    			if(M('Anli')->save($data)!==false){
					$this->success('保存成功',U('Article/anli'));
					die;
				}else{
					$this->error('保存失败');
				}
    		}else{
    			$cate=M('Anli');
    			$data=$cate->create();
    			$data['pub_time']=strtotime($data['pub_time']);
    			$data['add_time']=time();
    			if($cate->add($data)){
					$this->success('保存成功',U('Article/anli'));
					die;
    			}else{
					$this->error('保存失败');
    			}
    		}
    	}
    }
    public function anliDel()
    {
    	$id=I('get.id');
    	if(M('Anli')->delete($id)){
    		$this->success('删除成功',U('Article/anli'));
    	}else{
 			$this->error('删除失败');
    	}
    }
}