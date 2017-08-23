<?php
namespace Home\Model;
use Think\Model;
class OrderModel extends Model
{
    //给下单时间和订单号设置为自动完成
    protected $_auto=array(
        array('add_time','_autoAddTime',1,'callback'),
        array('order_sn','_autoOrder_sn',1,'callback'),
    );
    protected function _autoAddTime()
    {
        return time();
    }
    protected function _autoOrder_sn()
    {
        return date('YmdHi').uniqid();
    }
}