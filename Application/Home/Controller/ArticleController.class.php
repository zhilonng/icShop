<?php
namespace Home\Controller;
class ArticleController extends CommonController
{
    public function anLi()
    {
    	$list=M('Anli')->where(array('art_cate'=>1))->select();
    	$this->list=$list;
    	$recom=M('Product')->where(array('recommend'=>1))->select();
    	//p($recom);
    	$this->recom=$recom;
        $this->display();
    }
    public function anliDetail()
    {
    	$id=I('id');
    	$list=M('Anli')->find($id);
    	$recom=M('Product')->where(array('recommend'=>1))->select();
    	$this->recom=$recom;
    	$this->list=$list;
    	$this->display();
    }
    public function news()
    {
    	$list=M('Anli')->where(array('art_cate'=>2))->select();
    	$this->list=$list;
    	$recom=M('Product')->where(array('recommend'=>1))->select();
    	//p($list);
    	$this->recom=$recom;
        $this->display();
    }
    public function newsDetail()
    {
    	$id=I('id');
    	$list=M('Anli')->find($id);
    	$recom=M('Product')->where(array('recommend'=>1))->select();
    	$this->recom=$recom;
    	$this->list=$list;
    	$this->display();
    }

}