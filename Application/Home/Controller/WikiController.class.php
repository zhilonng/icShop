<?php
namespace Home\Controller;
use Think\Controller;
 // 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;
class WikiController extends Controller
{
    public function getArticle()
    {
        $res=M('Article')->order('id desc')->select();
        foreach($res as &$v){
            $v['add_day']=date('d',$v['add_time']);
           $v['add_monthyear']=date('Y.m',$v['add_time']);
           $v['add_time']=date('Y年m月d日',$v['add_time']);
           $v['imgpath']='https://oj76c7lts.qnssl.com/'.$v['imgpath'];
        }
        print_r(json_encode($res)) ;

    }
    public function ArticleDetail($id=0)
    {
    	$res=M('Article')->where("id='$id'")->find();
    	$res['add_time']=date('Y年m月d日',$res['add_time']);
        $res['add_day']=date('d',$res['add_time']);
        $res['add_monthyear']=date('Y.m',$res['add_time']);
    	print_r(json_encode($res)) ;
    }
    public function addText()
    {
    	if($data=M('Article')->create()){
            $data['imgpath']='https://oj76c7lts.qnssl.com/'.$data['imgpath'];
    		$data['add_time']=time();
    		if(!$data){
    			return 0;die;
    		}
    		if(M('Article')->add($data)){
    			echo 'ok';
    		}else{
    			echo 0;
    		}
    	}else{
    		echo 0;
    	}
    	print_r(json_encode($res));
    }
    public function token()
    {
        require VENDOR_PATH.'qiniu/autoload.php';
	    

	    // 需要填写你的 Access Key 和 Secret Key
	    $accessKey = 'jDw8-_n5yrXgvMOGHWwFuqyxYy3HUI5iTdUTvJOm';
	    $secretKey = 'Qt0rYR3ULXvfrWSqhz1TOOHlPHqJ4Yzol1PAn7OB';

	    // 构建鉴权对象
	    $auth = new Auth($accessKey, $secretKey);

	    // 要上传的空间
	    $bucket = 'Bucket_Name';

	    // 生成上传 Token
	    $token = $auth->uploadToken($bucket);

	}
	public function uploads($filepath,$filename)
	{
		require VENDOR_PATH.'qiniu/autoload.php';
        

        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = 'jDw8-_n5yrXgvMOGHWwFuqyxYy3HUI5iTdUTvJOm';
        $secretKey = 'Qt0rYR3ULXvfrWSqhz1TOOHlPHqJ4Yzol1PAn7OB';

        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);

        // 要上传的空间
        $bucket = 'momlabtest';

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath = $filepath;

        // 上传到七牛后保存的文件名
        $key = date('YmdHis').mt_rand(10000,99999).'.'.substr(strrchr($filePath, '.'), 1);

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
        p($_FILES);
        
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
    public function saveVideo()
    {

        $file = $_FILES['file'];
        $filename=$file['name'];
        $filepath= $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$_FILES['file']['name'];
        if(move_uploaded_file($file['tmp_name'], $filepath)){
                 if($this->uploads($filepath,$filename)){
                       echo 'ok';
                 }else{
                    echo 'error';
                 }
                 
        }else{
            echo 'error';
        }

    }
}