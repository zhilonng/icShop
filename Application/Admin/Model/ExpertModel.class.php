<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ExpertModel extends RelationModel {
    protected $_link = array(
        'level' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'expert_level',
            'foreign_key'       => 'level_id'
            )
    );
}
?>