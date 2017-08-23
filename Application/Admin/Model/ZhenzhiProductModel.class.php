<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ZhenzhiProductModel extends RelationModel {
    protected $_link = array(
        'order' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'order',
            'foreign_key'       => 'product_order_id',
            'mapping_fields'    =>array('order_factory,order_style_number,order_date,order_fmoney'),
            ),       
    );
}
?>