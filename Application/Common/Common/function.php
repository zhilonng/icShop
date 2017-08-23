<?php
function p($var='')
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    die;
}
function get_thumb($org,$spec='sma')
{
    $arr=pathinfo($org);
    return $arr['dirname'].'/'.$arr['filename'].'_'.$spec.'.'.'jpg';
}
// function getTree($list,$pid=0,$level=0)
// {
//     static $arr=array();
//     if(is_null($list)){
//         $arr=array();
//         return $arr;
//     }
//     foreach($list as $v){
//         if($v['pid']==$pid){
//             $v['level']=$level;
//             $arr[]=$v;
//             getTree($list,$v['id'],$level+1);
//         }
//     }
//     return $arr;
// }
function get_parents($list,$id)
{
    static $tree=array();
    foreach($list as $row){
        if($row['id']==$list[$id]['pid']){
            array_unshift($tree,$row);
            get_parents($list,$row['id']);
        }
    }
    return $tree;
}
//防止XSS攻击
function removeXss($val)
{
    static $obj=null;
    if($obj===null){
        require VENDOR_PATH . 'HTMLPurifier/HTMLPurifier.includes.php';
        $obj  = new HTMLPurifier();
    }
    return $obj->purify($val);
}
function upload()
{
    // 文件域的下标（字段名）
    $key = key($_FILES);

    // 如果没有上传文件
    if(! $_FILES[$key]['size'])
    {
        return array();
    }
    $upload           = new \Think\Upload();
    $upload->maxSize  = 3145728;
    $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
    $upload->rootPath = './';
    $upload->savePath = ltrim($_POST['_rootpath'], '/') . '/';
    $info             = $upload->upload();
    $f=$info[$key]['savepath'].$info[$key]['savename'];
    //p($key);
    //生成上传文件的字段和对应的文件夹目录名字
    $_POST[$key]      = date('Y-m-d').'/'.$info[$key]['savename'];
    //开始生成800*800的高清图
    $image           = new \Think\Image();
    $image->open($f);
    $image->thumb(C('THUMB.orgw'),C('THUMB.orgh'),2)->save($f);
    $arr=pathinfo($f);
    //循环输出配置文件中的三个尺寸的缩略图
    foreach(C('THUMB.SIZE') as $v){
        $image->open($f);
        $image->thumb(C('THUMB.'.$v .'w'),C('THUMB.'.$v.'h'),2)->save($arr['dirname'].'/'.$arr['filename'].'_'
            .$v.'.'.'jpg');
    }
    if(!$info)
    {
        // 上传错误提示错误信息
        return $upload->getError();
    }else
    {
        return $info;
    }
}
//短信API
function sendTemplateSMS($to,$datas,$tempId)
{
    include_once(VENDOR_PATH . 'SMS/CCPRestSmsSDK.php');
    // 初始化REST SDK
    # global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
    extract( C('SMS') );

    $rest = new REST($serverIP,$serverPort,$softVersion);
    $rest->setAccount($accountSid,$accountToken);
    $rest->setAppId($appId);

    // 发送模板短信
    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
    if($result == NULL ) {
        $error= "result error!";

    }else if($result->statusCode!=0) {
        $error= "error code:$result->statusCode<br>error msg:$result->statusMsg <br>";
        //TODO 添加错误处理逻辑
    }else{
        $error=true ;
        // 获取返回信息
    }
    return $error;
}
//邮箱API
function email($to='',$name='',$title='',$content='')
{
    require VENDOR_PATH.'PHPMailer/class.phpmailer.php';
    // 实例化
    $pm = new \PHPMailer();
    //配置文件取出循环配置
    foreach(C('EMAIL') as $k => $v){
        $pm->$k=$v;
    }
    // 服务器相关信息
    $pm->IsSMTP(); // 设置使用SMTP服务器发送邮件
    $pm->SMTPAuth = true; // 需要SMTP身份认证
    if(C('EMAIL.Host')=='smtp.qq.com'){
        //QQ邮箱开启以下两项
        $pm->SMTPSecure = 'ssl';
        $pm->Port = 465;
    }
    // 收件人信息
    $pm->AddAddress($to,$name); // 添加一个收件人
    // 邮件内容
    $pm->CharSet = 'utf-8'; // 内容编码
    $pm->Subject = $title; // 邮件标题
    $pm->MsgHTML($content); // 邮件内容
    // $this->AddAttachment($path); // 附件
    // 发送邮件
    if($pm->Send()){
        echo 'ok';
    }else {
        echo $pm->ErrorInfo;
    } 
}
 //5.5以下的array_column函数使用
    function i_array_column($input, $columnKey, $indexKey=null){
        if(!function_exists('array_column')){ 
            $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
            $indexKeyIsNull            = (is_null($indexKey))?true :false; 
            $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
            $result                         = array(); 
            foreach((array)$input as $key=>$row){ 
                if($columnKeyIsNumber){ 
                    $tmp= array_slice($row, $columnKey, 1); 
                    $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
                }else{ 
                    $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
                } 
                if(!$indexKeyIsNull){ 
                    if($indexKeyIsNumber){ 
                      $key = array_slice($row, $indexKey, 1); 
                      $key = (is_array($key) && !empty($key))?current($key):null; 
                      $key = is_null($key)?0:$key; 
                    }else{ 
                      $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                    } 
                } 
                $result[$key] = $tmp; 
            } 
            return $result; 
        }else{
            return array_column($input, $columnKey, $indexKey);
        }
    }
function token($refresh=false)
{
    if($refresh ||  ! $res= S(C('WEIXIN.token'))){
         $res=file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.C('MAMIWEIXIN.appID').'&secret='.C('MAMIWEIXIN.appsecret'));
         $res=json_decode($res,true);
         S(C('WEIXIN.token'),$res=$res['access_token'],7100);
    }
    return $res; 


}

function curl($url, $data = '')
{
    // 初始化一个cURL会话
    $ch = curl_init($url);
    // 设置一个cURL传输选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回结果而不是直接输出
    // 对于SSL的链接请求不要求验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch,CURLOPT_HTTPHEADER,"Content-Type: application/json");

    if($data)
    {
        if(is_array($data))
        {
            $data = http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_POST, true); // 启用post请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // 设置要post的数据
    }

    // 执行一个cURL会话
    $res = curl_exec($ch);
    // 关闭一个cURL会话
    curl_close($ch);

    return $res;
}
function checkError($result)
{
    static $i =0;
    $res=json_decode($result,true);
    if(! $res['errcode']){
        return 'ok';
    }else{
        if(in_array($res['errcode'],array(40001,42001)) && (++$i<=3) ){
            token();
            $this->setMenu();
            return true;
        }
        return '错误信息为:'.$res['errmsg'];
    }
}
//上传图文消息内的图片获取URL
function add_material($file_info){
    /*$file_info=array(
        'filename'=>'/images/1.png',  //片相对于网站根目录的路径
        'content-type'=>'image/png',  //文件类型
        'filelength'=>'11011'         //图文大小
    );*/
    $url='https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.token().'&type=image';
    $ch1 = curl_init ($url);
    $timeout = 5;
    $real_path="{$_SERVER['DOCUMENT_ROOT']}{$file_info['filename']}";
    //$real_path=str_replace("/", "\\", $real_path);
    $data= array("media"=>"@{$real_path}",'form-data'=>$file_info);
    curl_setopt ( $ch1, CURLOPT_URL, $url );
    curl_setopt ( $ch1, CURLOPT_POST, 1 );
    curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch1 );
    //var_dump($result);die;
    if(curl_errno($ch1)==0){
        $result=json_decode($result,true);
        //var_dump($result);die;
        return $result['media_id'];
    }else {
         $result=json_decode($result,true);
        return $result;
    }
}

function add_thumb($file_info){
    /*$file_info=array(
        'filename'=>'/images/1.png',  //片相对于网站根目录的路径
        'content-type'=>'image/png',  //文件类型
        'filelength'=>'11011'         //图文大小
    );*/
    $url='http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.token().'&type=thumb';
    $ch1 = curl_init ($url);
    $timeout = 5;
    $real_path="{$_SERVER['DOCUMENT_ROOT']}{$file_info['filename']}";
    //$real_path=str_replace("/", "\\", $real_path);
    $data= array("media"=>"@{$real_path}",'form-data'=>$file_info);
    curl_setopt ( $ch1, CURLOPT_URL, $url );
    curl_setopt ( $ch1, CURLOPT_POST, 1 );
    curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch1 );
    //var_dump($result);die;
    if(curl_errno($ch1)==0){
        $result=json_decode($result,true);
        //var_dump($result);die;
        return $result;
    }else {
        $result=json_decode($result,true);
        return $result;
    }
}
//获取七牛token()
/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/** 
 * 发送post请求 
 * @param string $url 请求地址 
 * @param array $post_data post键值对数据 
 * @return string 
 */  
function json_post($url, $post_data) {  
  $postdata = http_build_query($post_data);  
  $options = array(  
    'http' => array(  
      'method' => 'POST',  
      'header' => 'Content-type:application/json',  
      'content' => $postdata,  
      'timeout' => 15 * 60 // 超时时间（单位:s）  
    )  
  );  
  $context = stream_context_create($options);  
  $result = file_get_contents($url, false, $context);  
  return $result;  
} 

//敏感词调换
function strtr_array(&$str,&$replace_arr) {
    $maxlen = 0;$minlen = 1024*128;
    if (empty($replace_arr)) return $str;
    foreach($replace_arr as $k => $v) {
        $len = strlen($k);
        if ($len < 1) continue;
        if ($len > $maxlen) $maxlen = $len;
        if ($len < $minlen) $minlen = $len;
    }
    $len = strlen($str);
    $pos = 0;$result = '';
    while ($pos < $len) {
        if ($pos + $maxlen > $len) $maxlen = $len - $pos; 
        $found = false;$key = '';
        for($i = 0;$i<$maxlen;++$i) $key .= $str[$i+$pos]; //原文：memcpy(key,str+$pos,$maxlen)
        for($i = $maxlen;$i >= $minlen;--$i) {
            $key1 = substr($key, 0, $i); //原文：key[$i] = '\0'
            if (isset($replace_arr[$key1])) {
                $result .= $replace_arr[$key1];
                $pos += $i;
                $found = true;
                break;
            }
        }
        if(!$found) $result .= $str[$pos++];
    }
    return $result;
}

 /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    function request_post($url = '', $param = '') {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($param)
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        return  $result;
    }



