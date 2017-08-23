<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class DetailModel extends RelationModel {
    protected $_link = array(
        'color' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'color',
            'foreign_key'       => 'detail_color',
            'mapping_fields'    =>'color_name',
            ),
        'size' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'size',
            'foreign_key'       => 'detail_size',
            'mapping_fields'    =>'size_name',
            ),

    );
}
