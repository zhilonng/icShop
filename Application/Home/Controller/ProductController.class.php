<?php
namespace Home\Controller;
class ProductController extends CommonController
{
    public function VOC()
    {
        $data=D('Product')->relation(true)->where(array('cid'=>22,'recommend'=>1))->find();
        $this->data=$data;
        $cate=M('Category')->select(array('index'=>'category_id'));
        $list=array();
        foreach ($cate as $v) {
                $list[$v['category_pid']][]=$v['category_id'];
        }
        //p($list);
        $this->cate=$cate;
        $this->list=$list;
        $this->display();
    }
    public function index()
    {
        $data=M('Product')->select();
        $cate=M('Category')->select(array('index'=>'category_id'));
        $list=array();
        foreach ($cate as $v) {
                $list[$v['category_pid']][]=$v['category_id'];
        }
        //p($list);
        $this->cate=$cate;
        $this->list=$list;
        $this->data=$data;
       // p($data);
    	$this->display();
    }
    public function cate()
    {
        $cid=I('cid');
        $data=M('Product')->where(array('cid'=>$cid))->select();
        $this->name=M('Category')->where(array('category_id'=>$cid))->find();
        $cate=M('Category')->select(array('index'=>'category_id'));
        $list=array();
        foreach ($cate as $v) {
                $list[$v['category_pid']][]=$v['category_id'];
        }
        //p($list);
        $this->cate=$cate;
        $this->list=$list;
        $this->data=$data;
        $this->display();
    }
    public function zengzhi()
    {
        $recom=M('Product')->where(array('recommend'=>1))->select();
        $this->recom=$recom;
        $data=M('Product')->where(array('cid'=>$cid))->select();
        $this->name=M('Category')->where(array('category_id'=>$cid))->find();
        $cate=M('Category')->select(array('index'=>'category_id'));
        $list=array();
        foreach ($cate as $v) {
                $list[$v['category_pid']][]=$v['category_id'];
        }
        //p($list);
        $this->cate=$cate;
        $this->list=$list;
    	$this->display();
    }
    public function detail()
    {
        $id=I('id');
        $data=D('Product')->relation(true)->find($id);
        $this->name=M('Category')->where(array('category_id'=>$cid))->find();
        $cate=M('Category')->select(array('index'=>'category_id'));
        $list=array();
        foreach ($cate as $v) {
                $list[$v['category_pid']][]=$v['category_id'];
        }
        //p($list);
        $this->cate=$cate;
        $this->list=$list;
        $this->data=$data;
        $this->display();
    }
}