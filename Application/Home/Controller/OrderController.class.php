<?php
namespace Home\Controller;
class OrderController extends CommonController
{
    public function add()
    {
        //如果未登录跳转到登录页面否则加载视图
        $userinfo=cookie('USER');
        if(!$userinfo){
            $this->error('请先登录', U('User/Public/login', array('redirect'=>'Home-Order-add')));
        }
        $this->userinfo=$userinfo;
        $array=D('Cart')->lists();
        if(IS_POST){
            //由于POST到的数据组装也需要$array和$userinfo直接传递过去
            $this->_add($array,$userinfo);
            exit;
        }
        //p($array);
        $this->assign($array);
        $this->assign('nocart',true);
        $this->display();
    }
    protected function _add($array=array(),$userinfo=array())
    {
        //组装需要的数据
        $_POST['order_amount']=$array['total_price'];
        $order=D('Order');
        $_POST['user_id']=$userinfo['id'];
        //定义一个错误的变量默认为空 后面有错误就把错误赋给该变量;
        $error='';
        $order->startTrans();
        if($data=$order->create()){
             if($order_id=$order->add()){
                 //先入order表 成功以后开始入order_goods表开启事务
                 $orderGoods=M('OrderGoods');
                 $ogdata=array();
                 //开始循环大数组
                 foreach($array['data'] as $k=>$v){
                     $v['order_id']=$order_id; //订单id
                     unset($v['id']);
                     $v['goods_price']=$v['price']*$v['quantity']; //商品总价
                     //将商品快照序列化
                     $v['snapshot']=serialize(array(
                         'goods_name'=>$array['gdata'][$v['goods_id']]['goods_name'], //商品名
                         'goods_img'=>$array['gdata'][$v['goods_id']]['goods_img'],   //商品图
                         'spec'=>$v['av'],  //商品规格
                     ));
                     if($v=$orderGoods->create($v)){
                         $ogdata[]=$v;
                     }else{
                         $error=$orderGoods->getError();
                         break;
                     }
                 }
                 if(!$error && $ogdata && $orderGoods->addAll($ogdata)){
                     //ordergoods表成功增加后应该删除购物车表里的订单
                     $cart=M('Cart');
                     if($cart->where("user_id='$userinfo[id]'")->delete()){
                         //三个表操作完成
                     }else{
                         $error= $cart -> getDbError();
                     }
                 }else{
                     $error= $orderGoods -> getDbError();
                 }
             }else{
                 $error=$order->getDbError();
             }
        }else{
            $error=$order->getError();
        }
        //开始处理事务
        if($error){
            $order->rollback();
            $this->error('操作失败原因为:'.$error);
        }else{
            $order->commit();
            $this->success('订单提交成功',U('User/Index/index'));
        }

    }
}