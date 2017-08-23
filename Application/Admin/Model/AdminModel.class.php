<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class AdminModel extends RelationModel {
    protected $_link = array(
        'role' => array(
            'mapping_type'      =>  self::MANY_TO_MANY,
            'class_name'        => 'role',
            'foreign_key'       => 'user_id',
            'relation_foreign_key'  =>  'role_id',
            'relation_table'    =>  'b_role_user'
            )
    );
}
?>