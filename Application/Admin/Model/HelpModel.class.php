<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class HelpModel extends RelationModel {
    protected $_link = array(
        'category' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'helpCategory',
            'foreign_key'       => 'cid'
            )
    );
}
?>