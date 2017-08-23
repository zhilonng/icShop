<?php
namespace Home\Controller;
use \Think\Controller;
class CommonController extends Controller
{
    //吧cdata设置为一个属性可以方便调用
    public $cdata=array();

    public function _initialize()
    {
        // //获取商品分类
        // $this->cdata=M('Category')->select(array('index'=>'id'));
        // $this->assign('cdata',$this->cdata);
        // //获取族谱
        // $children=array();
        // foreach($this->cdata as $v){
        //     $children[$v['pid']][]=$v['id'];
        // }
        // //p($children);
        // $this->children=$children;
        // $nav=array();
        // $nav=array_filter($this->cdata,function($nav){
        //     return $nav['show_in_nav']==1 ? true : false;
        // });
        // $this->nav=$nav;
        // //PHP 5.4以下不能cookie('USER')['username']格式
        // $userinfos=cookie('USER');
        // $username=$userinfos['username'];
        // $this->username=$username;
        // //p($children);




    }
}