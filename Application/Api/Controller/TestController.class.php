<?php
namespace Api\Controller;

use Think\Controller;

class TestController extends Controller
{
	public function payJoinfee()
	{
		//p($_POST);
		$appid='wx6b075400ce000a59';
		$openid=$_POST['openid'];
		$mch_id='1412623802';
		$goods_id=$_POST['goods_id'];
		$goods_name=$_POST['goods_name'];
		$quantity=$_POST['quantity'];
		$price=$_POST['price'];
		require_once 'WeixinPay.php';
		$weixinpay = new \wxPay($appid,$openid,$mch_id,$key,$goods_id,$goods_name,$quantity,$price);
		$return=$weixinpay->pay();
        print_r(json_encode($return,JSON_UNESCAPED_UNICODE));
	}
}