<?php
namespace Admin\Controller;

use Think\Controller;

class ColorController extends CommonController
{


    //颜色
    public function index()
    {   
        $color = M('color');
        $count = $color->count();
        $list = $color
        ->where($where)
        ->select();
        $list=allGetTree($list);
        $this->assign('list', $list);
        $this->display();
    }

    public function add()
    {
        if(IS_POST){
            if($data=M('color')->create()){
                if(M('color')->add($data)){
                $this->success('添加成功', U('Color/index'));
                }else{
                    $this->error('编辑失败');
                }
            }   
        }else{
            $this->color=M('color')->where("pid=0")->select();
            $this->action='添加';
            $this->display();
        }

    }

    public function edit()
    {
        if(IS_POST){
            $data['id']=I('id');
            $data['pid']=I('pid');
            $data['color_name']=I('color_name');
            if(M('color')->save($data)!==false){
                $this->success('编辑成功', U('Color/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $id=$_GET['color_id'];
            $list=M('color')->find($id);
            $this->list=$list;
            //p($list);
            $this->color=M('color')->where("pid=0")->select();
            $this->action='编辑';
            $this->display('add');
        }

    }


    // 删除用户
    public function del()
    {
        $id = I('color_id');
        if(M('color')->delete($id)){
            $this->success('删除成功', U('Color/index'));
        }else{
            $this->error('删除失败');
        }
    }


    public function checkcolor()
    {
        $where['pid']=I('pid');
        if($color=M('color')->where($where)->select()){
            echo "<div class='form-group after'>";
            echo "<label class='col-sm-2 control-label'><em>*</em>已有下属颜色</label>";
            echo "<div class='col-sm-4' style='display: flex;flex-wrap: wrap;'>
    ";
            echo "<select  class='form-control'>";
            foreach($color as $vo){
                echo "<option>".$vo['color_name']."</option>";
            }
            echo "</select>";       
        }else{
            echo 0;
        }
    }

}
