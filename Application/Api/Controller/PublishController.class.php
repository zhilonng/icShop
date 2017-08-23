<?php
namespace Api\Controller;
use Think\Controller;
 // 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;
class PublishController extends Controller
{
   
    public function addText()
    {   
        //file_put_contents('./Application/Api/Controller/addtext.txt', $_POST);die;
    	if(IS_POST){
            require('./Application/Api/Controller/badword.src.php');
            $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
            $bb = I('post.record_content');
            $str = strtr_array($bb, $badword1);
            $data=$_POST;
            $data['record_content']=$str;
            $user_id=$data['record_userid'];
            if($data['ptags'] && $data['etags']){
                $data['record_tags']=$data['ptags'].','.$data['etags'];
            }else if($data['ptags']){
                $data['record_tags']=$data['ptags'];
            }else if($data['etags']){
                $data['record_tags']=$data['etags'];
            }
            if($data['tags']){
                $data['record_tags']=rtrim($data['tags'],",");
                if($oldtag=M('User')->where("user_id=$user_id")->getField('user_tags')){
                     $udata['user_tags']=$oldtag.','.$data['record_tags'];
                }else{
                    $udata['user_tags']=$data['record_tags'];
                }
            }else{
                $oldtag=M('User')->where("user_id=$user_id")->getField('user_tags');
                $udata['user_tags']=$oldtag;
            }
    		$data['record_time']=time();
            if($data['imgpath']){
                $data['record_photo']=$data['imgpath'];
            }
            if($data['videopath']){
                $data['record_video']=$data['videopath'];
            }
            //file_put_contents('./Application/Api/Controller/addtext.txt', $data);die;
            //p($data);
    		if(M('Record')->add($data)){
                if(M('User')->where("user_id=$user_id")->save($udata)){
                    echo 'ok';  
                } 
    		}else{
    			echo 0;
    		}
    	}else{
    		echo 0;
    	}
    }
    public function token()
    {
        require VENDOR_PATH.'qiniu/autoload.php';
	    

	    // 需要填写你的 Access Key 和 Secret Key
	    $accessKey = "l2llWlZGOo0oAjMWuTn3GkluRuIeuz0DlW3gBoAt";
	    $secretKey = "3uQOlFTEpLEtIIsJdGQLrTRIaDwLhbCZy3GFa5AF";

	    // 构建鉴权对象
	    $auth = new Auth($accessKey, $secretKey);

	    // 要上传的空间
	    $bucket = 'momlabtest';

	    // 生成上传 Token
	    $token = $auth->uploadToken($bucket);

        echo $token;

	}

    public function getToken()
    {
        require_once VENDOR_PATH.'qiniu/autoload.php';
       
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = 'jDw8-_n5yrXgvMOGHWwFuqyxYy3HUI5iTdUTvJOm';
        $secretKey = 'Qt0rYR3ULXvfrWSqhz1TOOHlPHqJ4Yzol1PAn7OB';

        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);

        // 要上传的空间
        $bucket = 'momlabtest';

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        S('token',$token,300);
    }
 

	public function uploads($filepath,$filename)
	{
		require_once VENDOR_PATH.'qiniu/autoload.php';
        
        if(S('token')==null){
            $this->getToken();
        }
        $token=S('token');
        // 要上传文件的本地路径
        $filePath = $filepath;

        // 上传到七牛后保存的文件名
        $key =date('YmdHis').mt_rand(10000,99999).'.'.substr(strrchr($filePath, '.'), 1);

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return  $err;
        } else {
            unlink($filepath);
            return $key;
            
        }

	}
        public function save()
    {
        p(S('token')) ;
        
    } 
    public function saves()
    {
        $file = $_FILES['file'];
        $filename=$file['name'];
        $filepath= $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$_FILES['file']['name'];
        if(move_uploaded_file($file['tmp_name'], $filepath)){
                 if($res=$this->uploads($filepath,$filename)){
                      echo $res;
                 }else{
                    echo 'error';
                 }
        }else{
            echo 'error';
        }
    }
    public function test()
    {
        phpinfo();
    }
    public function getTags()
    {
        $user_id=I('post.user_id');
        $tag=M('User')->where("user_id=$user_id")->getField('user_tags');
        echo $tag;
    }
}