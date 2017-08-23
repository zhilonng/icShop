<?php
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
                  ob_clean();
        	echo $echoStr;
        	exit;
        }
    }

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
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();

                if($postObj->MsgType=='event'){
                    if($postObj->EventKey=='todayMusic'){
                        die("<xml>
                            <ToUserName><![CDATA[$fromUsername]]></ToUserName>
                            <FromUserName><![CDATA[$toUsername]]></FromUserName>
                            <CreateTime>$time</CreateTime>
                            <MsgType><![CDATA[music]]></MsgType>
                            <Music>
                            <Title><![CDATA[今日音乐]]></Title>
                            <Description><![CDATA[每日推荐]]></Description>
                            <MusicUrl><![CDATA[http://sc1.111ttt.com/2016/1/12/10/205101300290.mp3]]></MusicUrl>
                            <HQMusicUrl><![CDATA[http://sc1.111ttt.com/2016/1/12/10/205101300290.mp3]]></HQMusicUrl>
                            </Music>
                            </xml>");
                    }else if($postObj->EventKey=='info'){
                        $openid=$fromUsername;
                        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".token()."&openid=$openid";
                        $res=curl($url);
                        $res=json_decode($res,true);
                        die("<xml>
                        <ToUserName><![CDATA[$fromUsername]]></ToUserName>
                        <FromUserName><![CDATA[$toUsername]]></FromUserName>
                        <CreateTime>$time</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[您的微信名为:  {$res['nickname']}   来自{$res['province']}省{$res['city']}市]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>");
                    }else if($postObj->Event=='subscribe'){
                        die("<xml>
                    <ToUserName><![CDATA[$fromUsername]]></ToUserName>
                    <FromUserName><![CDATA[$toUsername]]></FromUserName>
                    <CreateTime>$time</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[欢迎您关注Heaven,点击菜单或者直接发送文本、语音等有惊喜.如有任何意见请留言哦~]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>");

                    }else if($postObj->Event=='unsubscribe'){
                          //此项为取消关注，后面应该操作如从数据库删除用户信息等
                        //发送消息没作用
                    }
                    //如果接收为文本消息则回复以下内容
                }else if($postObj->MsgType=='text'){

                        die("<xml>
                        <ToUserName><![CDATA[$fromUsername]]></ToUserName>
                        <FromUserName><![CDATA[$toUsername]]></FromUserName>
                        <CreateTime>$time</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[你说{$keyword}这句话的时候帅爆了！]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>");

                }else if($postObj->MsgType=='voice'){
                    $res=$postObj->Recognition;
                    if(strpos($res,'时间')!== false){
                           $content='现在是北京时间'.date('Y年m月d日H点i分');
                        die("<xml>
                       <ToUserName><![CDATA[$fromUsername]]></ToUserName>
							 <FromUserName><![CDATA[$toUsername]]></FromUserName>
							 <CreateTime>$time</CreateTime>
							 <MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[$content]]></Content>
							<FuncFlag>0</FuncFlag>
                        </xml>");
                    }else{
                        die("<xml>
                       <ToUserName><![CDATA[$fromUsername]]></ToUserName>
							 <FromUserName><![CDATA[$toUsername]]></FromUserName>
							 <CreateTime>$time</CreateTime>
							 <MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[您想说的是:{$res}吗?]]></Content>
							<FuncFlag>0</FuncFlag>
                        </xml>");
                    }

                }


                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
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