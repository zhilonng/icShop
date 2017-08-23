<?php
namespace Admin\Controller;

use Think\Controller;

class LogoController extends CommonController
{


    // 品牌列表
    public function index()
    {   
        $logo = M('logo');
        $count = $logo->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $logo
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
            $data['logo_name']=I('logo_name');
            if(M('logo')->add($data)){
                $this->success('添加成功', U('Logo/index'));
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
            $data['logo_id']=I('logo_id');
            $data['logo_name']=I('logo_name');
            if(M('logo')->save($data)!==false){
                $this->success('编辑成功', U('Logo/index'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $id=$_GET['logo_id'];
            $list=M('logo')->find($id);
            $this->list=$list;
            $this->action='编辑';
            $this->display('add');
        }

    }


    // 删除
    public function del()
    {
        $id = I('logo_id');
        if(M('logo')->delete($id)){
            $this->success('删除成功', U('Logo/index'));
        }else{
            $this->error('删除失败');
        }
    }

}
