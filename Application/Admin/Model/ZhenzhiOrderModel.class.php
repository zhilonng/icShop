<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ZhenzhiOrderModel extends RelationModel {
    protected $_link = array(
        'factory' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'factory',
            'foreign_key'       => 'order_factory',
            'mapping_fields'    =>'factory_name',
            ),
        'logo' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'logo',
            'foreign_key'       => 'order_logo',
            'mapping_fields'    =>'logo_name',
            ),
        'designer'=>array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'designer',
            'foreign_key'       => 'order_designer',
            'mapping_fields'    =>'designer_name',
            ),
        'paper'=>array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'paper',
            'foreign_key'       => 'order_paper',
            'mapping_fields'    =>'paper_name',
            ),

    );
}
?>