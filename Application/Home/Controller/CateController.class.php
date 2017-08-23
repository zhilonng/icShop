<?php
namespace Home\Controller;
class CateController extends CommonController
{
    public function index()
    {
        settype($_GET['id'],'int');
        //实现面包屑效果
        $parents=get_parents($this->cdata,$_GET['id']);
        $this->parents=$parents;
        //得到当前分类里面的所有类 设一个topid 如果parents存在就是第一个的ID（顶级）否则是get的ID
        getTree(null);
        $topid=$parents ? $parents[0]['id'] : $_GET['id'];
        $sub=getTree($this->cdata,$topid);
        //由于查询都是所有后代最后把自己加上
        array_unshift($sub,$this->cdata[$topid]);
        $this->sub=$sub;
        //根据ID获取子代和子代的商品
        getTree(null);
        $sons=getTree($this->cdata,$_GET['id']);
        $ids=i_array_column($sons,'id');
        $ids[]=$_GET['id'];
        $data=M('Goods')
            ->field('goods_id,cat_id,goods_name,goods_img,shop_price,market_price,photo')
            ->where(array('cat_id'=>array('in',$ids)))
            ->select();
        //将取出来的photo字段用逗号分开，需要引用传递!!
        foreach($data as &$v){
            $v['photo']=explode(',',trim($v['photo'],','));
        }
        $this->data=$data;
        //p($data);
        //p($sub);
        //p($parents);
        $this->display();
    }
}