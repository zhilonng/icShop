<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ArticleModel extends RelationModel {
    protected $_link = array(
        'category' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'category',
            'foreign_key'       => 'cid'
            ),
        'admin' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'admin',
            'foreign_key'       => 'uid'
            )
    );
}
?>