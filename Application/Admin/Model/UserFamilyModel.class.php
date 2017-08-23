<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class UserFamilyModel extends RelationModel {
    protected $_link = array(

        'user' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'user',
            'foreign_key'       => 'user_id'
            ),
        'admin' => array(
            'mapping_type'      =>  self::BELONGS_TO,
            'class_name'        => 'admin',
            'foreign_key'       => 'report_admin_id'
            )
    );
}
?>