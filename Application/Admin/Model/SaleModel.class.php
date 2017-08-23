<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class SaleModel extends RelationModel {
    protected $_link = array(
        'order' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'order',
            'foreign_key'       => 'sale_order_id',
            'mapping_fields'    =>array('order_pic,order_style_number,order_fmoney,order_allmoney,order_salemoney'),
            ),
        'store' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'store',
            'foreign_key'       => 'sale_store',
            'mapping_fields'    =>'store_name',
            ),
    );
}
?>