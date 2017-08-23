<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class ZhenzhiRedoModel extends RelationModel {
    protected $_link = array(
			'detail' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'detail',
            'foreign_key'       => 'redo_detail_id',
   			 ),			
			'order' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'order',
            'foreign_key'       => 'redo_order_id',
            'mapping_fields'    =>array('order_id,order_factory,order_designer,order_paper,order_style_number,order_category'),
   			 )

	);
}
?>