<?php
namespace Admin\Model;
use Think\Model;
class GoosTypeModel extends Model{
    protected $_auto=array(
        array('arrt_group','_autoAttrGroup',3,'callback'),
    );
    protected function _autoAttrGroup()
    {
        return isset($_POST['attr_group']) ? trim($_POST['attr_group']) :false;
    }
}