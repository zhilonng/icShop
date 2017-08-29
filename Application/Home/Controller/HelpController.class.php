<?php
namespace Home\Controller;
class HelpController extends CommonController
{
    public function about()
    {
        $this->display();
    }
    public function connect()
    {
    	$this->list=M('Connect')->find();
        $this->display();
    }
    public function form()
    {
    	//p($_POST['data']);
    	$post=$_POST['data'];
    	$data=array();
    	foreach ($post as $k => $v) {
    		$data[$v['name']]=$v['value'];
    	}
    	$data['time']=time();
    	if(M('Liuyan')->add($data)){
    		echo 1;
    	}else{
    		echo 0;
    	}
    }
}