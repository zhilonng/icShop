<?php
namespace Admin\Controller;
class HelpController extends CommonController
{
    public function info()
    {
    	if(IS_POST){
    			$data=M('Connect')->create();
    			if(M('Connect')->save($data)!==false){
					$this->success('保存成功',U('Help/info'));
				}else{
					$this->error('保存失败');
				}
    	}else{
            $list=M('Connect')->find();
            $this->list=$list;
            $this->display();
        }
    }
    public function liuyan()
    {
        $count = M('Liuyan')->count();
        $Page=new \Think\Page($count,20);
       // p($Page);
        $show=$Page->show();
        $list=M('Liuyan')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        $this->page=$show;
        $this->list=$list;
        $this->display();
    }
    public function del()
    {
        $id=I('get.id');
        if(M('Liuyan')->delete($id)){
            $this->success('删除成功',U('Help/liuyan'));
        }else{
            $this->error('删除失败');
        }
    }
}