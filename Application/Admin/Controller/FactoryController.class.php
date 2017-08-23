<?php
namespace Admin\Controller;

use Think\Controller;

class FactoryController extends CommonController
{


    // 工厂列表
    public function index()
    {   
        $factory = M('factory');
        $count = $factory->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $factory
        ->where($where)
        ->limit($Page->firstRow . ',' . $Page->listRows)
        ->select();
        $this->assign('list', $list);
        //p($list);
        $this->assign('page', $show);
        $this->display();
    }

    public function add()
    {
        if(IS_POST){
            $data['factory_name']=I('factory_name');
            if(M('factory')->add($data)){
                $this->success('添加成功', U('Factory/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $this->action='添加';
            $this->display();
        }

    }

    public function edit()
    {
        if(IS_POST){
            $data['factory_id']=I('factory_id');
            $data['factory_name']=I('factory_name');
            if(M('factory')->save($data)!==false){
                $this->success('编辑成功', U('Factory/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $id=$_GET['factory_id'];
            $list=M('factory')->find($id);
            $this->list=$list;
            $this->action='编辑';
            $this->display('add');
        }

    }


    // 删除用户
    public function del()
    {
        $id = I('factory_id');
        if(M('factory')->delete($id)){
            $this->success('删除成功', U('Factory/index'));
        }else{
            $this->error('删除失败');
        }
    }

}
