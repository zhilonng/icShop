<?php
namespace Admin\Controller;
class AttributeController extends AuthController{
    public function add()
    {
        if(IS_POST){
            $this->_add();
            die;
        }
        $catdatas=M('GoodsType')->select();
        $this->catdatas=$catdatas;
        $this->assign('acttxt','添加');
        $this->display('info');
    }
    protected function _add()
    {
        $Attribute=D('Attribute');
        if($Attribute->create()){
            //p($Attribute->create());
            if($Attribute->add()){
                $this->success('添加成功','index');
            }else{
                $this->error('添加失败，原因为:'.$Attribute->getDbError());
            }
        }else{
            $this->error('添加失败，原因为:'.$Attribute->getError());
        }
    }
    public function index(){
        if($_GET['keyword']!=''){
            $where['attr_name']=array('like',"%$_GET[keyword]%");
        }
        if($_GET['cat_id']!=''){
            $where['cat_id']=$_GET['cat_id'];
        }
        $count=M('Attribute')->where($where)->count();
        $page=new \Think\Page($count,10);
        $data=M('Attribute')
            ->where($where)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $pagelist=$page->show();
        $gtdata=M('GoodsType')->select(array('index'=>'cat_id'));
        $this->pagelist=$pagelist;
        $this->gtdata=$gtdata;
        $this->data=$data;
        $this->display();
    }
    public function edit($attr_id=0){
        if(IS_POST){
            $this->_edit();die;
        }
        //获取商品类型列表
        $catdatas = M('GoodsType')->select();
        $this->assign('catdatas', $catdatas);
        $this->assign('acttxt','编辑');
        //取出要编辑的对应属性信息
        $data=M('Attribute')->find($attr_id);
        $this->data=$data;
        //p($gadata);
        $this->display('info');


    }
    protected function _edit()
    {
        $Attribute=M('Attribute');
        if($Attribute->create()){
            if($Attribute->save()!==false){
                $this->success('编辑成功!',U('index'));
            }else{
                $this->error('编辑失败原因为:'.$Attribute->getDbError());
            }
        }else{
            $this->error('编辑失败原因为:'.$Attribute->getError());
        }
    }
    public function delte($attr_id=0)
    {
        if(M('Attribute')->delete($attr_id)){
            $this->success('删除成功',U('index'));
        }else{
            $this->error('删除失败原因为:'.M('Attribute')->getError());
        }
    }
    public function getAttr($cat_id = 0, $goods_id = 0)
    {
        $data =M('Attribute')
            ->field('*,a.attr_id')
            ->alias('a')
            ->join("LEFT JOIN __GOODS_ATTR__ as ga ON(a.attr_id=ga.attr_id and goods_id='$goods_id')")
            ->where("cat_id='$cat_id'")
            ->select();
        //p($data);
        ////取出attr表和goods表中的符合条件的数据进行遍历
        foreach($data as $k=> $v){
            //如果前一个的attr_id等于现在的attr_id则说明是第二个不输出+
            $sign=$data[$k-1]['attr_id']==$v['attr_id']? '-' :'+';
            //如果不是唯一属性就加一个单击事件
            echo "<tr><th>".($v['attr_type']>0 ? "<a onclick='copy_row(this)'>[$sign]</a>" : '')."$v[attr_name]</th><td>";
           /* if($v['attr_type']>0){
                echo "[+]$v[attr_name]</th><td>";
            }else{
                echo "$v[attr_name]</th><td>";
            }*/
            //此方式为手工录入
            if($v['attr_input_type']==0){
                //输出一个输入框 value等于数组的attr_value下标的值
               echo "<input type='text' name='attr_value[$v[attr_id]]'  value='$v[attr_value]'>";
            }elseif($v['attr_input_type']==1){
                //等于1是从列表中选择
                echo "<select id='attr_value' name='attr_value[$v[attr_id]]".($v['attr_type']>0 ? '[]' : '')."'>";
                echo "<option value='0'>请选择...</option>";
                $arr=explode("\n",$v['attr_values']);
                foreach($arr as $vo){
                    $sel=$vo==$v['attr_value'] ? 'selected' : '';
                    echo "<option value='$vo' $sel>$vo</option>";
                }
                echo "</select>";
                if($v['attr_type']>0){
                    echo "属性价格<input type='text' name='attr_price[$v[attr_id]][] size=10' value='$v[attr_price]'>";
                }
            }elseif($v['attr_input_type']==2){
                echo "<textarea name='attr_value[$v[attr_id]]'></textarea>";
            }
            echo "</td></tr>";
        }
    }
}