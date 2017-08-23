<?php
namespace Api\Model;
use Think\Model;
class AttributeModel extends AdvModel{
	public function __construct()
	{
		$this->addConnect(C('MAMI'),1);
		$this->switchConnect(1);
	}
    // protected $_auto=array(
    //     array('arrt_values','_autoAttrValues',3,'callback'),
    // );
    // protected function _autoAttrValues()
    // {
    //     return isset($_POST['attr_values']) ? trim($_POST['attr_values']) :false;
    // }
}