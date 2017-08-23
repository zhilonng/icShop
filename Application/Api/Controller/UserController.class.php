<?php
namespace Api\Controller;
use Think\Controller;
class UserController extends Controller 
{
	  public function editBabyInfo()
	  {

	     $data=$_POST;
	    if($data['baby_sex']=='true'){
	      $data['baby_sex']='0';
	    }else if($data['baby_sex']=='false'){
	      $data['baby_sex']='1';
	    }
	    $data['baby_birthday']=strtotime($data['baby_birthday']);
	    if(M('Baby')->save($data)!==false){
	      echo 'ok';
	    }else{
	      echo M('Baby')->getDbError();
	    }
	  }
	  public function addBabyInfo()
	  {
	    $data=$_POST;
	    if($data['baby_sex']=='true'){
	      $data['baby_sex']='0';
	    }else if($data['baby_sex']=='false'){
	      $data['baby_sex']='1';
	    }
	    $data['baby_birthday']=strtotime($data['baby_birthday']);
	    if($baby_id=M('Baby')->add($data)){
	      echo $baby_id;
	    }else{
	      echo 'error';
	    }
	  }
	  public function editUser()
	  {
	      $data['user_name']=I('post.user_name');
	      if(I('post.user_sex')==true){
	      	$data['user_sex']=0;
	      }else if(I('post.user_sex')==false){
	      	$data['user_sex']=1;
	      }
	      if(I('post.user_avatar')){
	      	$data['user_avatar']=I('post.user_avatar');
	      }
	      $data['user_birthday']=strtotime(I('post.user_birthday'));
	      if(M('User')->where("user_id=$_POST[user_id]")->save($data)!==false){
	      	echo 'ok';
	      }else{
	      	echo 'error';
	      }
	  }
	  public function delBaby($baby_id)
	  {
	  	if(M('Baby')->where("baby_id=$baby_id")->delete()){
	  		echo 'ok';
	  	}else{
	  		echo 'error';
	  	}
	  }
	  public function editBackground($user_id,$path)
	  {
	  	$data['user_background']=$path;
	  	if(M('User')->where("user_id=$user_id")->save($data)!==false){
	  		echo 'ok';
	  	}else{
	  		echo 'error';
	  	}
	  }
	  public function FocusNews($news_user_id,$news_focus_uid)
	  {
	  	 $ndata['news_user_id']=$news_user_id;
	  	 $ndata['news_parise_id']=0;
	  	 $ndata['news_comment_id']=0;
	  	 $ndata['news_focus_uid']=$news_focus_uid;
	  	 if(M('News')->add($ndata)){
	  	 	echo 'ok';
	  	 }else{
	  	 	echo 'error';
	  	 }
	  }
}