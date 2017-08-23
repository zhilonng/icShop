<?php
namespace Api\Controller;

use Think\Controller;

class RelationController extends Controller
{
         public function getRelation($user_id)
      {
           $res=M('Friend')->where("friend_mom_id=$user_id")->order('friend_visit_time desc')->select();
           foreach ($res as &$v){
                $user_id=$v['friend_userid'];
                $uinfo=M('User')->field('user_name,user_avatar')->where("user_id=$user_id")->find();
                $v['user_name']=$uinfo['user_name'];
                $v['user_avatar']=$uinfo['user_avatar'];
                if($v['friend_visit_time']==0){
                     $v['friend_visit_time']='无';
                }else{
                    $v['friend_visit_time']=date('m-d H:i',$v['friend_visit_time']);
                }
                if($v['friend_identity']==0){
                    $v['friend_identity']='亲友';
                }else{
                    $v['friend_identity']='非亲友';
                }
           }
           print_r(json_encode($res));
      }
    public function editRelation()
    {
        $friend_mom_id=I('post.friend_mom_id');
        $friend_userid=I('post.friend_userid');
        $data['friend_identity']=I('post.friend_backup');
        if(M('Friend')->where("friend_mom_id=$friend_mom_id AND friend_userid=$friend_userid")->save($data)!==false){
            echo 'ok';
        }else{
            echo 'error';
        }

    }
    public function getFocus($user_id)
    {
        $result=array();
        $res=M('Friend')->field('friend_mom_id')->where("friend_userid=$user_id")->order('friend_visit_time desc')->select();
        foreach($res as $k=>$rr){
            $friend_id=$rr['friend_mom_id'];
            $momres=M('User')->field('user_name,user_id,user_avatar')->where("user_id='$friend_id'")->find();
            $users_id=$momres['user_id'];
            $babyres=M('Baby')->field('baby_name,baby_avatar')->where("baby_mom_id='$users_id'")->select();
            if(!$babyres){
                $momres['baby_mom_id']=$user_id;
            }
            foreach($babyres as &$v){
             if($v['baby_avatar']!=''){
                $v['baby_avatar']='https://oj76c7lts.qnssl.com/'.$v['baby_avatar'];
              }else{
               $v['baby_avatar']='https://oj76c7lts.qnssl.com/baby_default_img.png';
              } 
            }
            $res[$k]['momres']=$momres;
            $res[$k]['babyres']=$babyres;
        }
        print_r(json_encode($res));
    }  
    //获取朋友的页面

     public function getFriendArticle($record_userid,$loadpage)
    {
         $count=$loadpage*10;
        $res=M('Record')
        ->where("record_userid=$record_userid")
        ->limit("$count,10")
        ->order('record_time desc')
        ->select();
        if(!$res){
          die('error');
        }
        //根据用户ID查出所有用户的日志进行循环
        foreach($res as $rres=>&$v){
            //如果权限不是仅自己可见就循环取出
             if($v['record_indentity']==1){
                unset($res[$rres]);
             } 
            $parise_record_id=$v['record_id'];
                //先查出点日志里点赞对应用户的信息
                $parise_userid=M('Parise')->field('parise_userid')->where("parise_record=$parise_record_id")->select((array('index'=>'parise_userid')));
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
                   if(!$user_res){
                       $v['user_avatar']=array();
                  }else{
                      $v['user_avatar'][]= $user_res['user_avatar'];  
                  }  
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
         print_r(json_encode($res)) ;
    }
    public function visitFriendZone($user_id,$friendid)
    {   
        $res=M('Friend')->where("friend_userid=$user_id AND friend_mom_id=$friendid")->find();
        $data['friend_visit_time']=time();
        $data['friend_visit_number']=$res['friend_visit_number']+1;
        if(M('Friend')->where("friend_userid=$user_id AND friend_mom_id=$friendid")->save($data)!==false){
            echo 'ok';
        }else{
            echo 'error';
        }
    }
    public function OnFocus()
    {
        if(IS_POST)
        {
            $friend_mom_id=I('friend_id');
            $friend_userid=I('user_id');
            if(I('isFocus')=='quxiaofocus'){
                if(M('Friend')->where("friend_mom_id=$friend_mom_id AND friend_userid=$friend_userid")->delete()){
                    echo 'delok';
                }else{
                    echo 'delerror';
                }
            }else if(I('isFocus')=='focus') {
                if(!M('Friend')
                  ->where("friend_mom_id=$friend_mom_id AND friend_userid=$friend_userid")
                  ->find()){
                      $data['friend_mom_id']=$friend_mom_id;
                      $data['friend_userid']=$friend_userid;
                      $data['friend_visit_time']=time();
                      $data['friend_identity']=0;
                      if(M('Friend')->add($data)){
                          echo 'addok';
                      }else{
                          echo 'adderror';
                      }
                }else{
                  return false;
                }
                
            }
        }
    }

}