<?php
namespace Admin\Model;
use Think\Model;
class AttributeModel extends Model{
    protected $_auto=array(
        array('arrt_values','_autoAttrValues',3,'callback'),
    );
    protected function _autoAttrValues()
    {
        return isset($_POST['attr_values']) ? trim($_POST['attr_values']) :false;
    }
}