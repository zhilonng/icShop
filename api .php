<?php
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//测试信息
//$wechatObj->valid();
$wechatObj->responseMsg();


class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
//开启响应消息
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"] ? :file_get_contents('php://input');

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
              	//来自哪个用户
                $fromUsername = $postObj->FromUserName;
                //发给哪个用户
                $toUsername = $postObj->ToUserName;
                //用户发来的信息
                $keyword = trim($postObj->Content);
                //接收到的类型
                $msgType=$postObj->MsgType;
                //用户发来的时间
                $time = time();
               /* if($msgType=='text'){

                    die("<xml>
							<ToUserName><![CDATA[$fromUsername]]></ToUserName>
							<FromUserName><![CDATA[$toUsername]]></FromUserName>
							<CreateTime>$time</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[您发送的内容是:$keyword]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>");
                }else */if($msgType=='image'){
                	die("<xml>
							 <ToUserName><![CDATA[$fromUsername]]></ToUserName>
							 <FromUserName><![CDATA[$toUsername]]></FromUserName>
							 <CreateTime>$time</CreateTime>
							 <MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[图片的网址是:$postObj->PicUrl]]></Content>
							<FuncFlag>0</FuncFlag>
							 </xml>");
                }else if($msgType=='voice'){
                	$res=$postObj->Recognition;
                	die("<xml>
							 <ToUserName><![CDATA[$fromUsername]]></ToUserName>
							 <FromUserName><![CDATA[$toUsername]]></FromUserName>
							 <CreateTime>$time</CreateTime>
							 <MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[您想说的是:$res]]></Content>
							<FuncFlag>0</FuncFlag>
							 </xml>");
                }die();

                
                $textTpl = "<xml>
							<ToUserName><![CDATA[$fromUsername]]></ToUserName>
							<FromUserName><![CDATA[$toUsername]]></FromUserName>
							<CreateTime>$time</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[您发送的内容时:$keyword]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>