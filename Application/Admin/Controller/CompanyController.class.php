<?php
namespace Admin\Controller;
class CompanyController extends AuthController
{
    public function add()
    {
        if (IS_POST) {
            $this->_add();
            die;
        }
        $company=M('Company')->select();
        $category=M('Category')->select();
        /*$gtdata = M('GoodsType')->select();
        $lists        = M('Category')->select();
        $this->gtdata = $gtdata;
        $this->lists  = $lists;*/
        $this->category=$category;
        $this->company=$company;
        $this->assign('acttxt', '添加');
        $this->display('add');

    }

    protected function _add()
    {
        //p($_POST);
        $company = M('Company');
        //p($company->create());
        if ($company->create()) {
            $where['company_name']=I('company_name');
            //p($goods->create());
            if($company->where($where)->find()){
                $this->error('添加失败，已经存该保险公司名称');die;
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
        $count=M('Company')->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        $lists=M('Company')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        //p($lists);
        $this->assign('lists',$lists);
        $this->assign('show',$show);
        $this->meta_title = '保险公司列表';
        $this->display();
    }

    public function edit($company_id = 0)
    {
        if (IS_POST) {
            $this->_edit();
            die;
        }
        $data         = M('Company')->find($company_id);
        $this->data = $data;
        $this->display('add');
    }

    protected function _edit()
    {
        //p(I('post.'));
        $company = M('Company');
        if ($company->create() && $company->save()) {
         
        } else {
            $this->error('添加失败，原因为：' . $goods->getError());
        }
    }
    public function delete($goods_id=0)
    {
        if(M('Goods')->delete($goods_id)){
            if(M('GoodsAttr')->where("goods_id='$_GET[goods_id]'")->delete()){
                 $this->success('删除成功!');
            }else{
                $this->error('删除失败!');
            }
        }

    }
}