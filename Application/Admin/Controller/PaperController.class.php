<?php
namespace Admin\Controller;

use Think\Controller;

class PaperController extends CommonController
{


    // 纸样师列表
    public function index()
    {   
        $Paper = M('Paper');
        $count = $Paper->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Paper
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
            $data['paper_name']=I('paper_name');
            if(M('paper')->add($data)){
                $this->success('添加成功', U('Paper/index'));
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
            $data['paper_id']=I('paper_id');
            $data['paper_name']=I('paper_name');
            if(M('paper')->save($data)!==false){
                $this->success('编辑成功', U('Paper/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $id=$_GET['paper_id'];
            $list=M('paper')->find($id);
            $this->list=$list;
            $this->action='编辑';
            $this->display('add');
        }

    }


    // 删除用户
    public function del()
    {
        $id = I('paper_id');
        if(M('paper')->delete($id)){
            $this->success('删除成功', U('Paper/index'));
        }else{
            $this->error('删除失败');
        }
    }

}
