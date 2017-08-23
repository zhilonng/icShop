<?php
namespace Admin\Controller;
use Think\Controller;
class SaleController extends CommonController{
	
	// 销售列表
	public function index(){
        //p($_POST);
		$start = $_POST['start'] ? strtotime($_POST['start'].' 00:00:00') : 0;
		$end = $_POST['end'] ? strtotime($_POST['end'].' 23:59:59') : 0;
        $factory=$_POST['factory_id'];
        $custom=$_POST['sale_custom'];
        $sale_store=$_POST['sale_store'];
        //p($sale_store);
		if($_POST){
			if($start && $end){
				$where['sale_date'] = array('between', array($start, $end));
			}
			if($start && !$end){
				$where['sale_date'] = array('egt', $start);
			}
			if(!$start && $end){
				$where['sale_date'] = array('elt', $end);
			}
			if($username){
				$where['order_style_number'] =array('like',"$_POST[order_style_number]") ;
			}
			if($custom){
				$where['sale_custom'] =array('like',"%$_POST[sale_custom]%") ;
			}
            if($factory){
                $where['order_factory']=$_POST['factory_id'];
            }
            if($sale_store){
                $where['sale_store']=$_POST['sale_store'];
            }
		}
        if($_GET['today']==1){
                $a=strtotime(date('Y-m-d'));
                $b=time();
                $where['sale_addtime'] = array('between', array($a, $b));
                $this->today=date('Y年m月d日');

        }
		$Sale = D('Sale');
		$count = $Sale->where($where)->count();
		$Page = new \Think\Page($count,20);
		$show = $Page->show();
		$list = $Sale
		->where($where)
		->relation(true)
        ->order('sale_id')
		->limit($Page->firstRow.','.$Page->listRows)
		->select();
        //p($list);
        $all_total=0;
        foreach ($list as $k => $v) {
        	$data=D('Detail')->where("detail_id=$v[sale_detail_id]")->relation(true)->find();
        	$list[$k]['color']=$data['color']['color_name'];
        	$list[$k]['size']=$data['size']['size_name'];
            $all_total+=$v['sale_quantity']*$v['sale_price'];
        }
        $this->all_total=$all_total;
        $this->store=M('store')->select();
        $this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
    //销售记录
	public function add(){
		if(IS_POST){
			//p($_POST);
			$detail_id=I('sale_detail_id');
			$oldsale=M('detail')->where("detail_id=$detail_id")->getfield('detail_allsale');
			$ddata['detail_allsale']=$oldsale+I('sale_quantity');
			$ddata['detail_id']=$detail_id;
			if(M('detail')->save($ddata)===false){
				$this->error('添加失败');
				die;
			}
			$data = array(
				'sale_order_id' => I('sale_order_id'),
				'sale_detail_id' => I('sale_detail_id'),
				'sale_store' => I('sale_store'),
				'sale_custom' => I('sale_custom'),	
				'sale_quantity' => I('sale_quantity'),
				'sale_remark' => I('sale_remark'),	
				'sale_date' => strtotime($_POST['sale_date']),										
				'sale_addtime' => time(),
				'sale_user_id'=>I('sale_user_id'),	
				);
            //p($data);
				if(M('Sale')->add($data)){
					$this->success('添加成功', U('Sale/add'));
				}else{
					$this->error('添加失败');
				}

		}else{
			$this->store=M('store')->select();
			$this->date=date('Y-m-d H:i:s');
			$this->display();
		}
	}

	//ajax获取出货id
	public function sel_detail()
	{
		$style_id=I('style_id');
        $str=M('Order')
        ->field('order_id,order_detail_id,order_pic,order_fmoney,order_allmoney,order_salemoney')
        ->where("order_style_number=$style_id")
        ->find();
        if(!$str){
        	die('error');
        }
        $odata=explode(',',$str['order_detail_id']);
        $pic=$str['order_pic'];
        $order_id=$str['order_id'];
        $data=array();
        foreach($odata as$k=>$v){
        	$qu_to=0;
            $list=D('Detail')
            ->relation(true)
            ->find($v);
            $data[$k]['id']=$list['detail_id'];
            $qu_to=$list['detail_alltotal']-$list['detail_allsale'];
            $data[$k]['info']=$list['color']['color_name'].'---'.$list['size']['size_name'].'---出货共:'.$list['detail_alltotal'].'件---已销售:'.$list['detail_allsale'].'件---库存:'.$qu_to.'件';
        }      
                echo "<div class='form-group add'>";
                echo "<div class='selbox'>";  
                echo "<input type='hidden' name='sale_order_id' value='$order_id'>" ;   
                echo "<label class='col-sm-2 control-label'><em>*</em>对应颜色尺码</label>";
                echo "<div class='col-sm-4' style='width:800px;height:110px'>";
                echo "<select style='width:50%' name='sale_detail_id'  class='form-control'>";
                echo "<option value='0'>请选择...</option>";
                foreach($data as $vo){
                    echo "<option  value='$vo[id]'>$vo[info]</option>";
                }
                echo "</select>";
                echo "<img style='width:80px;height:70px;float:left' src='$pic' >";
                echo "</div>" ;
                echo "</div>"; 
                echo "</div>";

                echo  '<div class="form-group">';
                echo           '<label class="col-sm-2 control-label">零售价:</label>';
                echo            '<div class="col-sm-4" style="margin-top:6px">';
                echo               "{$str[order_salemoney]}元---加工费:{$str[order_fmoney]}元---总成本:{$str[order_allmoney]}元";
                echo            '</div>';
                echo       '</div>';


	}

	// 编辑出货记录  
	public function edit(){
		if(IS_POST){
            //p($_POST);
			//编辑出货要对数据库之前的数据进行已出货要先减少再更改增加 不然数据会出现错误
			$s_id=I('post.sale_detail_id');
			$all_sale=M('Detail')->where("detail_id=$s_id")->getfield('detail_allsale');
			$ddata['detail_allsale']=$all_sale-I('last_s_quantity')+I('sale_quantity');
            $ddata['detail_id']=$s_id;
            $pdata['sale_id']=I('sale_id');
            $pdata['sale_quantity']=I('sale_quantity');
            $pdata['sale_remark']=I('sale_remark');
            $pdata['sale_addtime']=strtotime(I('sale_addtime'));
            //p($pdata);
			if(M('Detail')->save($ddata)!==false){
        		if(M('Sale')->save($pdata)!==false){
        			$this->success('编辑成功', U('Sale/index'));
        		}else{
					$this->error('编辑失败');
			    }
			}else{
				$this->error('编辑失败');
			}

		}else{
			$id = I('sale_id');
			$list=D('Sale')->relation(true)->find($id);
            //p($list);
            $list['money']="{$list[order][order_salemoney]}元---加工费:{$list[order][order_fmoney]}元---总成本:{$list[order][order_allmoney]}";
			$order_id=$list['sale_order_id'];
			$detail_id=$list['sale_detail_id'];
			$list['sale_date']=date('Y-m-d',$list['sale_date']);
			$this->store=M('store')->select();
            $lists=D('Detail')->relation(true)->find($detail_id);
            $list['info']=$lists['color']['color_name'].'---'.$lists['size']['size_name'].'---出货共:'.$lists['detail_alltotal'].'件---已销售:'.$lists['detail_allsale'].'件---库存:'.($lists['detail_alltotal']-$lists['detail_allsale']).'件';
            $this->list=$list;
            $this->data=$data;   
			$this->display();
		}
	}

    // 删除销售记录
    public function del(){
        //出货要对数据库之前的数据进行已出货要先减少再更改增加 不然数据会出现错误
        $sid = I('sale_id');
        $list = M('sale')->where("sale_id=$sid")->field('sale_detail_id,sale_quantity')->find();
        //p($did);
        $did=$list['sale_detail_id'];
        $detail_allsale=M('Detail')->where("detail_id=$did")->getfield('detail_allsale');
        $ddata['detail_allsale']=$detail_allsale-$list['product_quantity'];
        $ddata['detail_id']=$did;
        if(M('Detail')->save($ddata)!==false){
            if(M('Sale')->delete($sid)){
                $this->success('删除成功', U('Sale/index'));            
            }else{
                $this->error('删除失败..sale');
            }
        }else{
            $this->error('删除失败..detail');
        }
    }

}
