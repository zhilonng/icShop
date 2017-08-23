<?php
namespace Admin\Controller;

use Think\Controller;

class UserController extends CommonController
{


    // 会员列表
    public function index()
    {   
        if($_GET['nickname']){
            $where['nickname']=array('LIKE',"%{$_GET['nickname']}%");
        }
        if($_GET['realname']){
            $where['realname']=array('LIKE',"%{$_GET['realname']}%");
        }
        $user = M('user');
        $count = $user->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $user
        ->where($where)
        ->limit($Page->firstRow . ',' . $Page->listRows)
        ->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    // 查看报告
    public function report()
    {
        if (IS_POST) {
            $data = I('post.');
            if ($data['is_change_all']==1) {
                foreach ($data['company'] as $key => $val) {
                    M('userReport')->where(array('report_id'=>$key))->delete();
                    foreach ($val as $k => $v) {
                        $arr = array(
                            'product_id' => $data['product'][$key][$k],
                            'company' => $v,
                            'property' => $data['property'][$key][$k],
                            'limit' => $data['limit'][$key][$k],
                            'payment' => $data['payment'][$key][$k],
                            'insured' => $data['insured'][$key][$k],
                            'premium' => $data['premium'][$key][$k],
                            'desc' => $data['desc'][$key][$k],
                            'user_id' => $data['user_id'],
                            'report_id' => $key,
                        );
                        if ($arr['company'] && $arr['property']) {
                            M('userReport')->add($arr);
                        }
                    }
                }


//                die();
//                if ($data['r_product'] && $data['r_company']) {
//                    foreach ($data['r_product'] as $key => $val) {
//                        M('userReport')->where(array('report_id'=>$key))->delete();
//                        $arr = array(
//                            'report_id' => $key,
//                            'product_id' => $val,
//                            'company' => $data['r_company'][$key],
//                            'property' => $data['r_property'][$key],
//                            'limit' => $data['r_limit'][$key],
//                            'payment' => $data['r_payment'][$key],
//                            'insured' => $data['r_insured'][$key],
//                            'premium' => $data['r_premium'][$key],
//                            'desc' => $data['r_desc'][$key],
//                            'user_id' => $data['user_id'],
//                        );
//                        var_dump($arr);
//                        M('userReport')->add($arr);
//                    }
//                    die();
//                }
            } else {
                if ($data['r_product'] && $data['r_company']) {
                    foreach ($data['r_product'] as $key => $val) {
                        $arr = array(
                            'id' => $key,
                            'product_id' => $val,
                            'company' => $data['r_company'][$key],
                            'property' => $data['r_property'][$key],
                            'limit' => $data['r_limit'][$key],
                            'payment' => $data['r_payment'][$key],
                            'insured' => $data['r_insured'][$key],
                            'premium' => $data['r_premium'][$key],
                            'desc' => $data['r_desc'][$key]
                        );
                        M('userReport')->save($arr);
                    }
                }

                foreach ($data['company'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        $arr = array(
                            'product_id' => $data['product'][$key][$k],
                            'company' => $v,
                            'property' => $data['property'][$key][$k],
                            'limit' => $data['limit'][$key][$k],
                            'payment' => $data['payment'][$key][$k],
                            'insured' => $data['insured'][$key][$k],
                            'premium' => $data['premium'][$key][$k],
                            'desc' => $data['desc'][$key][$k],
                            'user_id' => $data['user_id'],
                            'report_id' => $key,
                        );
                        if ($arr['company'] && $arr['property']) {
                            M('userReport')->add($arr);
                        }

                    }
                }
            }


            foreach ($data['age'] as $key => $value) {
                $arr = array(
                    'id' => $key,
                    'desc' => $data['rdesc'][$key],
                    'age' => $value,
                    'die' => $data['die'][$key],
                    'stricken' => $data['stricken'][$key],
                    'ailment' => $data['ailment'][$key],
                    'medical' => $data['medical'][$key]
                );
                M('report')->save($arr);
            }

            $arr = array(
                'user_id' => $data['user_id'],
                'desc' => $data['family'],
            );
            $_result = M('userConfig')->where(array('user_id' => $data['user_id']))->find();
            if ($_result) {
                $arr['id'] = $_result['id'];
                M('userConfig')->save($arr);
            } else {
                M('userConfig')->add($arr);
            }
            M('user')->save(array('id' => $data['user_id'], 'report_time' => time()));

            $user = M('user')->find($data['user_id']);

            // $data = array(
            // 	'touser'=> $user['openid'],
            // 	'template_id' => 'gi762nKIXE7wLQy--C9smakRV_f1qGarCB5NII7-gEQ',
            // 	"url" => "http://lab.wodemaya.com/report/index",
            // 	'data' => array(
            // 		'first' => array(
            // 			'value' => '我们已经更新了您的家庭保险配置报告，请及时查看。',
            // 			"color"=>"#173177"
            // 			),
            // 		'keyword1' => array(
            // 			'value' => $user['realname'] ? $user['realname'] : $user['nickname'],
            // 			"color"=>"#173177"
            // 			),
            // 		'keyword2' => array(
            // 			'value' => '信息已更新',
            // 			"color"=>"#173177"
            // 			),
            // 		'remark' => array(
            // 			'value' => "马上查看",
            // 			"color"=>"#173177"
            // 			),
            // 		)
            // 	);
            // sendWxNotice(json_encode($data));

            $serviceOrders=M('serviceOrder')->where(['user_id'=> $data['user_id']])->select();
            if($serviceOrders){
                foreach($serviceOrders as $serviceOrder){
                    if($serviceOrder['admin_id']==0){
                        $serviceOrder['admin_id']=$_SESSION[C('USER_AUTH_KEY')];
                        M('serviceOrder')->save($serviceOrder);
                    }
                }
            }

            $this->success('更新成功', U('user/report', array('id' => $user['id'])));

        } else {
            //宝爸
            $uid = I('id', 0);
            $this->user = M('user')->find($uid);
            $report = D('report');
            //取出宝爸的个人报告信息 根据product_id查询相关产品的URL 再赋值给$father
            $father=$report->relation(true)->where(array('user_id' => $uid, 'level' => 1))->find();
            $furll=$father;
            //p($furll);
            $fid=$furll['report'];
            if($fid){
                foreach ($fid as $k=>$v) {
                    $product_id=$v['product_id'];
                    $father['report'][$k]['url']=M('Product')->where("id=$product_id")->getField('url');
                }
            }
            $this->father =$father;
            //p( $father); 
  
            //宝妈
            $mather = $report->relation(true)->where(array('user_id' => $uid, 'level' => 0))->find();
            $murll=$mather;
            $mid=$murll['report'];
            if($mid){
                foreach ($mid as $k=>$v) {
                    $product_id=$v['product_id'];
                    $mather['report'][$k]['url']=M('Product')->where("id=$product_id")->getField('url');
                }
            }
            $this->mather =$mather;
            //p($mather);
     
            //宝宝
            $baby = $report->relation(true)->where(array('user_id' => $uid, 'level' => 2))->select();
            //p($baby);
             foreach ($baby as $k => $v) {
                $bid=$v['report'];
                foreach($bid as $ko=>$vo){
                    $product_id=$vo['product_id'];
                    $baby[$k]['report'][$ko]['url']=M('Product')->where("id=$product_id")->getField('url');
                }
             }
            //p($baby);
            $this->baby=$baby;
           /* $burl=$this->baby;
            $bid=$burl[0]['report'][0]['product_id'];
            if($bid){
               $burl=M('Product')->where("id=$bid")->getField('url');
               $this->burl=urlencode($burl); 
            }*/
            //p($burl);
            // $this->product = D('product')->relation(true)->where(array('status' =>1))->order(‘convert(title using gb2312) asc’)->select();


            $this->product = D('product')->relation(true)->where(array('status' => 1))->order('title  asc ')->select();
            //p($this->product);
            $this->family = M('userConfig')->where(array('user_id' => $uid))->find();
            $this->f_t_conf = M('templateConf')->where(array('level'=>0))->select();
            $this->m_t_conf = M('templateConf')->where(array('level'=>1))->select();
            $this->b_t_conf = M('templateConf')->where(array('level'=>2))->select();
            $this->fam_t_conf = M('templateConf')->where(array('level'=>3))->select();
            $this->t_all = M('templateAll')->select();
            //获取用户聊天时的备注信息
            $id=$_GET['id'];
            $userinfo=M('UserRemarks')->where("user_id=$id")->find();
            $tags=$userinfo['tags'];
            if(!$tags){
                //p('notags');
                $this->assign('uid', $uid);
                $this->display();
            }else{
                $tags=M('UserTags')->where(array('id'=>array('in',$tags)))->field('tag')->select();
                //p('tags');
                $info=$userinfo['user_info'];
                $this->tags=$tags;
                $this->info=$info;
                $this->assign('uid', $uid);
                $this->assign('havetags',true);
                $this->display();
            }

        }
    }

//增加唯一id，用于自动填充，增加单个产品P，用于一开始填充   eisen
    public function reportForm()
    {
        $this->rid = I('rid', 0);
        $this->type = uniqid();
        $this->product = D('product')->relation(true)->order('title asc')->select();
        //p($this->product);
        $this->p = $this->product['0'];
        $this->display();
    }


    // 删除产品
    public function delProduct()
    {
        $id = I('id', 0);
        $userReport = M('userReport');
        $_result = $userReport->find($id);
        //p($_result);
        if ($_result) {
            if ($userReport->delete($id)) {
                $this->ajaxReturn(array('status' => 1, 'msg' => '删除成功'));
            } else {
                $this->ajaxReturn(array('status' => 0, 'msg' => '删除失败'));
            }
        } else {
            $this->ajaxReturn(array('status' => 0, 'msg' => '请求失败'));
        }
    }


    // 删除用户
    public function delUser()
    {
        $id = I('id', 0);
        $user = M('user');
        $_result = $user->find($id);
        if ($_result) {
            if ($user->delete($id)) {
                $this->success('删除成功', U('user/index'));
            } else {
                $this->error('删除失败', U('user/index'));
            }
        } else {
            $this->error('找不到指定的用户', U('user/index'));
        }
    }

    //ajax获取产品  eisen
    public function ajaxProduct()
    {
        $id = I('id');
        $product = M('product')->field('id,company,property,limit,insured,premium,desc,payment,url')->where(array('id' => $id))->find();
        //p($product);
        if ($product) {
            $product['status'] = 1;
            $this->ajaxReturn($product);
        } else {
            $this->ajaxReturn(array('status' => 0));
        }
    }
}
