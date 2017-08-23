<?php
namespace Admin\Controller;
class CategoryController extends AuthController
{
    public function add(){
        if(IS_POST){
            $this->_add();die;
        }
        $this->assign('acttxt','添加');
        $this->display('add');
    }
  protected function _add()
    {
        //p($_POST);
        $company = M('Category');
        //p($company->create());
        if ($company->create()) {
            $where['category_name']=I('category_name');
            if($company->where($where)->find()){
                $this->error('添加失败，已经存该保险类型名称');die;
            }
            if ( $company->add()) {
                $this->success('添加成功', U('index'));
            } else {
                $this->error('添加失败，原因为：' . $goods->getDbError());
            }
        } else {
            $this->error('添加失败，原因为：' . $goods->getError());
        }
    }
    public function index()
    {
        $count=M('Category')->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        $lists=M('Category')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        //p($lists);
        $this->assign('lists',$lists);
        $this->assign('show',$show);
        $this->meta_title = '保险类型列表';
        $this->display();
    }
    public function edit($id=0){
        if(IS_POST){
            $this->_edit();
            die;
        }
        $data=M('Category')->find($id);
        $lists=M('Category')->select();
        $this->data=$data;
        $this->treedata=getTree($lists);
        $this->display('info');
    }
    protected function _edit(){
        $Category=M('Category');
        if($Category->create()){
            if($Category->save()!==false){
                $this->success('编辑成功!',U('index'));
            }else{
                $this->error('编辑失败原因为:'.$Category->getDbError());
            }
        }else{
            $this->error('编辑失败原因为:'.$Category->getError());
        }
    }
    public function delete($id=0){
        if(M('Category')->delete($id)){
            $this->success('删除成功!',U('index'));
        }else{
            $this->error('删除失败');
        }

    }
}