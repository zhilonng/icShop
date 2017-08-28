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
}