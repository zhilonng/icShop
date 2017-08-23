<?php
namespace Admin\Controller;
class GoodsTypeController extends AuthController{
    public function add()
    {
        if(IS_POST){
            $this->_add();
        }
        $this->assign('acttxt','添加');
        $this->display('info');
    }
    protected function _add()
    {
        $goodsType=D('GoodsType');
        if(!$goodsType->create()){
            $goodsType->getError();
            die;
        }
        if(!$goodsType->add()){
            $goodsType->getDbError();
            die;
        }else{
            $this->success('添加商品类型成功!','index');
            die;
        }
    }
    public function edit($cat_id=0)
    {
        if(IS_POST){
            $this->_edit();
            die;
        }
        //$cat_id=$_GET['cat_id'];
        $lists=M('GoodsType')->find($cat_id);
        //dump($lists);die;
        $this->assign('lists',$lists);
        $this->assign('acttxt','编辑');
        $this->display('info');
    }
    protected function _edit()
    {
        $goodsType=D('GoodsType');
        if($goodsType->create()){
            if($goodsType->save()!==false){
                $this->success('编辑成功',U('index'));
            }else{
                $this->error('编辑失败,原因为:'.$goodsType->getDbError());
            }
        }else{
            $this->error('编辑失败,原因为:'.$goodsType->getError());
        }
    }
    public function index(){
        if($_GET['keyword']!=''){
            $where['cat_name']=array('like',"%$_GET[keyword]%");
        }
        $count=M('GoodsType')->where($where)->count();
        $page=new \Think\Page($count,3);
        $data=M('GoodsType')
            ->limit($page->firstRow.','.$page->listRows)
            ->where($where)
            ->select();
        $pagelist=$page->show();
        //dump($pagelist);die;
        $this->assign('pagelist',$pagelist);
        $this->assign('data',$data);
        //$this->assign('keyword',$keyword);
        //dump($data);die;
        $this->display();
    }
    public function delete()
    {
        $cat_id=$_GET['cat_id'];
        //dump($cat_id);die;
        if(M('GoodsType')->delete($cat_id)){
            $this->success('删除成功!',U('index'));
        }else{
            $this->error('删除失败');
        }
    }
    public function ajaxdel(){
        $cat_id=$_GET['cat_id'];
        //dump($cat_id);die;
        if(M('GoodsType')->delete($cat_id)){
            echo 1;
        }else{
            echo 0;
        }

    }
    public function ajaxdelq(){
     /*   if($_GET['keyword']!=''){
            $where['cat_name']=array('like',"%$_GET[keyword]%");
        }
        $count=M('GoodsType')->where($where)->count();
        $page=new \Think\Page($count,3);
        $Nowpage=$_GET['Nowpage'];
        $datas=M('GoodsType')
            ->limit(($Nowpage-1)*3,3)
            ->where($where)
            ->select();
        echo json_encode($datas);*/
    }
    public function getAttrGroup($cat_id=0){
        $data=M('GoodsType')
        ->where("cat_id='$cat_id'")
        ->getField('attr_group');
        //p($data);
        $data=explode("\n",$data);
        foreach($data as $k =>$v){
            echo "<option value='$k'>$v</option>";
        }
    }
}