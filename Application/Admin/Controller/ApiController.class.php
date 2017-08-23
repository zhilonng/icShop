<?php
namespace Admin\Controller;
class ApiController extends AuthController{
    public function validator()
    {
        $this->display();
    }
    public function index()
    {
        $count=M('GoodsType')->count();
        $page=new \Think\Page($count,3);
        $pagelist=$page->show();

        $lists=M('GoodsType')->limit($page->firstRow.','.$page->listRows)->select();
        //dump($lists);die;
        $this->assign('pagelist',$pagelist);
        $this->assign('lists',$lists);
        $this->display();
    }
}