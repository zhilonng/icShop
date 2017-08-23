<?php
namespace Caculate\Controller;

use Think\Controller;

class PayController extends Controller
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
		$key='appid='.$appid.'&body=test&device_info=1000&mch_id='.$mch_id;	
		require_once 'WeixinPay.php';
		$weixinpay = new \wxPay($appid,$openid,$mch_id,$key,$goods_id,$goods_name,$quantity,$price);
		$return=$weixinpay->pay();
        print_r(json_encode($return,JSON_UNESCAPED_UNICODE));
	}
	public function notify()
	{
		$data=$_REQUEST;
		if($id=M('notify')->add($data)){
			echo '<xml>
				  <return_code><![CDATA[SUCCESS]]></return_code>
				  <return_msg><![CDATA[OK]]></return_msg>
				</xml>'
		}else{
			echo 'error';
		}
	}
}