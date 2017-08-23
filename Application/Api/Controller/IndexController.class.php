<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
  //加载首页取出用户对应的数据
    public function index($user_id){
       $res=array();
       if($count=M('News')->where("news_user_id='$user_id' AND news_is_read=0")->count('news_id')){
         $res['momres']['newsCount']=$count;
       }else{
       	 $res['momres']['newsCount']=0;
       }
       $momres=M('User')->field('user_id,user_name,user_avatar,user_sex,user_birthday,user_background')->where("user_id='$user_id'")->find();
       $momres['user_birthday']=date('Y-m-d',$momres['user_birthday']);
       $momres['user_background']='https://oj76c7lts.qnssl.com/'.$momres['user_background'];
       $momres['newsCount']=$count;
       $momres['Time']=date('Y年m月d日');
       $momres['format_time']=date('Y-m-d');
       $users_id=$momres['user_id'];
       $babyres=M('Baby')->where("baby_mom_id='$users_id'")->select();
       if(!$babyres){
       	   $momres['baby_mom_id']=$user_id;
       }
       foreach($babyres as &$v){
        if($v['baby_avatar']!=''){
           $v['baby_avatar']='https://oj76c7lts.qnssl.com/'.$v['baby_avatar'];
         }else{
          $v['baby_avatar']='https://oj76c7lts.qnssl.com/baby_default_image.png';
         } 
          $v['baby_birthday_']=date('Y-m-d',$v['baby_birthday']);
          $v['baby_birthday']=date('Y-m-d',$v['baby_birthday']);
       }
      
       $res['momres']=$momres;
       $res['babyres']=$babyres;
       return $res;
    }

      public function getNewsCount($user_id)
      {
      	$count=M('News')->where("news_user_id='$user_id' AND news_is_read=0")->count('news_id');
      	echo $count;
      }
      public function getVisitorInfo($user_id,$my_id){
       $res=array();
       $momres=M('User')->field('user_id,user_name,user_avatar,user_sex,user_birthday,user_background')->where("user_id='$user_id'")->find();
       $momres['user_birthday']=date('Y-m-d',$momres['user_birthday']);
       $momres['user_background']='https://oj76c7lts.qnssl.com/'.$momres['user_background'];
       if(!M('Friend')->where("friend_mom_id=$user_id AND friend_userid=$my_id")->find()){
       	    $momres['relation']=false;
       }else{
            $momres['relation']=true;
       }
       $users_id=$momres['user_id'];
       $babyres=M('Baby')->where("baby_mom_id='$users_id'")->select();
       if(!$babyres){
           $momres['baby_mom_id']=$user_id;
       }
       foreach($babyres as &$v){
        if($v['baby_avatar']!=''){
           $v['baby_avatar']='https://oj76c7lts.qnssl.com/'.$v['baby_avatar'];
         }else{
          $v['baby_avatar']='https://oj76c7lts.qnssl.com/baby_default_image.png';
         } 
          $v['baby_birthday_']=date('Y-m-d',$v['baby_birthday']);
          $v['baby_birthday']=date('Y-m-d',$v['baby_birthday']);
       }
      
       $res['momres']=$momres;
       $res['babyres']=$babyres;
       print_r(json_encode($res)); 
    }

    //根据用户登录获取openID并将openid存入user表如果已经存在用户信息就直接返回
    public function login()
    {
    	$code=$_POST['code'];
      //我们宝贝小程序
    	$appid='wxaa949be24c459de7';
    	$secret='5e15dabefc874af7fd1bcf98bdc8599c';
      $url='https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
      $data = curl_exec($curl);
      $data=json_decode($data,true);
      // //获取unionid
      // require_once "wxBizDataCrypt.php";
      // $sessionKey = $data['session_key'];
      // $encryptedData=$_POST['encryptedData'];
      // $iv = $_POST['iv'];
      // //p($_POST);
      // $pc = new \WXBizDataCrypt($appid, $sessionKey);
      // $errCode = $pc->decryptData($encryptedData, $iv, $datas );
      // if ($errCode == 0) {
      //     $datas=json_decode($datas,true);
      //     $unionid=$datas['unionId'];
      // } else {
      //     print($errCode . "\n");die;
      // } 
      // $mami=M('User','b_','MAMI');
      // $where['unionid']=$unionid;
      // if($list=$mami->where($where)->find()){
      //   p($list);
      // }else{
      //   p('no');
      // }
      // p($unionid);
		  $openid=$data['openid'];
		  curl_close($curl);
		  $user=M('User');
		  if(!$user->where("user_openid='$openid'")->find()){
			$data=array();
		    $data['user_openid']=$openid;
		    $data['user_name']=$_POST['username'];
		    $data['user_avatar']=$_POST['useravatar'];
        $data['user_sex']=$_POST['usersex'];
        $data['user_addtime']=time();
            if(!$add_user_id=$user->add($data)){
                   $this->error("系统错误原因为:".$user->getDberror());
            }else{
              $data['record_userid']=$add_user_id;
              $data['record_content']='欢迎来到我们宝贝!在这里,你可以发布照片、视频和日记,记录孩子的成长,与亲友一起分享。需要设置你和宝宝的信息,请点击顶部的背景图片。你也可以点击详情,删除这条记录。';
              $data['record_time']=time();
              M('Record')->add($data);
            }
		}
		$res=M('User')->field('user_id')->where("user_openid='$openid'")->find();
		$user_id=$res['user_id'];
		$result=$this->index($user_id);
		print_r(json_encode($result));	
    }
    //取出所有的日志及内容 回复 点赞

     public function getArticle($record_userid,$loadpage)
    {
        $count=$loadpage*10;
        $res=M('Record')
        ->where("record_userid=$record_userid")
        ->order('record_time desc')
        ->limit("$count,10")
        ->select();
        if(!$res){
          die('error');
        }
        //根据用户ID查出所有用户的日志进行循环
        foreach($res as &$v){
            $parise_record_id=$v['record_id'];
            //先查出点日志里点赞对应用户的信息
            $parise_userid=M('Parise')->field('parise_userid')->order('parise_time')->where("parise_record=$parise_record_id")->select((array('index'=>'parise_userid')));
            //如果没有点赞人则返回空
            if(!$parise_userid){
              $v['isShowPariseArea']=false;
              $v['parise_user_id']=null;
	          $v['user_avatar']=array();
            }else{
               $v['isShowPariseArea']=true;
            }
            //取出每一篇日志中点赞人的ID再循环查出点赞人的对应信息
            $v['parise_user_id']= $parise_userid;
             foreach($v['parise_user_id'] as $k=>$vo){
                    $v['user_id'][]=$k;
             }

            $v['parise_user_id']=implode(',', $v['user_id']);
            foreach($v['user_id'] as &$pui){
	            $user_res=M('User')->field('user_avatar')->where("user_id=$pui")->find();
	                $v['user_avatar'][]= $user_res['user_avatar'];  
            }
            
            //将用户头像信息加到数组里面在视图中循环取出
          
           $comment=M('Comment')->where("record_id=$parise_record_id")->limit(10)->order('comment_id asc')->select();
           //循环日志里的评论
            foreach($comment as &$io){
              if($io['comment_replyid']!=0){
                $io['isReply']=true;
                $urep_id=$io['comment_replyid'];
                $repUser=M('User')->field('user_name')->where("user_id=$urep_id")->find();
                 $io['reply_user_name']=$repUser['user_name'];
              }else{
                $io['isReply']=false;
                $io['reply_user_name']='';
              }
               $uid=$io['user_id'];
               $comUser=M('User')->field('user_name,user_avatar')->where("user_id=$uid")->find();
               $io['user_name']=$comUser['user_name'];
               $io['user_avatar']=$comUser['user_avatar'];
            }
           $v['comment']=$comment;
           $v['add_day']=date('d',$v['record_time']);
           $v['add_monthyear']=date('Y.m',$v['record_time']);
           $v['add_time']=date('Y年m月d日',$v['record_time']);
           if($v['record_photo']){
               $v['record_photo']=explode(',',$v['record_photo']);
                   foreach($v['record_photo'] as &$rp){
                      $rp='https://oj76c7lts.qnssl.com/'.$rp;
               }
             }else{
             	 $v['record_photo']=array();
             }
           if($v['record_video']){
           	    $v['video_name']=$v['record_video'];
            	$v['record_video']='https://oj76c7lts.qnssl.com/'.$v['record_video'];
           }
           if($v['record_tags']){
            $v['record_tags']=explode(',',$v['record_tags']);
           }else{
            $v['record_tags']=null;
           }    
        }

       print_r(json_encode($res)) ;

    }
    //点击日志详情页所需要组装的数据
    public function ArticleDetail($id=0)
    {
    	$res=M('Record')->where("record_id='$id'")->find();
      if($res['record_photo']){
          $res['record_photo']=explode(',', $res['record_photo']);
          foreach($res['record_photo'] as &$rp){
                  $rp='https://oj76c7lts.qnssl.com/'.$rp;
          }
      }
      if($res['record_video']){
            	$res['record_video']='https://oj76c7lts.qnssl.com/'.$res['record_video'];
           }
      if($res['record_tags']){
            $res['record_tags']=explode(',',$res['record_tags']);
           }else{
            $res['record_tags']=null;
           } 
    	$res['record_time']=date('Y年m月d日',$res['record_time']);
      $res['record_day']=date('d',$res['record_time']);
      $res['record_monthyear']=date('Y.m',$res['record_time']);
      $comment=M('Comment')->where("record_id=$id")->select();
          foreach($comment as &$io){
             if($io['comment_replyid']!=0){
                $io['isReply']=true;
                $urep_id=$io['comment_replyid'];
                $repUser=M('User')->field('user_name')->where("user_id=$urep_id")->find();
                 $io['reply_user_name']=$repUser['user_name'];
              }else{
                $io['isReply']=false;
                $io['reply_user_name']='';
              } 
             $uid=$io['user_id'];
             $comUser=M('User')->field('user_name,user_avatar')->where("user_id=$uid")->find();
             $io['user_name']=$comUser['user_name'];
             $io['user_avatar']=$comUser['user_avatar'];
          }
      $res['comment']=$comment;
      $parise_userid=M('Parise')->field('parise_userid')->where("parise_record=$id")->select((array('index'=>'parise_userid')));
            if(!$parise_userid){
              $res['isShowPariseArea']=false;
              $res['parise_user_id']=null;
              $res['user_avatar']=array();
            }else{
               $res['isShowPariseArea']=true;
            }
            //取出每一篇日志中点赞人的ID再循环查出点赞人的对应信息
            $res['parise_user_id']= $parise_userid;
             foreach($res['parise_user_id'] as $k=>$vo){
                    $res['user_id'][]=$k;
             }
            $res['parise_user_id']=implode(',', $res['user_id']);
            foreach($res['user_id'] as &$pui){
	            $user_res=M('User')->field('user_avatar')->where("user_id=$pui")->find();
	             if(!$user_res){
	                 $res['user_avatar']=array();
	            }else{
	                $res['user_avatar'][]= $user_res['user_avatar'];  
	            }  
            }
    	print_r(json_encode($res)) ;
    }
    //添加点赞或者再次点击取消
    public function addParise($record_id,$user_id)
    {
      $where=("parise_record=$record_id AND parise_userid=$user_id");
      if(!M('Parise')->where($where)->find())
      {
          $data['parise_record']=$record_id;
          $data['parise_userid']=$user_id;
          $data['parise_time']=time();
          if($parise_id=M('Parise')->add($data)){
            $rres=M('Record')->field('record_userid')->where("record_id=$record_id")->find();
            $new_user_id=$rres['record_userid']; 
            $ndata['news_parise_id']=$parise_id;
            $ndata['news_user_id']=$new_user_id;
            if($ndata['news_user_id']!=$user_id){
               if(M('News')->add($ndata)){
                    echo 'ok';
               }
            }else if($ndata['news_user_id']==$user_id){
            	echo 'ok';
            }  
          }
          //再次点击取消
      }else{
          $res=M('Parise')->field('parise_id')->where($where)->find();
          $par_id=$res['parise_id'];
          if(M('Parise')->where($where)->delete()){
            $rres=M('Record')->field('record_userid')->where("record_id=$record_id")->find();
            $new_user_id=$rres['record_userid']; 
            if(M('News')->where("news_parise_id=$par_id AND news_user_id=$new_user_id")->delete()){
               echo 'delete';
            }
          }
      }
    }
    //添加评论 
    public function addComment()
    {
      if(IS_POST){
      $data=$_POST;
      $user_id=I('user_id');
      $data['user_id']=I('user_id');
      $data['record_id']=I('record_id');
      $data['comment_content']=I('comment_content');
      $data['comment_time']=time();
        if($data['comment_replyid']!=0){
          $data['comment_content']=substr($data['comment_content'],strrpos($data['comment_content'],':')+1);

        }
        if($comid=M('Comment')->add($data)){
            $comres=M('Comment')->field('record_id')->where("comment_id=$comid")->find();
            $record_id=$comres['record_id'];
            $rres=M('Record')->field('record_userid')->where("record_id=$record_id")->find();
            $new_user_id=$rres['record_userid']; 
            $ndata['news_comment_id']=$comid;
            $ndata['news_user_id']=$new_user_id;
            if($ndata['news_user_id']!=$user_id ){
            	if(M('News')->add($ndata)){
                      $res['id']=$comid;
                      $res['comment_content']=$data['comment_content'];
                   print_r(json_encode($res)) ;
                }   
            }else if($ndata['news_user_id']==$user_id){
            	if($data['comment_replyid']!=0){
            		$ndata['news_comment_id']=$comid;
                    $ndata['news_user_id']=$data['comment_replyid'];
                    if(M('News')->add($ndata)){
                      $res['id']=$comid;
                      $res['comment_content']=$data['comment_content'];
                   print_r(json_encode($res)) ;
                    }   
            	}else{
            		      $res['id']=$comid;
                      $res['comment_content']=$data['comment_content'];
                   print_r(json_encode($res)) ;
            	}
            	
            }
             
        }else{
          echo 'error';
        }
      }
     }
     public function getUserNews($user_id)
     {
     	$res=M('News')
      ->field('news_id,news_parise_id,news_comment_id,news_focus_uid')
      ->where("news_user_id='$user_id' AND news_is_read=0")
      ->order('news_id desc')
      ->select();
     	foreach($res as$k=>&$v){
     		if($v['news_comment_id']==0 && $v['news_parise_id']==0){
     			$focus_uid=$v['news_focus_uid'];
     			$usinfo=M('User')->where("user_id=$focus_uid")->find();
     			$v['user_name']=$usinfo['user_name'];
     			$v['user_avatar']=$usinfo['user_avatar'];
     			$v['user_id']=$usinfo['user_id'];
                $v['comment_content']=$usinfo['user_name'].'关注了您';
                $v['record_id']=0;
     		}
     		if($v['news_comment_id']!=0){
     			$comres=M('Comment')->field('user_id,record_id,comment_content,comment_time')->where("comment_id=$v[news_comment_id]")->find();
     			$v['action_time']=date('Y年m月d日 H:i',$comres['comment_time']);
     			$comresuser_id=$comres['user_id'];
     			$uinfo=M('User')->field('user_name,user_avatar')->where("user_id='$comresuser_id'")->find();
                $v['user_name']=$uinfo['user_name'];
                $v['user_avatar']=$uinfo['user_avatar'];
                $recid=$comres['record_id'];
                $recres=M('record')->field('record_id,record_content,record_photo,record_video')->where("record_id='$recid'")->find();
                $v['record']=$recres;
                $v['record_id']=$recres['record_id'];
                if($recres['record_photo']){
                	if(strpos($recres['record_photo'],',')!==false){
                		$v['record_content_p']='https://oj76c7lts.qnssl.com/'.substr($recres['record_photo'],0,strpos($recres['record_photo'],','));
                	}else{
                		$v['record_content_p']='https://oj76c7lts.qnssl.com/'.$recres['record_photo'];
                	}
                	
                }else{
                	$v['record_content']=mb_substr($recres['record_content'],0,10).'...';
                }
                $v['comment_content']=$comres['comment_content'];
     			$res[$k][]=$comres;
     		}else{
     			unset($v['news_comment_id']);
     		}
     		if($v['news_parise_id']!=0){
     			$parres=M('Parise')->where("parise_id=$v[news_parise_id]")->find();
     			$v['action_time']=date('Y年m月d日 H:i',$parres['parise_time']);
     			$usinfo=M('User')->field('user_name,user_avatar')->where("user_id='$parres[parise_userid]'")->find();
                $v['user_name']=$usinfo['user_name'];
                $v['user_avatar']=$usinfo['user_avatar'];
                $recid=$parres['parise_record'];
                $recres=M('record')->field('record_id,record_content,record_photo,record_video')->where("record_id='$recid'")->find();
                $v['record']=$recres;
                $v['record_id']=$recres['record_id'];
                 if($recres['record_photo']){
                	if(strpos($recres['record_photo'],',')!==false){
                		$v['record_content_p']='https://oj76c7lts.qnssl.com/'.substr($recres['record_photo'],0,strpos($recres['record_photo'],','));
                	}else{
                		$v['record_content_p']='https://oj76c7lts.qnssl.com/'.$recres['record_photo'];
                	}
                	
                }else{
                	$v['record_content']=mb_substr($recres['record_content'],0,10).'...';
                }
                $v['comment_content']="";
     			$res[$k][]=$parres;
     		}else{
     			unset($v['news_parise_id']);
     		}

     	}
     	print_r(json_encode($res));


     }
     public function test()
     {

       /*echo $_SERVER['DOCUMENT_ROOT'];*/
      $name = '18jshy1bz17o8ixq0inqh3erk9'.'.jpg'; 
      $from = 'https://oj76c7lts.qnssl.com/18jshy1bz17o8ixq0inqh3erk9';
      $to = $_SERVER['DOCUMENT_ROOT'].'/uploads/';  
      $str = "ffmpeg -i ".$from." -y -f mjpeg -ss 3 -t 1 -s 740x500 ".$to.$name;  
      exec($str);
      
     }
     public function newsReaded()
     {
     	if(IS_POST){
     		$newss_id=I('newsId');
	     	$data['news_is_read']=1;
	     	if(M('News')->where(array('news_id'=>array('in',"$newss_id")))->save($data)){
	     		echo 'ok';
     	    }else{
     	    	echo 'error';
     	    }
     	}	
     }
     public function searchTags($user_id,$tag)
     {
     		$res=M('Record')
     		->where(array("record_userid"=>"$user_id","record_tags"=>array('like',"%{$tag}%")))
     		->order('record_id desc')
     		->select();
            foreach($res as &$v){
            $parise_record_id=$v['record_id'];
            //先查出点日志里点赞对应用户的信息
            $parise_userid=M('Parise')->field('parise_userid')->where("parise_record=$parise_record_id")->select((array('index'=>'parise_userid')));
            //如果没有点赞人则返回空
            if(!$parise_userid){
              $v['isShowPariseArea']=false;
              $v['parise_user_id']=null;
            }else{
               $v['isShowPariseArea']=true;
            }
            //取出每一篇日志中点赞人的ID再循环查出点赞人的对应信息
            $v['parise_user_id']= $parise_userid;
             foreach($v['parise_user_id'] as $k=>$vo){
                    $v['user_id'][]=$k;
             }
            $v['parise_user_id']=implode(',', $v['user_id']);
            $user_res=M('User')->field('user_avatar')->where(array('user_id'=>array('in',"$v[parise_user_id]")))->select();
             if(!$user_res){
              $v['user_avatar']=array();
            }
            $v['user_avatar']=array();
            $user_res=array_reverse($user_res);
            //将用户头像信息加到数组里面在视图中循环取出
            foreach($user_res as $j=>$jo){
                    array_push($v['user_avatar'], $jo['user_avatar']);
             }
           $comment=M('Comment')->where("record_id=$parise_record_id")->limit(10)->order('comment_id asc')->select();
           //循环日志里的评论
            foreach($comment as &$io){
              if($io['comment_replyid']!=0){
                $io['isReply']=true;
                $urep_id=$io['comment_replyid'];
                $repUser=M('User')->field('user_name')->where("user_id=$urep_id")->find();
                 $io['reply_user_name']=$repUser['user_name'];
              }else{
                $io['isReply']=false;
                $io['reply_user_name']='';
              }
               $uid=$io['user_id'];
               $comUser=M('User')->field('user_name,user_avatar')->where("user_id=$uid")->find();
               $io['user_name']=$comUser['user_name'];
               $io['user_avatar']=$comUser['user_avatar'];
            }
           $v['comment']=$comment;
           $v['add_day']=date('d',$v['record_time']);
           $v['add_monthyear']=date('Y.m',$v['record_time']);
           $v['add_time']=date('Y年m月d日',$v['record_time']);
           if($v['record_photo']){
               $v['record_photo']=explode(',',$v['record_photo']);
                   foreach($v['record_photo'] as &$rp){
                      $rp='https://oj76c7lts.qnssl.com/'.$rp;
               }
             }else{
             	 $v['record_photo']=array();
             }
           if($v['record_video']){
           	    $v['video_name']=$v['record_video'];
            	$v['record_video']='https://oj76c7lts.qnssl.com/'.$v['record_video'];
           }
           if($v['record_tags']){
            $v['record_tags']=explode(',',$v['record_tags']);
           }else{
            $v['record_tags']=null;
           }    
        }
        print_r(json_encode($res));	
     }
     public function delComment($comment_id)
     {
         if(M('Comment')->where("comment_id=$comment_id")->delete()){
         	echo 'ok';
         }else{
         	echo 'error';
         }
     }
     public function delRecord($record_id)
     {
     	if(M('Record')->where("record_id=$record_id")->delete()){
     		echo 'ok';
     	}else{
     		echo 'error';
     	}
     }

    public function paylogin()
    {
      $code=$_POST['code'];
      //我们宝贝小程序
      // $appid='wxaa949be24c459de7';
      // $secret='5e15dabefc874af7fd1bcf98bdc8599c';
      $appid='wx6b075400ce000a59';
      $secret='6c5339093276dd0d6cefffa7b4be10b8';
      $url='https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
      $data = curl_exec($curl);
      $data=json_decode($data,true);
      curl_close($curl);
      //获取unionid
      require_once "wxBizDataCrypt.php";
      $sessionKey = $data['session_key'];
      $encryptedData=$_POST['encryptedData'];
      $iv = $_POST['iv'];
      //p($_POST);
      $pc = new \WXBizDataCrypt($appid, $sessionKey);
      $errCode = $pc->decryptData($encryptedData, $iv, $datas );
      if ($errCode == 0) {
          $datas=json_decode($datas,true);
          //p($datas);
          $unionid=$datas['unionId'];
      } else {
          print($errCode . "\n");die;
      } 
      $mami=M('User','b_','MAMI');
      $where['unionid']=$unionid;
      if($list=$mami->where($where)->find()){
          $result['realname']=$list['realname'];
          $result['mobile']=$list['mobile'];
          $result['uid']=$list['id'];
          print_r(json_encode($result)); 
      }else{
        $udata['nickname']=$datas['nickName'];
        $udata['openid']='wiApp';
        $udata['province']=$datas['province'];
        $udata['add_time']=time();
        $udata['unionid']=$unionid;
        $udata['lastlogin']=time();
        $udata['status']=0;
        $udata['headimgurl']=$_POST['useravatar'];
        $udata['mobile']='';
        $udata['realname']='';
        $udata['subscribe']=1;
        $udata['report_time']=0;
        $udata['family_time']=0;
        if($id=$mami->add($udata)){
          $result['realname']='';
          $result['mobile']='';
          $result['uid']=$id;
          $result['new']=1;
          print_r(json_encode($result)); 
        }
      }  
    }
      

}