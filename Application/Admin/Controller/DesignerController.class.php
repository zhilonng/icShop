<?php
namespace Admin\Controller;

use Think\Controller;

class DesignerController extends CommonController
{


    // 设计师列表
    public function index()
    {   
        $designer = M('designer');
        $count = $designer->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $designer
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
            $data['designer_name']=I('designer_name');
            if(M('designer')->add($data)){
                $this->success('添加成功', U('Designer/index'));
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
            $data['designer_id']=I('designer_id');
            $data['designer_name']=I('designer_name');
            if(M('designer')->save($data)!==false){
                $this->success('编辑成功', U('Designer/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $id=$_GET['designer_id'];
            $list=M('Designer')->find($id);
            $this->list=$list;
            $this->action='编辑';
            $this->display('add');
        }

    }


    // 删除用户
    public function del()
    {
        $id = I('designer_id');
        if(M('designer')->delete($id)){
            $this->success('删除成功', U('Designer/index'));
        }else{
            $this->error('删除失败');
        }
    }

}
