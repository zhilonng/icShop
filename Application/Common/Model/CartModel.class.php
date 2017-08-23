<?php
namespace Common\Model;
use Think\Model;
class CartModel extends Model
{
    public function addToCart($data=array())
    {
        //已经登录情况下直接将物品加入购物车的数据表
        //没有规格的attr设为空,不设置为空 没有规格的同样的物品回存多条记录
        $attr_id=$data['attr'] ? implode(',',$data['attr']) : '';
        $userinfo=cookie('USER');
        //p($userinfo);
        if($userinfo){
            //先通过传过来的三个ID去数据库查询是否已经有相同的物品加入了购物车
            $where=array('goods_id'=>$data['goods_id'],'attr_id'=>$attr_id,'user_id'=>$userinfo['id']);
            $row=$this->where($where)->find();
            if($row){
                //如果已经在数据库中则吧数据表中的数量字段更新
                if($this->where(array('id'=>$row['id']))->setInc('quantity',$data['quantity'])){
                    return true;
                }
            }else{
                //否则把数据新增入表中
                //有的商品没有属性值则没有这个字段
                $attr_id && ($data['attr_id']=$attr_id);
                $data['user_id']=$userinfo['id'];
                if($this->create($data)){
                    if($this->add()) {
                        return true;
                    }else
                    {
                        return $this->getDbError();
                    }
                }else
                {
                    return $this->getError();
                }
            }
        }else{
            //设置一个名为cart的cookie  未登录情况下加入购物车
            $cart=cookie('cart');
            $key=$_POST['goods_id'].'-'.implode(',',$_POST['attr']);
            if($cart[$key]){
                $cart[$key]+=$_POST['quantity'];
            }else{
                $cart[$key]=$_POST['quantity'];
            }
            cookie('cart',$cart);
            return true;
        }

    }
    public function moveToDb()
    {
        //在Public/_login里面登录时调用此方法
        //重getcookie函数中获取数据
        $data=$this->getCookieCart();
        if($data){
            //循环插入数据
            foreach($data as $k => $v){
                $data[$k]=$this->create($v);
            }
            $this->addAll($data);
            //清除cookie
            cookie('cart',null);
        }
    }
    public function getCookieCart()
    {
        //将cookie购物车的数据移到数据表当中
        $userinfo=cookie('USER');
        $cart=cookie('cart');
        //p($cart);
        $arr=array();
        foreach($cart as $k=>$v){
            $gid=explode('-',$k);
            $data['id']=$userinfo ? null : $k;
            $data['goods_id']=$gid[0];
            $data['attr_id']=$gid[1];
            $data['user_id']=$userinfo['id'];
            $data['quantity']=$v;
            //为了登录和未登录都有id值可以加入到数据表 未登录用$k来代替
            $arr[]=$data;
        }
        //p($arr);
        return $arr;
    }
    public function lists()
    {
        $userinfo=cookie('USER');
        if($userinfo){
            //如果用户有登录从数据库取出数据
            $data=$this->where(array('user_id'=>$userinfo['id']))->select();
            if(!$data){
                return false;
            }

        }else{
            //否则从COOKIE取出数据
            $data=$this->getCookieCart();
            if(!$data){
                return false;
            }
        }
        if($data){
            //为了让两种地方取出来的数据都能以goods_ID为下标
            $goodsids=array_column($data,'goods_id');
        }
        $gdata=M('Goods')->field('goods_id,goods_name,goods_img,shop_price')
            ->where(array('goods_id'=>array('in',$goodsids)))
            ->select(array('index'=>'goods_id'));
        $total_price=0;
        foreach($data as $k=>$v){
            //用空格分开每一行的规格值 用：连接多个字段
            $arr=M('GoodsAttr')->alias('ga')
                ->field('sum(attr_price)as price,
                GROUP_CONCAT(concat(attr_name,":",attr_value)SEPARATOR "<br>")as av')
                ->join('__ATTRIBUTE__ as ab ON(ga.attr_id=ab.attr_id)')
                ->where(array('goods_attr_id'=>array('in',$v['attr_id'])))
                ->group('goods_id')
                ->find();
            $arr['price']+=$gdata[$v['goods_id']]['shop_price'];
            /*[price] => 500
              [av] => 排量:1.5L
              变速箱:手动
              座位数:5*/
            $data[$k]=array_merge($v,$arr);
            $total_price+=$v['quantity']*$arr['price'];
        }
            //return $data;
            return (array('data'=>$data,'gdata'=>$gdata,'total_price'=>$total_price));


    }
    public function edit($data=array())
    {
        $userinfo=cookie('USER');
        if($userinfo){
            //$data如下：
           /* Array
            (
                [id] => 12
                [quantity] => 2
            )*/
            $where=array('id'=>$data['id'],'user_id'=>$userinfo['id']);
            if($this->create()){
                if($this->where($where)->save()!==false){
                    return true;
                }else{
                    return "更新失败原因为:".$this->getDbError();
                }
            }else{
                return "更新失败原因为:".$this->getError();
            }
        }else{
            $cart=cookie('cart');
            $cart[$data['id']]=$data['quantity'];
            cookie('cart',$cart);
            return true;

        }
    }
    public function ajax_del($id=0)
    {
        $userinfo=cookie('USER');
        if($userinfo){
            //return $id;die;
            return $this->where(array('id'=>$id,'user_id'=>$userinfo['id']))->delete();
        }else{
            //如果未登录删除cookie里面的数据
            $cart=cookie('cart');
            unset($cart[$id]);
            //更新cookie里面的数据
            cookie('cart',$cart);
            return true;
        }

    }
}