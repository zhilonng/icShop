<?php
namespace Admin\Controller;
use Think\Controller;
class ProductController extends Controller{
    public function index()
    {
        $count = M('Product')->count();
        $Page=new \Think\Page($count,10);
       // p($Page);
        $show=$Page->show();
    	$list=D('Product')
        ->relation(true)
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        $this->page=$show;
    	$this->list=$list;
    	$this->display();
    }
    public function info()
    {
    	if(IS_GET){
    		if(isset($_GET['id'])){
	    		$cid=I('id');
	    		$data=M('Product')->find($cid);
                //p($data);
	    		$this->cate=getTree(M('Category')->select());
	    		$this->list=$data;
	    		$this->action='编辑';
	    		$this->display('info');
    		}else{
	    		$this->cate=getTree(M('Category')->select());
	    		$this->action='添加';
	    		$this->display();   	
    		}	
    	}
    	if(IS_POST){
    		if(I('post.id')){
    			$data=M('Product')->create();
    			if(M('Product')->save($data)!==false){
					$this->success('保存成功',U('Product/index'));
					die;
				}else{
					$this->error('保存失败');
				}
    		}else{
    			$cate=M('Product');
    			$data=$cate->create();
                //p($data);
    			if($cate->add($data)){
					$this->success('保存成功',U('Product/index'));
					die;
    			}else{
					$this->error('保存失败');
    			}
    		}
    	}
    }
    public function del()
    {
    	$id=I('get.id');
    	if(M('Product')->delete($id)){
    		$this->success('删除成功',U('Product/index'));
    	}else{
 			$this->error('删除失败');
    	}
    }
    public function uploadImg(){  
  
        $upload = new \Think\Upload();// 实例化上传类  
        $upload->maxSize   =     3145728 ;// 设置附件上传大小  
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型  
        $upload->rootPath = '/Applications/MAMP/htdocs/icShop/Public/Uploads/images/';
         //$upload->rootPath = '/home/wwwroot/order/Public/Uploads/images/';
        $upload->saveName =date('YmdHis').mt_rand(1000,9999);
        // 上传文件   
        $info   =   $upload->uploadOne($_FILES['weixin_image']);  
        if(!$info) {// 上传错误提示错误信息  
            //$this->error($upload->getError());  
            echo $upload->getError();  
        }else{// 上传成功 获取上传文件信息  
            //处理照片
            $image = new \Think\Image(); 
           // $image->open('/home/wwwroot/order/Public/Uploads/images/'.$info['savepath'].$info['savename']);
            $image->open('/Applications/MAMP/htdocs/icShop/Public/Uploads/images/'.$info['savepath'].$info['savename']);

            // 按照原图的比例生成一个最大为400*400的缩略图
             // $image->thumb(400, 400)->save('/home/wwwroot/order/Public/Uploads/Admin/'.$info['savepath'].$info['savename']);
            $image->thumb(400, 400)->save('/Applications/MAMP/htdocs/icShop/Public/Uploads/images/'.$info['savepath'].$info['savename']);

            echo "/Public/Uploads/images/".$info['savepath'].$info['savename'];  
        }  
    }
    // public function delimg()
    // {
    //     $path=I('path');
    //     if(unlink("/home/wwwroot/order$path")){
    //         echo 1;
    //     }else{
    //         echo 0;
    //     }
    // } 
        public function delimg()
    {
        $path=I('path');
        if(unlink("/Applications/MAMP/htdocs/icShop{$path}")){
            echo 1;
        }else{
            echo 0;
        }
    } 


}