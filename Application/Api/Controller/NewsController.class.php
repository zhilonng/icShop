<?php
namespace Api\Controller;
use Think\Controller;
class NewsController extends Controller 
{
  public function getNews($user_id)
  {
       $res=M('News')->where("news_user_id=$user_id")->order('id desc')select();
       p($res);
     
  }
  


}