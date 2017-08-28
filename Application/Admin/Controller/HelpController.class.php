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
}