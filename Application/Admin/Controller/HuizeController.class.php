<?php
namespace Admin\Controller;
class HuizeController extends AuthController
{
    public function add()
    {
        if (IS_POST) {
            $this->_add();
            die;
        }
        $gtdata = M('GoodsType')->select();
        //p($gtdata);
        $lists        = M('Category')->select();
        $lists        = getTree($lists);
        $this->gtdata = $gtdata;
        $this->lists  = $lists;
        $this->assign('acttxt', '添加');
        $this->display('info');

    }

    protected function _add()
    {
        //p($_POST);
        unset($_FILES['file']);
        $goods = D('Goods');
        $res   = upload();
        if (is_string($res)) {
            //($res);
            $this->error('文件上传失败，原因为' . $res);
        }
        if ($goods->create()) {
            //p($goods->create());
            if ($goods_id = $goods->add()) {
                $GoodsAttr = M('GoodsAttr');
                $arr       = array();
                foreach ($_POST['attr_value'] as $k => $v) {
                    if (is_string($v) && trim($v) == '') {
                        continue;
                    }
                    if (is_array($v)) {
                        foreach ($v as $ko => $vo) {
                            $data  = array(
                                'goods_id' => $goods_id, //商品ID
                                'attr_id' => $k,  //属性ID
                                'attr_value' => $vo, //属性可选值
                                'attr_price' => $_POST['attr_price'][$k][$ko], //属性价格
                            );
                            $arr[] = $GoodsAttr->create($data);
                        }
                    } else {
                        $data  = array(
                            'goods_id' => $goods_id,
                            'attr_id' => $k,
                            'attr_value' => $v,
                            'attr_price' => 0,
                        );
                        $arr[] = $GoodsAttr->create($data);
                    }
                }
                $arr && $GoodsAttr->addAll($arr);
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
        $count=M('User')->count();
        $Page = new \Think\Page($count,50);
        $show = $Page->show();
        $lists=M('User')->order('user_addtime')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('lists',$lists);
        $this->assign('show',$show);
        $this->meta_title = '用户信息';
        $this->display();
    }

    public function edit($goods_id = 0)
    {
        if (IS_POST) {
            $this->_edit();
            die;
        }
        //p($goods_id);
        $data         = M('Goods')->find($goods_id);
        $lists        = M('Category')->select();
        $gtdata       = M('GoodsType')->select();
        $lists        = getTree($lists);
        $this->lists  = $lists;
        $this->gtdata = $gtdata;
        //p($data);
        $this->data = $data;
        $this->display('info');
    }

    protected function _edit()
    {
        $goods = M('Goods');
        M('GoodsAttr')->where("goods_id='$_POST[goods_id]")->delete();
        if ($goods->create()) {
            //更新是受影响行数 所以用false全等来判断
            if ($goods_id=$goods->save() !== false) {
                $GoodsAttr = M('GoodsAttr');
                //先把goods表删除重新入库
                $arr = array();
                foreach ($_POST['attr_value'] as $k => $v) {
                    if (is_string($v) && trim($v) == '') {
                        continue;
                    }
                    if (is_array($v)) {
                        foreach ($v as $ko => $vo) {
                            $data  = array(
                                'goods_id' => $goods_id, //商品ID
                                'attr_id' => $k,  //属性ID
                                'attr_value' => $vo, //属性可选值
                                'attr_price' => $_POST['attr_price'][$k][$ko], //属性价格
                            );
                            $arr[] = $GoodsAttr->create($data);
                        }
                    } else {
                        $data  = array(
                            'goods_id' => $goods_id,
                            'attr_id' => $k,
                            'attr_value' => $v,
                            'attr_price' => 0,
                        );
                        $arr[] = $GoodsAttr->create($data);
                    }
                }
                $arr && $GoodsAttr->addAll($arr);
                $this->success('编辑成功', U('index'));
            } else {
                $this->error('编辑失败，原因为：'. $goods->getDbError());
            }
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