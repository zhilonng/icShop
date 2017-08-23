<?php
namespace Admin\Controller;
class GoodsController extends AuthController
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
        $goodsType = M('GoodsType');
        if ($goodsType->create()) {
            //p($goods->create());
            if ($goodsTypeid = $goodsType->add()) {
                $gdata['goods_goods_type_id']=$goodsTypeid;
                $gdata['goods_caseCode']=I('goods_caseCode');
                $gdata['goods_ageLimit']=I('goods_ageLimit');
                M('Goods')->add($gdata);
                $this->success('添加成功', U('index'));
            } else {
                $this->error('添加失败，原因为：' . $goods->getDbError());
            }
        } else {
            $this->error('添加失败，原因为：' . $goods->getError());
        }
    }

    public function mulUpload()
    {
        // 设置存储路径
        $_POST['_rootpath'] = C('TMPL_PARSE_STRING.__UP_GOODS__');

        // 调用函数实现上传
        $res = upload();

        // 输出子目录及文件名
        echo date('Y-m-d') . '/' . $res['file']['savename'];

        //p($res);
        //file_put_contents( uniqid().'.txt', var_export($_FILES, true) );
    }

    public function index()
    {
        $count=M('GoodsType')->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        $lists=M('Goods_type')
        ->alias('gt')
        ->field('gt.*,huize_company.company_name,huize_category.category_name')
        ->join('__COMPANY__ on gt.goods_type_company=__COMPANY__.company_id')
        ->join('__CATEGORY__ on gt.goods_type_category=__CATEGORY__.category_id')
        ->order('goods_type_id')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        //p($lists);
        $this->assign('lists',$lists);
        $this->assign('show',$show);
        $this->meta_title = '产品列表';
        $this->display();
    }

    public function edit($goods_type_id = 0)
    {
        if (IS_POST) {
            $this->_edit();
            die;
        }
        $data  = M('GoodsType')->find($goods_type_id);
        $company=M('Company')->select();
        $category=M('Category')->select();
        $goods=M('Goods')->where("goods_goods_type_id=$goods_type_id")->find();
        //p($data);
        $this->goods=$goods;
        $this->company=$company;
        $this->category=$category;
        $this->data = $data;
        $this->display('add');
    }

    protected function _edit()
    {
        $goodstype = M('GoodsType');
        $goods=M('Goods');
        $goods_type_id=$_POST['goods_type_id'];
        if ($goodstype->create()) {
            $goods->where("goods_goods_type_id=$goods_type_id")->delete();
            //p($goodstype->create());
            $data['goods_goods_type_id']=$goods_type_id;
            $data['goods_caseCode']=$_POST['goods_caseCode'];
            $data['goods_ageLimit']=$_POST['goods_ageLimit'];
            //p($data);
            //更新是受影响行数 所以用false全等来判断
            if ($goodstype->save() !== false) {
                if($goods->add($data)){
                     //先把goods表删除重新入库
                $this->success('编辑成功', U('index'));
                }
            } else if($goods_id=$goods->add($data) && $goodstpye->save($goods_id)) {
                 $this->success('编辑成功', U('index'));
            }
        } else {
            $this->error('添加失败，原因为：' . $goods->getError());
        }
    }
    public function delete($goods_type_id=0)
    {
        //p($goods_type_id);
        if(M('GoodsType')->delete($goods_type_id)){
            if(M('Goods')->where("goods_goods_type_id=$goods_type_id")->delete()){
                 $this->success('删除成功!');
            }else{
                $this->error('删除失败!');
            }
        }

    }
}
