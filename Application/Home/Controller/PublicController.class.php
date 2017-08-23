<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller
{
    /**
     * 生成验证码
     */
    public function code()
    {
        $verify=new \Think\Verify();
        $verify->length=4;
        $verify->entry();

    }
}