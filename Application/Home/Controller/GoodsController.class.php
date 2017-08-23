<?php
namespace Home\Controller;
class GoodsController extends CommonController
{
    public function detail()
    {
        //$photo=array();
        settype($_GET['goods_id'],'int');
        //根据传过来的Id查询对应商品的信息
        $data=M('Goods')->find($_GET['goods_id']);
        $this->data=$data;
        //取出相册信息
        $photo=explode(',',trim($data['photo'],','));
        $this->photo=$photo;
        //p($this->cdata);
        //面包屑效果 先取出所有祖先
        $parents=get_parents($this->cdata,$data['cat_id']);
        //吧自身加到该数组
        $parents[]=$this->cdata[$data['cat_id']];
        $this->parents=$parents;
        //根据goods_attr表链attribute表取出属性和对应名字
        $gadata=M('GoodsAttr')->alias('ga')
            ->JOIN('__ATTRIBUTE__ as a ON(ga.attr_id=a.attr_id)')
            ->where("goods_id='$_GET[goods_id]'")
            ->ORDER('goods_attr_id')
            ->select();
        //先选出是属于单选属性的
        $radio=array();
        $unique=array();
        foreach($gadata as $v){
            if($v['attr_type']==1){
                $radio[$v['attr_id']][]=$v;
            }elseif($v['attr_type']==0){
                $unique[$v['attr_id']][]=$v;
            }
        }
        //根据cat_id取出goods_type表的属性分组
        $group=M('GoodsType')->where("cat_id='$data[goods_type]'")
        ->getField('attr_group');
        $group=explode("\n",$group);
        //p($gadata);
        $this->unique=$unique;
        $this->gadata=$gadata;
        $this->group=$group;
        $this->radio=$radio;
        $this->display();
    }
}