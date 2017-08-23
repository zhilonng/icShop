<?php
namespace Admin\Controller;
use Think\Controller;
class ProductController extends CommonController{
	
	// 默认
	public function index(){
		$start = $_GET['start'] ? strtotime($_GET['start'].' 00:00:00') : 0;
		$end = $_GET['end'] ? strtotime($_GET['end'].' 23:59:59') : 0;
		$style_num=$_GET['order_style_number'];
		$factory=$_GET['factory_id'];
		if($_GET){
			if($start && $end){
				$where['product_addtime'] = array('between', array($start, $end));
			}
			if($start && !$end){
				$where['product_addtime'] = array('egt', $start);
			}
			if(!$start && $end){
				$where['product_addtime'] = array('elt', $end);
			}
			if($style_num){
				$oid=M('Order')->where("order_style_number='$style_num'")->getfield('order_id');
				$where['product_order_id']=$oid;
			}
            if($factory){
                $where['b_order.order_factory']=array('like',"%$_GET[factory_id]%") ;
			}
			if($_GET['today']==1){
	                $a=strtotime(date('Y-m-d'));
	                $b=time();
	                $where['product_addtime'] = array('between', array($a, $b));
	                $this->today=date('Y年m月d日');
	        }
        }	
		$Product = M('Product');
		$count = $Product
		->where($where)
		->field('b_product.*,b_order.order_factory')
		->join('LEFT JOIN b_order  on b_product.product_order_id=b_order.order_id')
		->count();
		$Page = new \Think\Page($count,20);
		$show = $Page->show();
		$list = $Product
		->field('b_product.*,b_order.order_factory,b_order.order_style_number,b_order.order_date,b_order.order_fmoney,b_order.order_allmoney')
		->join('LEFT JOIN b_order on b_product.product_order_id=b_order.order_id')
		->where($where)
		//->relation(true)
		->order('product_acttime desc','product_order_id')
		->limit($Page->firstRow.','.$Page->listRows)
		->select();
		//p($list);
		foreach ($list as $k => $v) {
			$did=$v['product_detail_id'];
			$data=D('Detail')->relation(true)->find($did);
			//p($data);
			$list[$k]['detail_total']=$data['detail_total'];
			$list[$k]['detail_alltotal']=$data['detail_alltotal'];
			$list[$k]['detail_color']=$data['color']['color_name'];
			$list[$k]['detail_size']=$data['size']['size_name'];
			$factoryarr=explode(',', $v['order_factory']);
			//p($factoryarr);
            foreach ($factoryarr as  $vi) {
                $list[$k]['factory_name'][]=M('factory')->where("factory_id='$vi'")->getfield('factory_name');
                //p($list);
            }
            $list[$k]['factory_name']=implode(',', $list[$k]['factory_name']);
		}
		//p($list);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->factory=M('factory')->select();
		$this->display();
	}
	public function shaixuan(){
		$start = $_POST['start'] ? strtotime($_POST['start'].' 00:00:00') : 0;
		$end = $_POST['end'] ? strtotime($_POST['end'].' 23:59:59') : 0;
		$style_num=$_POST['style_num'];
		//p($style_num);
		$factory=$_POST['factory_id'];
		if($_POST){
			if($start && $end){
				$where['product_addtime'] = array('between', array($start, $end));
			}
			if($start && !$end){
				$where['product_addtime'] = array('egt', $start);
			}
			if(!$start && $end){
				$where['product_addtime'] = array('elt', $end);
			}
			if($style_num){
				$oid=M('Order')->where("order_style_number='$style_num'")->getfield('order_id');
				$where['product_order_id']=$oid;
			}
            if($factory){
                $where['b_order.order_factory']=array('like',"%$_GET[factory_id]%") ;
			}
        }	
        //p($where);
		$Product = M('Product');
		$list = $Product
		->field('b_product.*,b_order.order_factory,b_order.order_style_number,b_order.order_date,b_order.order_fmoney,b_order.order_allmoney')
		->join('LEFT JOIN b_order on b_product.product_order_id=b_order.order_id')
		->where($where)
		->select();
		//p($list);
		$catesum=array();
		$factMoney=0; //加工费=单款数量*加工费
		$totalMoney=0;//总成本=单款成本*单款出货数
		$allProduct=0;//总出货数
		foreach ($list as $ko => $vo) {
			$did=$vo['product_detail_id'];
			$data=D('Detail')->relation(true)->find($did);
			$list[$ko]['detail_total']=$data['detail_total'];
			$list[$ko]['detail_alltotal']=$data['detail_alltotal'];
			array_push($catesum,$vo['order_style_number']);
		}
		//p($list);
		foreach ($list as $k => $v) {
			$factMoney+=$v['product_quantity']*$v['order_fmoney'];
			$totalMoney+=$v['product_quantity']*$v['order_allmoney'];
			$allProduct+=$v['product_quantity'];
		}
		$data='<div class="meselect">款数:'.count(array_count_values($catesum)).'</br>共出货:'.$allProduct.'件</br>加工费:'.$factMoney.'元</br>总成本:'.$totalMoney.'元</div>';
		echo $data;
	}
	// 添加出货记录
	public function add(){
		if(IS_POST){
			//P($_POST);
			$list=$_POST;
			$order=M('Order');
			$stynum=$list['order_style_number'];
			$product_order_id=$order->where("order_style_number='$stynum'")->getfield('order_id');
			$ndata=array();
			foreach ($list['product_detail_id'] as $k => $v) {
				$ndata[$k]['product_detail_id']=$v;
			}
			foreach ($list['product_quantity'] as $k => $v) {
				$ndata[$k]['product_quantity']=$v;
			}
	        //定义一个错误的变量默认为空 后面有错误就把错误赋给该变量;开启事务
	        $error='';
	        $order->startTrans();
			foreach ($ndata as $key => $value) {
				$data = array(
					'product_order_id'=>$product_order_id,
					'product_detail_id' => $value['product_detail_id'],
					'product_quantity' => $value['product_quantity'],
					'product_user_id' => $_POST['product_user_id'],
					'product_remark'=>$_POST['product_remark'],
					'product_addtime' => strtotime($_POST['product_addtime']),
					'product_acttime'=>time(),		
				);
				$detail_id=$value['product_detail_id'];
				$ddata['detail_alltotal']=M('Detail')->where("detail_id=$detail_id")->getfield('detail_alltotal');
				$ddata['detail_alltotal']+=$value['product_quantity'];
				$ddata['detail_id']=$value['product_detail_id'];
				if(M('Detail')->save($ddata)!==false){
					if(M('Product')->add($data)){
						//三表操作完成
					}else{
                         $error=M('Product')->getDbError();
					}
				}else{
				  	$error=M('Detail')->getDbError();				
				}
			}
			//开始处理事务
	        if($error){
	            $order->rollback();
	            $this->error('操作失败原因为:'.$error);
	        }else{
	            $order->commit();
	            $this->success('出货记录添加成功',U('Admin/Product/add'));
	        }
		}else{
			//p($_SESSION);
			$this->date=date('Y-m-d');
			$this->display();
		}
	}
	//ajax获取出货id
	public function sel_detail()
	{
		$style_id=I('style_id');
        $list=M('Order')->field('order_detail_id,order_id')->where("order_style_number='$style_id'")->find();
        $oid=$list['order_id'];
        $str=$list['order_detail_id'];
        if(!$str){
        	die('error');
        }
        $odata=explode(',',$str);
        $data=array();
        foreach($odata as$k=>$v){
        	$qu_to=0;
            $list=D('Detail')
            ->relation(true)
            ->find($v);
            $data[$k]['id']=$list['detail_id'];
            $qu_to=$list['detail_total']-$list['detail_alltotal'];
            $data[$k]['info']=$list['color']['color_name'].'---'.$list['size']['size_name'].'---订单共:'.$list['detail_total'].'件---已出货:'.$list['detail_alltotal'].'件---未出货:'.$qu_to.'件';
            $data[$k]['data']='颜色:'.$list['color']['color_name'].'|尺码:'.$list['size']['size_name'];
        }       
                // echo "<div class='form-group after'>";
                // echo "<div class='selbox'>";     
                // echo "<input type='hidden' name='product_order_id' value='$oid'>"   ; 
                // echo "<label class='col-sm-2 control-label'><em>*</em>对应颜色尺码</label>";
                // echo "<div class='col-sm-4'>";
                // echo "<select style='width:390px'  class='form-control' name='product_detail_id'>";
                // echo "<option value='0'>请选择...</option>";
                // foreach($data as $vo){
                //     echo "<option value='$vo[id]'>$vo[info]</option>";
                // }
                // echo "</select>";
                // echo "</div>";
                // echo "</div>"; 
                // echo "</div>";

        $this->ajaxReturn($data);
	}


	// 编辑出货记录  
	public function edit(){
		if(IS_POST){
			//编辑出货要对数据库之前的数据进行已出货要先减少再更改增加 不然数据会出现错误
			$d_id=I('post.product_detail_id');
			$all_total=M('Detail')->where("detail_id=$d_id")->getfield('detail_alltotal');
			$ddata['detail_alltotal']=$all_total-I('last_p_quantity')+I('product_quantity');
            $ddata['detail_id']=$d_id;
            $pdata['product_id']=I('product_id');
            $pdata['product_quantity']=I('product_quantity');
            $pdata['product_remark']=I('product_remark');
            $pdata['product_addtime']=strtotime(I('product_addtime'));
            //p($pdata);
			if(M('Detail')->save($ddata)!==false){
        		if(M('Product')->save($pdata)!==false){
        			$this->success('编辑成功', U('product/index'));
        		}else{
					$this->error('编辑失败');
			    }
			}else{
				$this->error('编辑失败');
			}

		}else{
			$id = I('product_id');
			$list=D('Product')->relation(true)->find($id);
			$did=$list['product_detail_id'];
			$data=D('Detail')
			->relation(true)
			->find($did);
            $qu_to=$data['detail_total']-$data['detail_alltotal'];
            $list['info']=$data['color']['color_name'].'---'.$data['size']['size_name'].'---订单共:'.$data['detail_total'].'件---已出货:'.$data['detail_alltotal'].'件---未出货:'.$qu_to.'件';
			//p($list);
			$this->list=$list;
			$this->display();
		}
	}


	// 删除出货记录
	public function del(){
		//出货要对数据库之前的数据进行已出货要先减少再更改增加 不然数据会出现错误
		$pid = I('product_id');
		$_result = M('product')->where("product_id=$pid")->find($pid);
		$did=$_result['product_detail_id'];
		$detail_alltotal=M('Detail')->where("detail_id=$did")->getfield('detail_alltotal');
		$ddata['detail_alltotal']=$detail_alltotal-$_result['product_quantity'];
		//p($ddata['detail_alltotal']);
		$ddata['detail_id']=$did;
		if(M('Detail')->save($ddata)!==false){
            if(M('Product')->delete($pid)){
				$this->success('删除成功', U('product/index'));            
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
	}

}

?>