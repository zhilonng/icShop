<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ServiceOrderModel extends RelationModel {
    protected $_link = array(
        'service' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'service',
            'foreign_key'       => 'service_id'
            ),
        'user' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'user',
            'foreign_key'       => 'user_id'
            ),
        'expert' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'expert',
            'foreign_key'       => 'expert_id'
            )
    );
}
?>