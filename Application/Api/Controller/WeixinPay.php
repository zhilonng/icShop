<?php

/**
 * 微信支付企业付款接口
 */
class wxPay{
    //=======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径
    protected $SSLCERT_PATH = './apiclient_cert.pem';//自己修改一下路径
    protected $SSLKEY_PATH =  './apiclient_key.pem';//自己修改一下路径
    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
    //protected $APPID = 'wx6b075400ce000a59 ';//填写您的appid。微信公众平台里的
    //受理商ID，身份标识
    //protected $MCHID = '1412623802';//商户id
    //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    //protected $KEY = '192006250b4c09247ec02edce69f6a2d'; 
    //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	protected $appid;
	protected $mch_id;
	protected $openid;

	protected $goods_id;
	protected $goods_name;
	protected $quantity;
	protected $price;
	function __construct($appid,$openid,$mch_id,$key,$goods_id,$goods_name,$quantity,$price){
		$this->appid=$appid;
		$this->openid=$openid;
		$this->mch_id=$mch_id;
		$this->goods_id=$goods_id;
		$this->goods_name=$goods_name;
		$this->quantity=$quantity;
		$this->price=$price;
	} 
	public function pay(){
		//统一下单接口
		$return=$this->weixinapp();
		return $return;
	}
	//统一下单接口
	private function unifiedorder(){
		$url='https://api.mch.weixin.qq.com/pay/unifiedorder';
		$str=array();
		$str['goods_id']=$this->goods_id;
		$str['goods_name']=$this->goods_name;
		$str['quantity']=$this->quantity;
		$str['price']=$this->price;
		// $str=json_encode($str,JSON_UNESCAPED_UNICODE);
		// p($str);
		$parameters=array(
			'appid'=>$this->appid,//小程序ID
			'mch_id'=>$this->mch_id,//商户号
			'nonce_str'=>$this->createNoncestr(),//随机字符串
			'body'=>'测试',//商品描述
			'out_trade_no'=>date('YmdHis').mt_rand(1000,9999),//商户订单号
			'total_fee'=>floatval($this->price*100),//总金额 单位 分
			'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],//终端IP
			'notify_url'=>'http://www.weixin.qq.com/wxpay/pay.php',//通知地址
			'openid'=>$this->openid,//用户id
			'detail'=>json_encode($str,JSON_UNESCAPED_UNICODE),
			'trade_type'=>'JSAPI'//交易类型
		);
		//统一下单签名
		$parameters['sign']=$this->getSign($parameters);
		$xmlData=$this->arrayToXml($parameters);
		$return=$this->xmlToArray($this->postXmlSSLCurl($xmlData,$url,60));
		return $return;
	}
	//微信小程序接口
	private function weixinapp(){
		//统一下单接口
		$unifiedorder=$this->unifiedorder();
		$parameters=array(
			'appId'=>$this->appid,//小程序ID
			'timeStamp'=>''.time().'',//时间戳
			'nonceStr'=>$this->createNoncestr(),//随机串
			'package'=>'prepay_id='.$unifiedorder['prepay_id'],//数据包
			'signType'=>'MD5'//签名方式
		);
		//签名
		$parameters['paySign']=$this->getSign($parameters);
		return $parameters;
	}

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) 
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     *  作用：生成签名
     */
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=MAmiyanjiuyuanxiaochengxu0909api";
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  
        return $str;
    }

    /**
     *  作用：array转xml
     */
    public function arrayToXml($arr)
    {
        $xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes' ?><xml>";
        foreach ($arr as $key=>$val)
        {
        	if($key=='detail'){
                //$val=json_encode($val,JSON_UNESCAPED_UNICODE);
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        	}else{
                $xml.="<".$key.">".$val."</".$key.">"; 
             }
        }
        $xml.="</xml>"; 
        return $xml; 
    }

    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml)
    {       
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);      
        return $array_data;
    }

    /**
     *  作用：使用证书，以post方式提交xml到对应的接口url
     */
    public function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else { 
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>"; 
            curl_close($ch);
            return false;
        }
    }
}