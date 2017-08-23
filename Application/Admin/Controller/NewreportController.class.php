<?php
namespace Admin\Controller;

use Think\Controller;

class NewreportController extends CommonController
{

    // 查看报告
    public function report()
    {
    	
        if (IS_POST) {
            $data = I('post.');
           // p($data);
            if ($data['is_change_all']==1) {
                //p($data);
                foreach ($data['company'] as $key => $val) {
                    M('userReport')->where(array('report_id'=>$key,'temp_id'=>$data['temp_id']))->delete();
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
                            'temp_id'=>$data['temp_id'],
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
                //p($data);
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

           // p($data);
            foreach ($data['age'] as $key => $value) {
                $arr = array(
                    'id' => $key,
                    'desc' => $data['rdesc'][$key],
                    //'age' => $value,
                    'die' => $data['die'][$key],
                    'stricken' => $data['stricken'][$key],
                    'ailment' => $data['ailment'][$key],
                    'medical' => $data['medical'][$key]
                );
                M('report')->save($arr);
            }

            $arr = array(
                'user_id' => $data['user_id'],
                'family_desc' => $data['family'],
            );
            $_result = M('userFamily')->where(array('user_id' => $data['user_id']))->find();
            if ($_result) {
                $arr['id'] = $_result['id'];
               // p($arr);
                M('userFamily')->save($arr);
            } else {
                M('userFamily')->add($arr);
            }
            M('user')->save(array('id' => $data['user_id'], 'report_time' => time()));
            $user = M('user')->find($data['user_id']);

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
            $reportlist=$report->relation(true)->where(array('user_id' => $uid))->order('level asc')->select();

						$outpolicy = 0;
            foreach ($reportlist as $k=>$v) {
                    $reportlist[$k]['age']=$this->birthday($v['birthday']).'岁';
										$reportlist[$k]['out_report']= M('personOrder')->where(array('user_id' => $uid,'relation_level'=>$v['level']))->select();
                   	$reportlist[$k]['policy_count'] = count($reportlist[$k]['report']);
                   	$reportlist[$k]['out_report_count'] = count($reportlist[$k]['out_report']);
                   	$reportlist[$k]['conf'] =M('templateConf')->where(array('level'=>$v['level']))->select();
                   	$money_count=0;
                   	foreach ($reportlist[$k]['report'] as $k2=>$v2) {
                   		$money_count+=$v2['premium'];
                   	}
                   	$reportlist[$k]['money_count'] = $money_count;
                   	if($v['level']==11)
                   		$outpolicy = 1;
           	}
           	if($outpolicy == 0)
           	{
           		$out=array(
           			"id"=>0,
           			"level"=>11,
           			"desc"=>""
           		);
           		$reportlist[] = $out;
           	}
           	$all_money = $out_money + $all_money_count;
            $family = M('userFamily')->where(array('user_id' => $uid))->find();
            
            $family['all_money'] = $family['policy_money'] + $family['other_money'];
            $family['safe_scale'] = $family['all_money'] / ($family['income'] *100);
            $family['report_admin'] = M('admin')->field('nickname')->find($family['report_admin_id']);
           
            $family['insure_admin'] = M('admin')->field('nickname')->find($family['insure_admin_id']);
            
            
            	
            $family['birthday'] = 0;
            $family['report_day'] = (time() - $family['add_time'])/24/60/60 ;	            		
	      		$second1 = time();
	      		$second2 = mktime(0,0,0,1,1,date("Y"));
	      		$day = round(($second1-$second2)/3600/24);
	      		$days=array();
      			for($i=0;$i<7;$i++)
      			{
      				if($day +$i > 365)
      					$day=0-$i;
      				$days[]=$day+$i;
      			}
						$days=implode(',',$days);
						
	      		$where['b_report.birthday_count']=array('in',$days);
	      		$where['user_id'] = $uid;
	          $ubay = M('report')->where($where)->select();
            if($ubay)
            	$family['birthday'] = 1;	
            
						$this->reportlist = $reportlist;
            $this->family = $family;
            $conf = M('templateConf')->where(array('type'=>0))->select();
            
             $this->conf = $conf;
            $this->t_all = M('templateAll')->select();
           //p($this->conf);
            //获取用户聊天时的备注信息

            
           
            $this->assign('user_status', $user_status);
            
            $productCategory=M('productCategory')->field('cid,cname')->select();
            $productName=M('product')->field('id,cid,title')->order('title asc')->select();
            foreach ($productCategory as $k=>$v) {
            	$productCategory[$k]['ptitle']=array();
            	$i=0;
							foreach ($productName as $k2=>$v2) {
								if($v2['cid']==$v['cid'])
								{
									$productCategory[$k]['ptitle'][$i]=$v2;
									$i++;
								}
							}
						}
						$tags=$family['tags'];
            $this->assign('productCategory', json_encode($productCategory));
            $this->assign('uid', $uid);
            $this->info=$userinfo['family_info'];
            if(!$tags){
                $this->display();
            }else{
                $tags=M('UserTags')->where(array('id'=>array('in',$tags)))->field('tag')->select();
                $this->tags=$tags;
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
    //获取年龄
    public function birthday($birthday){
        //$birthday='1992-04-29';
        $birthday=date('Y-m-d',$birthday);
        list($year,$month,$day) = explode('-',$birthday);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff  = date("d") - $day;
        if ($day_diff < 0 && $month_diff < 0){
            $year_diff--;
            return $year_diff;
        }else{
            return $year_diff;
        }
    }
    public function birthday_day($birthday){
        //$birthday='1992-04-29';
        $birthday=date('Y-m-d',$birthday);
        list($year,$month,$day) = explode('-',$birthday);
        $t1 = strtotime($year."-01-01");
        return round(($birthday - $t1)/3600/24);
    }
    public function xxxUser()
    {
    	
    		$productOrder = M('productOrder');
						$list = $productOrder
            ->field('b_product_order.*')
           ->group('id')
            ->order('add_time desc')->select();
            
        foreach ($list as $k => $v) {
            	$user_id=$v['user_id'];
            	$level=$v['level'];
            	//p($user_id.','.$level);
            	$mywhere['user_id']=$user_id;
            	$mywhere['level']=$level;
            	$rid = 0;
            	if($mywhere['level']>=2){
            		$mywhere['level']=2;
            		$report_id=M('Report')->field('id')->where($mywhere)->select();  
            		$level=$level-2;
            	  $rid=$report_id[$level]['id'];
            	  echo $user_id.'='.$level.'='.$rid."<br>";
            	}else {
            		$report_id=M('Report')->where($mywhere)->getfield('id'); 
            		//p($mywhere);
            		$rid=$report_id;		
            	}
            	$mywhere['level']=$v['level'];
            	$data['report_id'] = $rid;
            	M('productOrder')->where($mywhere)->save($data);
            	
        }    
    }  
}
