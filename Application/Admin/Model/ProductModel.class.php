<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ProductModel extends RelationModel {
    protected $_link = array(
        'cate' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'category',
            'foreign_key'       => 'cid'
            ),
    );
}
?>