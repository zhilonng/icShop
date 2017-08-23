<?php
namespace Home\Controller;
use Think\Controller;
class SmsController extends CommonController
{
    public function demo()
    {
        //phpinfo();
        sendTemplateSMS('13202091525',array('1234','3'),'1');
    }
}