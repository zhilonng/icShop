<?php
namespace Admin\Controller;

use Think\Controller;

class SizeController extends CommonController
{


    //尺码
    public function index()
    {   
        $size = M('size');
        $count = $size->count();
        $list = $size
        ->where($where)
        ->select();
        $list=allGetTree($list);
        $this->assign('list', $list);
        $this->display();
    }

    public function add()
    {
        if(IS_POST){
            if($data=M('size')->create()){
                if(M('size')->add($data)){
                $this->success('添加成功', U('Size/index'));
                }else{
                    $this->error('编辑失败');
                }
            }   
        }else{
            $this->color=M('size')->where("pid=0")->select();
            $this->action='添加';
            $this->display();
        }

    }

    public function edit()
    {
        if(IS_POST){
            $data['id']=I('id');
            $data['pid']=I('pid');
            $data['size_name']=I('size_name');
            if(M('size')->save($data)!==false){
                $this->success('编辑成功', U('Size/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $id=$_GET['size_id'];
            $list=M('size')->find($id);
            $this->list=$list;
            //p($list);
            $this->color=M('size')->where("pid=0")->select();
            $this->action='编辑';
            $this->display('add');
        }

    }


    // 删除
    public function del()
    {
        $id = I('size_id');
        if(M('size')->delete($id)){
            $this->success('删除成功', U('Size/index'));
        }else{
            $this->error('删除失败');
        }
    }

    public function checksize()
    {
        $where['pid']=I('pid');
        if($size=M('size')->where($where)->select()){
            echo "<div class='form-group after'>";
            echo "<label class='col-sm-2 control-label'><em>*</em>已有下属尺码</label>";
            echo "<div class='col-sm-4' style='display: flex;flex-wrap: wrap;'>
    ";
            echo "<select  class='form-control'>";
            foreach($size as $vo){
                echo "<option>".$vo['size_name']."</option>";
            }
            echo "</select>";       
        }else{
            echo 0;
        }
    }

}
