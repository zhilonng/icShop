<?php
namespace Admin\Controller;
use Think\Controller;
class AuthController extends Controller{
    public function _initalize()
    {
        if(!session('Admin.id') || session('Admin.username')){
            $this->error('登录超时请重新登录!',U('Public/Login'));
        }
    }
}