<?php
namespace Home\Controller;
use \Think\Controller;
class WeixinController extends Controller
{
   public function api()
   {
       require_once VENDOR_PATH.'Weixin/WechatCallbackapiTest.class.php';
       define('TOKEN','babymamid');
       $wechatObj= new \wechatCallbackapiTest();
       //验证消息开启valid，响应情况下注释掉
       $wechatObj->valid();
       //$wechatObj->responseMsg();

   }
    public function token()
    {
        echo token();
    }
    public function setMenu()
    {
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.token();
        $data=' {
     "button":[
     {
          "type":"click",
          "name":"今日歌曲",
          "key":"todayMusic"
      },
      {
           "name":"菜单",
           "sub_button":[
           {
               "type":"view",
               "name":"搜索",
               "url":"http://www.baidu.com/"
            },
            {
               "type":"view",
               "name":"视频",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"查看个人信息",
               "key":"info"
            },
            {
                "type": "scancode_push",
                "name": "扫一扫有惊喜",
                "key": "rselfmenu_0_1",
                "sub_button": [ ]
            }]
       }]
 }';
        $res=curl($url,$data);
        echo checkError($res);
    }
    public function delMenu()
    {
        $res=curl('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.token());
        echo checkError($res);


    }
    public function getUsers()
    {
        $url='https://api.weixin.qq.com/cgi-bin/user/get?access_token='.token();
        p($url);
        $res=json_decode(curl($url),true);
        //p($res);
        $info=array();
        foreach($res['data'] as $v){
            foreach($v as $k=>$vo){
             $uurl='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.token().'&openid='.$vo.'&lang=zh_CN';
                $userinfo=json_decode(curl($uurl),true);
                $info[]=$userinfo;
            }
           //p($v);
        }
        p($info);
    }
    public function uploadNews()
    {
        $url='https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.token();
        $data='{
   "articles": [
		 {
             "thumb_media_id":"OG5MRF0qG2NdqvSz2eDMtDOJ2FhRq98Dwdfe28X2dciTDoBuk015dqnzQ3sDoWCk",
             "author":"Heaven",
			 "title":"Happy Day",
			 "content_source_url":"www.qq.com",
			 "content":"腾讯欢迎您~",
			 "digest":"点击有惊喜哦!",
                        "show_cover_pic":1
		 }]
}';
        $res=json_decode(curl($url,$data));
        echo $res['media_id'];
        //srzqM2FFD056HktxCy-m1B9Fw3Eordxc4E5QVU8pzhrAyORrf0LXLqeYKCkyqdMO


    }
    public function addimg()
    {
        $file_info=array(
            'filename'=>'/Public/Uploads/images/20161008/1475942079876342.jpg',  //国片相对于网站根目录的路径
            'content-type'=>'image/jpg',  //文件类型
            'filelength'=>'11011'         //图文大小
        );
        $res=add_material($file_info);
        echo $res;

    }
    public function sendall()
    {
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.token();
        $data='{
           "filter":{
              "is_to_all":true
           },
           "mpnews":{
              "media_id":"srzqM2FFD056HktxCy-m1B9Fw3Eordxc4E5QVU8pzhrAyORrf0LXLqeYKCkyqdMO"
           },
            "msgtype":"mpnews"
        }';
         p(curl($url,$data));

    }
    public function addthumb()
    {
        $file_info=array(
            'filename'=>'/Public/Uploads/images/20161008/thumb.jpg',  //国片相对于网站根目录的路径
            'content-type'=>'image/jpg',  //文件类型
            'filelength'=>'8'         //图文大小
        );
        $res=add_thumb($file_info);
        echo $res['thumb_media_id'];
        //OG5MRF0qG2NdqvSz2eDMtDOJ2FhRq98Dwdfe28X2dciTDoBuk015dqnzQ3sDoWCk

    }
    public function preview()
    {
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.token();
        $data='{
   "touser":"otPVGwSKWAMX6oxMazEh6l84zZh8",
   "mpnews":{
            "media_id":"srzqM2FFD056HktxCy-m1B9Fw3Eordxc4E5QVU8pzhrAyORrf0LXLqeYKCkyqdMO"
             },
   "msgtype":"mpnews"
}';
        $res=curl($url,$data);
        echo $res;

    }
    //第一步：添加缩略图addthumb获取thumb_media_id,第二步:uploadNews获取news(media_id),第三步preview或者send_all
    public function test()
    {
      /*'Tb12tlYGDl9NvHTeQgCb5nGna2mtTpE8_ZvOskr4OkZHp1_6KSrm_4M1LZI2ONIhc8BJv0zTaQArXAl__fAPjGtgHmSDLlyiSF0HwOpXqRvut5AjSRtIDi5t3jCxxHTLSgADAHZG'*/
    }
}