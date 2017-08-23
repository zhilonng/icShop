<?php
namespace Admin\Controller;
use Think\Controller;
class ZhenzhiOrderController extends CommonController{
	
	// 生产订单
	public function index(){
        //p($_POST);
        $this->sort=1;
        $start = $_GET['start'] ? :'1000-01-01';
        $end = $_GET['end'] ? : '9999-12-31' ;
        $order_style_number=$_GET['order_style_number'];
        $cate=$_GET['category'];
		if($_GET){
			if($start && $end){
				$where['order_date'] = array('between', array($start, $end));
			}
			if($start && !$end){
				$where['order_date'] = array('egt', $start);
			}
			if(!$start && $end){
				$where['order_date'] = array('elt', $end);
			}
			if($order_style_number){
				$where['order_style_number'] =array('like',"$_GET[order_style_number]") ;
			}
            if($cate){
                $where['order_category']=array('like',"%$_GET[category]%");
            }
            if($_GET['sort']){
                $sort=$_GET['sort']==1?'order_id desc':'order_id asc';
                $this->sort=$_GET['sort']==0?1:0;
            }
		}
        //p($where);
        if($_GET['today']==1){
                $a=strtotime(date('Y-m-d'));
                $b=time();
                $where['order_addtime'] = array('between', array($a, $b));
                $this->today=date('Y年m月d日新增');
        }
        if($_GET['today']==2){
                $this->today='总';
        }
		$Order = D('ZhenzhiOrder');
		$count = $Order->where($where)->count();
		$Page = new \Think\Page($count,10);
        // foreach($where as $key=>$val) {
        //     $Page->parameter   .=   "$key=".urlencode($val).'&';
        // }
		$show = $Page->show();
		$list = $Order
		->where($where)
        ->order($sort)
        ->relation(array('designer','paper'))
		->limit($Page->firstRow.','.$Page->listRows)
		->select();
        //p($list);
        foreach ($list as $k => $v) {
            //p($v);
            $factoryarr=explode(',', $v['order_factory']);
            $num=0;
            $hasnum=0;
            $sale=0;
            $color=array();
            $size=array();
            $quantity=array();
            $alltotal=array();
            $allsale=array();
            $did=$v['order_detail_id'];
            $did=explode(',',$did);
            foreach ($did as $ko => $vo) {
                $lists=D('ZhenzhiDetail')
                ->relation(true)
                ->find($vo);
                //p($lists);
                $color[]=$lists['color']['color_name']; 
                $size[]=$lists['size']['size_name'];
                $quantity[]=$lists['detail_total'];
                $alltotal[]=$lists['detail_alltotal'];
                $allsale[]=$lists['detail_allsale'];
            }
            //p($allsale);
            $num=array_sum($quantity);
            $hasnum=array_sum($alltotal);
            $sale=array_sum($allsale);
            $size=array_flip(array_flip($size));
            $color=array_flip(array_flip($color));
            $list[$k]['order_color']=implode($color,',');
            $list[$k]['order_size']=implode($size,',');
            $list[$k]['order_quantity']=$num;
            $list[$k]['order_alltotal']=$hasnum;
            $list[$k]['order_to_all']=$num-$hasnum;
            $list[$k]['order_sale']=$sale;
            $list[$k]['order_cangku']=$hasnum-$sale;
            $list[$k]['order_date']=date('y-m-d',strtotime($list[$k]['order_date']));
        }
        $this->password=M('password')->where("type='delete'")->getfield('password');
        $this->assign('list', $list);
		$this->assign('page', $show);
        //p($_GET);
		$this->display();
	}
    public function detail()
    //动态的修改详情单里面的数据
    {
    	if(IS_POST){  
                //p($_POST);
                $id=I('order_id');
                $data=M('ZhenzhiOrder')->create();
                if($data['order_factory']){
                    $arr=explode(',',$data['order_factory']);
                    $str='';
                    foreach ($arr as $k => $v) {
                        $str=$str.','.M('factory')->where("factory_name='$v'")->getfield('factory_id');
                    }
                    $data['order_factory']=ltrim($str,',');
                }
                $filepath=M('ZhenzhiOrder')->where("order_id=$id")->getfield('order_pic');
                if($_POST['imgpath']){
                    if($filepath!=$_POST['imgpath']){
                    unlink("/home/wwwroot/order$filepath");  
                    $data['order_pic']=$_POST['imgpath']; 
                    }  
                }
                //p($data);
    			if(M('ZhenzhiOrder')->save($data)!==false){
    				$this->success('保存成功',U('Admin/ZhenzhiOrder/index'));
    			}else{
    				$this->error('保存失败');
    			}
    		}else{
                $order_id=$_GET['order_id'];
                $factory=M('factory')->select();
                $list=M('ZhenzhiOrder')->find($order_id);
                 $factoryarr=explode(',', $list['order_factory']);
                $this->list=$list;
                $did=explode(',',$list['order_detail_id']);
                $detail=array();
                foreach ($did as $ko => $vo) {
                    $lists=D('ZhenzhiDetail')
                    ->relation(true)
                    ->find($vo);
                    //p($lists);
                    $d_did=$lists['detail_id'];
                    $rdata=M('ZhenzhiRedo')->where("redo_detail_id=$d_did")->field('redo_id,redo_detail_total')->select();
                    $num=0;
                    foreach ($rdata as $key => $value) {
                        $num+=$value['redo_detail_total'];
                    }
                    $detail[$ko]['detail_id']=$lists['detail_id'];
                    $detail[$ko]['detail_color']=$lists['color']['color_name']; 
                    $detail[$ko]['detail_size']=$lists['size']['size_name'];
                    $detail[$ko]['detail_total']=$lists['detail_total'];
                    $detail[$ko]['first']=$lists['detail_total']-$num;
                }
                //p($detail);
                $this->detail=$detail;
                $this->factory=$factory;
                $this->designer=M('designer')->select();
                $this->paper=M('paper')->select();
                $this->display('detail');
            }

    }
    public function delDetail()
    {
        //P($_POST);
        //改变详情字段的字符串，删除详情里面的id  删除返单里面的记录
        $did=I('did').',';
        $id=I('oid');
        $detail_id=I('did');
        if(M('ZhenzhiDetail')->delete($did)){
            $str=M('ZhenzhiOrder')->where("order_id=$id")->getfield('order_detail_id');
            $data['order_id']=$id;
            $data['order_detail_id']=str_replace($did,'',$str);
            if(M('ZhenzhiOrder')->save($data)){
                M('ZhenzhiRedo')->where("redo_detail_id=$detail_id")->delete();
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
    }

    public function ajaxChangeTotal()
    {
        $id=I('id');
        $old=M('ZhenzhiDetail')->where("detail_id=$id")->getfield('detail_total');
        if($old==I('total')){
            die('2');
        }
        $data['detail_id']=I('id');
        $data['detail_total']=I('total');
        if(M('ZhenzhiDetail')->save($data)!==false){
            echo 1;
        }else{
            echo 0;
        }
    }

    public function ajaxAllTotal()
    {
        $data['order_id']=I('order_id');
        $data['order_total']=I('total');
        if(M('ZhenzhiOrder')->save($data)!==false){
            echo 1;
        }else{
            echo 0;
        }
    }

     public function view()
    {
                $order_id=$_GET['order_id'];
                //p($order_id);
                $factory=M('factory')->select();
                $list=D('ZhenzhiOrder')->relation(true)->find($order_id);
                $did=explode(',',$list['order_detail_id']);
                $detail=array();
                $i=1;
                foreach ($did as $ko => $vo) {
                    $lists=D('ZhenzhiDetail')
                    ->relation(true)
                    ->find($vo);
                   // p($lists);
                    $koo=$lists['detail_color'];
                    if(array_key_exists($koo,$detail)){
                        //p(1);
                        foreach ($detail as $key => $value) {
                           // p($detail);
                            $i++;
                            if($value[0]['detail_color']==$lists['color']['color_name']){
                                    //p(2);
                                $detail[$key][$i]['detail_color']=$lists['color']['color_name']; 
                                $detail[$key][$i]['detail_size']=$lists['size']['size_name'];
                                $detail[$key][$i]['detail_total']=$lists['detail_total'];
                                $detail[$key][$i]['detail_alltotal']=$lists['detail_alltotal'];
                                $detail[$key][$i]['detail_notout']=$lists['detail_total']-$lists['detail_alltotal'];
                                $detail[$key][$i]['detail_allsale']=$lists['detail_allsale'];
                                $detail[$key][$i]['detail_stock']=$lists['detail_alltotal']-$lists['detail_allsale'];
                            }
                        }
                    }else{
                        $detail[$koo][0]['detail_color']=$lists['color']['color_name']; 
                        $detail[$koo][0]['detail_size']=$lists['size']['size_name'];
                        $detail[$koo][0]['detail_total']=$lists['detail_total'];
                        $detail[$koo][0]['detail_alltotal']=$lists['detail_alltotal'];
                        $detail[$koo][0]['detail_notout']=$lists['detail_total']-$lists['detail_alltotal'];
                        $detail[$koo][0]['detail_allsale']=$lists['detail_allsale'];
                        $detail[$koo][0]['detail_stock']=$lists['detail_alltotal']-$lists['detail_allsale'];
                    }
                }
                //p($detail);
                $this->list=$list;
                $this->detail=$detail;
                $this->display();
    }

    //文件上传
    public function mulUpload()
    {
        // 设置存储路径
        $_POST['_rootpath'] = C('TMPL_PARSE_STRING.__UP_ORDER__');

        // 调用函数实现上传
        $res = upload();

        // 输出子目录及文件名
        echo date('Y-m-d') . '/' . $res['file']['savename'];

        //p($res);
        //file_put_contents( uniqid().'.txt', var_export($_FILES, true) );
    }
    
    public function addOrder()
    {
    	if(IS_POST){
    		//p($_POST);
                $d_data=array();
                foreach ($_POST['order_color'] as $k => $v) {
                    $d_data[$k]['detail_color']=$v;
                }
                 foreach ($_POST['order_size'] as $k => $v) {
                    $d_data[$k]['detail_size']=$v;
                }                
                foreach ($_POST['order_total'] as $k => $v) {
                    $d_data[$k]['detail_total']=$v;
                } 
                $number=array();
                //p($d_data);
                foreach ($d_data as $ko => $vo) {
                    $number[]=M('ZhenzhiDetail')->add($d_data[$ko]);
                }
                    $data['order_pic']=I('imgpath'); 
                    $data['order_detail_id']=implode($number,',');
                    $data['order_style_number']=I('order_style_number') ;
                    $data['order_category']=I('order_category') ;
                    $data['order_allmoney']=I('order_allmoney');
                    $data['order_date']=I('order_date');
                    $data['order_total']=I('order_quantity');
                    $data['order_order_delivery']=I('order_order_delivery');
                    $data['order_user_id']=I('order_user_id');
                    $data['order_addtime']=time();
                    //p($data);
                    if(M('ZhenzhiOrder')->add($data)){
                        //p($data);
                        $this->success('保存成功',U('Admin/ZhenzhiOrder/index'));
                    }else{
                        $this->error('保存失败');
                    }

        }else{
            $category=M('category')->select();
            $category=getTree($category);
            $color=M('color')->select();
            $this->color=allGetTree($color);
            $size=M('size')->select();
            $size=allGetTree1($size);
            $this->size=$size;
            $this->category=$category;
            $this->display('addorder');
        }
    }

    public function dateDetail($order_id){
        $start = $_POST['start'] ? strtotime($_POST['start'].' 00:00:00') : 0;
        $end = $_POST['end'] ? strtotime($_POST['end'].' 23:59:59') : 0;
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
        }
        $arrdetail=M('ZhenzhiOrder')
        ->field('b_zhenzhi_order.*,f.factory_name')
        ->join('__FACTORY__ as f on b_zhenzhi_order.order_factory=f.factory_id')       
        ->find($order_id);
        $odata['order_style_number']=$arrdetail['order_style_number'];
        $odata['factory_name']=$arrdetail['factory_name'];
        $odata['order_date']=$arrdetail['order_date'];
        $arrdetail=explode(',',$arrdetail['order_detail_id']);
        $data=array();
        //p($arrdetail);
        foreach($arrdetail as$k=> $v){
            $where['product_detail_id']=$v;
            $Product = D('ZhenzhiProduct');
            $data[] = $Product
            ->field('b_zhenzhi_product.*,d.detail_color,d.detail_size,d.detail_total,d.detail_alltotal,c.color_name,s.size_name')
            ->where($where)
            ->join('LEFT JOIN __ZHENZHI_DETAIL__ as d on __ZHENZHI_PRODUCT__.product_detail_id=d.detail_id')
            ->join("__COLOR__ as c on d.detail_color=c.id")
            ->join("__SIZE__ as s on d.detail_size=s.id") 
            ->order('product_addtime')
            ->select();
            //p($data);
        }
        //p($data);
        $list=array();
        foreach ($data as $ko => $vo) {
            foreach ($vo as $koo => $voo) {
                $list[]=$voo;
            }
        }
        $this->odata=$odata;
        //p($list);
        $this->assign('list', $list);
        $this->display();
    }
    public function del($order_id)
    {
        //删除订单 订单下相关详情 订单相关的返单 出货记录 订单先关的图片
        $list=M('ZhenzhiOrder')->find($order_id);
        $detail_ids=explode(',', $list['order_detail_id']);
        if($list['order_pic']){
            $file=$list['order_pic'];
            unlink("/home/wwwroot/order$file");
        }
        //p($detail_ids);
        if(M('ZhenzhiRedo')->where("redo_order_id=$order_id")->delete()===false){
            $this->error('删除失败');
            die;
        }
        foreach ($detail_ids as $k => $v) {
            if(M('ZhenzhiDetail')->delete($v)){
                continue;
            }
        }
        if(M('ZhenzhiProduct')->where("product_order_id=$order_id")->delete()===false){
            $this->error('删除失败');
            die;
        }
        if(M('ZhenzhiOrder')->delete($order_id)){
            $this->success('删除成功',U('Admin/ZhenzhiOrder/index'));
        }else{
            $this->error('删除失败');
        }
    }

    // public function addcolor()
    // {
    //     $this->colorGroup=M('color')->where("pid=0")->select();
    //         $this->color=$color;
    //     $this->display();
    // }

    // public function addsize()
    // {
    //     $this->sizeGroup=M('size')->where("pid=0")->select();
    //         $this->size=$size;
    //     $this->display();
    // }

    // public function ajaxColor()
    // {
    //     $pcolor=I('pcolorname');
    //     $color=I('colorname');
    //     $where['color_name']=array('like',"$pcolor");
    //     $list=M('color')->where($where)->find();
    //     //p($list);
    //     if(!$list){
    //         $data['pid']=0;
    //         $data['color_name']=$pcolor;
    //         if($id=M('color')->add($data)){
    //             $adata['pid']=$id;
    //             $adata['color_name']=$color;
    //             if(M('color')->add($adata)){
    //                 echo 'ok';
    //             }else{
    //                 echo 'add3error';

    //             }
    //         }else{
    //             echo 'add1error';
    //         }
    //     }else{
    //         $where['pid']=$list['id'];
    //         $where['color_name']=$color;
    //         if(M('color')->where($where)->find()){
    //             die('has');
    //         }else{
    //             $ndata['pid']=$list['id'];
    //             $ndata['color_name']=$color;
    //             if(M('color')->add($ndata)){
    //                 echo 'ok';
    //             }else{
    //                 echo 'add2error';
    //             }            
    //         }


    //     }
    // }

    // //ajax size

    // public function ajaxSize()
    // {
    //     $psize=I('psizename');
    //     $size=I('sizename');
    //     //p($_POST);
    //     $where['size_name']=array('like',"$psize");
    //     $list=M('size')->where($where)->find();
    //     //p($size);
    //     if(!$list){
    //         $data['pid']=0;
    //         $data['size_name']=$psize;
    //         if($id=M('size')->add($data)){
    //             $adata['pid']=$id;
    //             $adata['size_name']=$size;
    //             if(M('size')->add($adata)){
    //                 echo 'ok';
    //             }else{
    //                 echo 'add3error';
    //             }
    //         }else{
    //             echo 'add1error';
    //         }
    //     }else{
    //         $where['pid']=$list['id'];
    //         $where['size_name']=$size;
    //         if(M('size')->where($where)->find()){
    //             die('has');
    //         }else{
    //             $ndata['pid']=$list['id'];
    //             $ndata['size_name']=$size;
    //             if(M('size')->add($ndata)){
    //                 echo 'ok';
    //             }else{
    //                 echo 'add2error';
    //             }            
    //         }
    //     }
    // }

    public function ajaxGetColor()
    {
        $color=M('color')->select();
        $color=allGetTree($color);
        echo "<label class='col-sm-2 control-label'><em>*</em>颜色</label>";
        echo "<div class='col-sm-4' style='display: flex;flex-wrap: wrap;'>
";
        echo "<select id='ajaxchange' class='form-control' name='order_color[]'>";
        echo "<option value='0'>请选择</option>
";
        foreach($color as $vo){
            echo "<option value='$vo[id]'>".str_repeat('&nbsp;', $vo['level']*4).$vo['color_name']."</option>";
        }
        echo "</select>";
    }

    public function ajaxGetSize()
    {
        $size=M('size')->select();
        $size=allGetTree($size);
        echo "<label class='col-sm-2 control-label'><em>*</em>尺码</label>";
        echo "<div class='col-sm-4' style='display: flex;flex-wrap: wrap;'>
";
        echo "<select id='ajaxchange' class='form-control' name='order_size[]'>";
        echo "<option value='0'>请选择</option>
";
        foreach($size as $vo){
            echo "<option value='$vo[id]'>".str_repeat('&nbsp;', $vo['level']*4).$vo['size_name']."</option>";
        }
        echo "</select>";        
    }

    public function addRedo()
    {
        //增加一个返单添加记录的页面 通过款号查询出对应的信息，然后再通过具体的尺码和颜色查询是否有该类型，如果有在提交的时候自动在原基础上进行增加，否则直接新增一种颜色尺码的类型，在order中新加就行,ajax查询类型的时候如果有结果在input data 中增加一个hastype标示，表示已经有这种类型，
        if(IS_POST){
            //p($_POST);
            $d_data=array();
            foreach ($_POST['order_color'] as $k => $v) {
                $d_data[$k]['detail_color']=$v;
            }
                foreach ($_POST['order_size'] as $k => $v) {
                $d_data[$k]['detail_size']=$v;
            }                
            foreach ($_POST['order_total'] as $k => $v) {
                $d_data[$k]['detail_total']=$v;
            }
            foreach ($_POST['hastype'] as $k => $v) {
                $d_data[$k]['detail_id']=$v;
            } 
            //循环得到的数据
            //$detail_str='';
            $order_id=$_POST['order_id'];
            $order_factory=$_POST['redo_factory'];
            $factoryarr=explode(',', M('ZhenzhiOrder')->where(array('order_id'=>$order_id))->getfield('order_factory'));
            if(!in_array($order_factory, $factoryarr)){
                $factoryarr[]=$order_factory;
                $odata['order_factory']=implode(',',$factoryarr);
                $odata['order_id']=$order_id;
                if(M('ZhenzhiOrder')->save($odata)===false){
                    $this->error('保存失败..detail');
                }
            }
            //p($odata);
            $detail_arr=array();
            foreach ($d_data as $ko => $vo) {
                if(!$vo['detail_id']){
                    $newdata['detail_color']=$vo['detail_color'];
                    $newdata['detail_size']=$vo['detail_size'];
                    $newdata['detail_total']=$vo['detail_total'];
                    if($vo['detail_id']=M('ZhenzhiDetail')->add($newdata)){
                        $detail_arr['id'][]=$vo['detail_id'];
                        $detail_arr['total'][]=$vo['detail_total'];
                    }else{
                        $this->error('保存失败..detail');
                    }
                }else{
                    $detail_id=$vo['detail_id'];
                    $list=M('ZhenzhiDetail')->find($detail_id);
                    $redata['detail_total']=$vo['detail_total']+$list['detail_total'];
                    $redata['detail_id']=$list['detail_id'];
                    if(M('ZhenzhiDetail')->save($redata)!==false){
                        $detail_arr['id'][]=$vo['detail_id'];
                        $detail_arr['total'][]=$vo['detail_total'];
                    }
                }
            }
            //p($detail_arr);
                $order_id=I('order_id');
                $list_str=M('ZhenzhiOrder')->where(array('order_id'=>$order_id))->getfield('order_detail_id');
                $list_str=explode(',',$list_str);
                $list_str=array_merge($detail_arr['id'],$list_str);
                $list_str=array_flip(array_flip($list_str));
                $newtotal=0;
                foreach($list_str as $v){
                    $newtotal+=M('ZhenzhiDetail')->where(array('detail_id'=>$v))->getfield('detail_total');
                }
                $odata['order_total']=$newtotal;
                $odata['order_detail_id']=implode(',',$list_str);
                $odata['order_id']=$order_id;
                if(M('ZhenzhiOrder')->save($odata)!==false){
                    $sdata=array();
                    foreach ($detail_arr['id'] as $ki=>$vi) {
                        $sdata[$ki]['detail_id']=$vi;
                    }
                    foreach($detail_arr['total'] as $kii=>$vii){
                        $sdata[$kii]['total']=$vii;
                    }
                    //p($sdata);
                    foreach ($sdata as $key => $value) {
                        $rdata['redo_factory']=I('redo_factory');
                        $rdata['redo_order_id']=I('order_id');
                        $rdata['redo_detail_id']=$value['detail_id'];
                        $rdata['redo_detail_total']=$value['total'];
                        $rdata['redo_date']=I('redo_date');
                        $rdata['redo_user_id']=I('order_user_id');
                        $rdata['redo_addtime']=time();
                        $rdata['redo_remark']=I('redo_remark');
                        if(M('ZhenzhiRedo')->add($rdata)){
                            continue;
                        }else{
                            $this->error('保存失败..redo');
                        }
                    }
                        $this->success('返单添加成功',U('Admin/ZhenzhiOrder/index'));
                }else{
                    $this->error('保存失败..order');
                }
 
           // p($d_data);
        }
        if(IS_GET){
            $order_id=$_GET['order_id'];
            $list=M('ZhenzhiOrder')->find($order_id);
            $farr=explode(',', $list['order_factory']);
            $list['order_factory']=$farr[0];
            //p($list);
            $this->list=$list;
            $color=M('color')->select();
            $this->color=allGetTree($color);
            $size=M('size')->select();
            $size=allGetTree1($size);
            $this->paper=M('paper')->select();
            $this->size=$size;
            $this->designer=M('designer')->select();
            $this->factory=M('factory')->select();
            $this->display();
        }
        
    }

    public function editRedo($redo_id)
    {
        if(IS_POST){
            //可以添加以后进行颜色尺码的 ajax is_new查询 如果是新的才进行新增 
            //两种情况 一种是已经有该颜色该尺码 另外一种是没有该颜色该尺码
            $id=$_POST['order_id'];
            $did=M('Order')->where("order_id=$id")->getfield('order_detail_id');
            $did=explode(',',$did);
            foreach ($did as $ko => $vo) {
                $ddata[]=M('Detail')->find($vo);
            }
            p($ddata);
            foreach ($ddata as $v) {
                if($v['detail_color']==$_POST['detail_color'] && $v['detail_size']==$_POST['detail_size']){
                    p(1);
                    //该情况是已经有该尺码该颜色的情况
                    if($v['detail_id']==I('detail_id')){
                        //说明是之前的颜色和之前的尺码
                        $rdata['redo_id']=I('redo_id');
                        $rdata['redo_detail_total']=I('redo_detail_total');
                        $rdata['redo_remark']=I('redo_remark');
                        if(M('Redo')->save($rdata)!==false){
                            $this->success('编辑成功',U('Admin/Order/redoRecord',array('order_id'=>$id)));                        
                        }else{
                            $this->error('编辑失败...redosaveold');
                        }
                    }else{
                        //有颜色尺码
                        $olddata['detail_id']=I('detail_id');
                        $olddata['detail_total']=M('detail')->where($olddata)->getfield('detail_total')-I('redo_detail_total');
                        M('Detail')->save($olddata);
                        $newdata['detail_id']=$v['detail_id'];
                        $newdata['detail_total']=M('detail')->where($newdata)->getfield('detail_total')+I('redo_detail_total');
                        M('Detail')->save($newdata);
                        $rdata['redo_id']=I('redo_id');
                        $rdata['redo_detail_total']=I('redo_detail_total');
                        $rdata['redo_detail_id']=$v['detail_id'];
                        $rdata['redo_remark']=I('redo_remark');
                        if(M('Redo')->save($rdata)!==false){
                            $this->success('编辑成功',U('Admin/Order/redoRecord',array('order_id'=>$id)));                        
                        }else{
                            $this->error('编辑失败...redosavenew');
                        }
                    } 
                }               
            } 
            //跳出foreach
            p(2);
            //没有该尺码的情况 ,先把原数据的数量减掉
            $olddata['detail_id']=I('detail_id');
            $olddata['detail_total']=M('detail')->where($olddata)->getfield('detail_total')-I('redo_detail_total');
            M('Detail')->save($olddata);                    
            $ddata['detail_color']=I('detail_color');
            $ddata['detail_size']=I('detail_size');
            $ddata['detail_total']=I('redo_detail_total');
            $rdata['redo_id']=I('redo_id');
            $rdata['redo_detail_id']=M('Detail')->add($ddata);
            $rdata['redo_detail_total']=I('redo_detail_total');
            $rdata['redo_remark']=I('redo_remark');  
            if(M('Redo')->save($rdata)!==false){

                $this->success('编辑成功',U('Admin/Order/redoRecord',array('order_id'=>$id)));
            }else{
                $this->error('编辑失败...newdetail');
            }             



        }else{
            $list=D('Redo')->relation(true)->find($redo_id);
            //p($list);
            $this->list=$list;
            $this->color=M('color')->select();
            $this->size=M('size')->select();
            $this->display();
        }
        //p($list);
    }
    public function delRedo()
    {
        $redo_id=I('redo_id');
        $list=M('ZhenzhiRedo')->find($redo_id);
        $detail_id=$list['redo_detail_id'];
        $order_id=$list['redo_order_id'];
        //删除返单 先在详情里查找对应详情的ID 有两种情况 一是直返单是一个新的详情 返单的数量就等于详情的裁床数
        //另外一种是返单是在已经有的详情基础上再次添加的 要先取出总数再减去该数值
        $dtotal=M('ZhenzhiDetail')->where("detail_id=$detail_id")->getfield('detail_total');
        //返单的数量
        $redo_total=$list['redo_detail_total'];
        if($dtotal==$list['redo_detail_total']){
            //p(11);
            if(M('ZhenzhiDetail')->delete($detail_id)){
                //删除订单里详情记录id记录
                $lists=M('ZhenzhiOrder')->field('order_total,order_detail_id')->find($order_id);
                //id字符串循环找出要删除的id然后再合成字符串
                $arr=explode(',', $lists['order_detail_id']);
                foreach ($arr as $k=>&$v) {
                    if($v==$detail_id){
                        unset($arr[$k]);
                    }
                }
                //还有如果工厂要删除的话需要修改
                $odata['order_id']=$order_id;
                $odata['order_detail_id']=implode(',',$arr);
                $odata['order_total']=$lists['order_total']-$redo_total;
                if(M('ZhenzhiOrder')->save($odata)!==false){
                    if(M('ZhenzhiRedo')->delete($redo_id)){
                        $this->success('删除成功',U('Admin/ZhenzhiOrder/index'));
                    }else{
                        $this->error('删除失败...redo_del');
                    }
                }else{
                    $this->error('删除失败...order_save');
                }     
            }
        }else{
            //已经存在详情的id上删除的返单，在详情的裁床数上先减少然后再订单里面减去
            $ndtotal=$dtotal-$redo_total;
            $data['detail_id']=$detail_id;
            $data['detail_total']=$ndtotal;
            if(M('ZhenzhiDetail')->save($data)===false){
                $this->error('删除失败...detail_save');die;
            }
            $ototal=M('ZhenzhiOrder')->where("order_id=$order_id")->getfield('order_total');
            $odata['order_id']=$order_id;
            $odata['order_total']=$ototal-$redo_total;
                if(M('ZhenzhiOrder')->save($odata)!==false){
                    if(M('ZhenzhiRedo')->delete($redo_id)){
                        $this->success('删除成功',U('Admin/ZhenzhiOrder/index'));
                    }else{
                        $this->error('删除失败...redo_del');
                    }
                }else{
                    $this->error('删除失败...order_save');
                }
        }
    }

    public function ajaxredo()
    {
        $id=$_POST['order_id'];
        $did=M('ZhenzhiOrder')->where("order_id=$id")->getfield('order_detail_id');
        $did=explode(',',$did);
        foreach ($did as $ko => $vo) {
            $ddata[]=M('ZhenzhiDetail')->find($vo);
        }
        if(!$ddata){
            echo '';
        }else{
            foreach ($ddata as $v) {
                if($v['detail_color']==$_POST['color'] && $v['detail_size']==$_POST['size']){
                    echo $v['detail_id'];
                }else{
                    echo '';
                }
            }    
        }
    }
    public function redoRecord($order_id)
    {
        $list=M('ZhenzhiRedo')
        ->where("redo_order_id=$order_id")
        ->select();
       // p($list);
        foreach ($list as $ko => $vo) {
            $detail_id=$vo['redo_detail_id'];
            $lists=D('ZhenzhiDetail')
            ->where("detail_id=$detail_id")
            ->relation(true)
            ->find();
            $list[$ko]['detail_color']=$lists['color']['color_name']; 
            $list[$ko]['detail_size']=$lists['size']['size_name'];
        }
        //p($list);
        $odata=M('ZhenzhiOrder')
        ->field('b_zhenzhi_order.order_style_number')
        ->find($order_id);
        $this->odata=$odata;
        $this->factory=M('factory')->select();
        $this->list=$list;
        $this->display();
    }
    //upladofile
    public function uploadImg(){  
  
        $upload = new \Think\Upload();// 实例化上传类  
        $upload->maxSize   =     3145728 ;// 设置附件上传大小  
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型  
        //$upload->rootPath = '/Applications/MAMP/htdocs/test/Public/uploads/Admin/';
         $upload->rootPath = '/home/wwwroot/order/Public/Uploads/Admin/';
        // $upload->savePath = '';
        $upload->saveName =date('YmdHis').mt_rand(1000,9999);
          
        // 上传文件   
        $info   =   $upload->uploadOne($_FILES['weixin_image']);  
        if(!$info) {// 上传错误提示错误信息  
            //$this->error($upload->getError());  
            echo $upload->getError();  
        }else{// 上传成功 获取上传文件信息  
            //处理照片
            $image = new \Think\Image(); 
            $image->open('/home/wwwroot/order/Public/Uploads/Admin/'.$info['savepath'].$info['savename']);
            // 按照原图的比例生成一个最大为400*400的缩略图
              $image->thumb(400, 400)->save('/home/wwwroot/order/Public/Uploads/Admin/'.$info['savepath'].$info['savename']);
            //$this->display('templateList');  
            echo "/Public/Uploads/Admin/".$info['savepath'].$info['savename'];  
        }  
    }
    public function delimg()
    {
        $path=I('path');
        if(unlink("/home/wwwroot/order$path")){
            echo 1;
        }else{
            echo 0;
        }
    } 
    public function checkno()
    {
        $no=I('no');
        if(M('ZhenzhiOrder')->where("order_style_number='$no'")->find()){
            echo 'repeat';
        }else{
            echo 'no';
        }
    }

}
