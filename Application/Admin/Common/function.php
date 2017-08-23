<?php
/**
 * 递归重组节点信息为多维数组
 * @param [type] $node  [要处理的节点数组]
 * @param integer $pid  [父级id]
 * @param [type] 
 */
function node_merge($node,$access = null, $pid =0){
	$arr = array();
	foreach($node as $val){
		if(is_array($access)){
			$val['access'] = in_array($val['id'], $access) ? 1 : 0;
		}
		if($val['pid'] == $pid){
			$val['child'] = node_merge($node, $access, $val['id']);
			$arr[] = $val;
		}
	}
	return $arr;
}


// 菜单组合一维数组
function menuForLevel($menu,$html= '----', $pid=0, $level = 0){
    $arr = array();
    foreach($menu as $val){
        if($val['pid'] == $pid){
            $val['level'] = $level + 1;
            $val['html'] = str_repeat($html, $level);
            $arr[] = $val;
            $arr = array_merge($arr,menuForLevel($menu, $html, $val['id'], $level+1));
        }
    }
    return $arr;
}

//菜单组合多维数组
function menuForLayer($menu, $name = 'child', $pid = 0){
    $arr = array();
    foreach ($menu as $val) {
        $val['name'] = urlencode($val['name']);
        if($val['pid'] == $pid){
            $val[$name] = menuForLayer($menu, $name, $val['id']);
            $arr[] = $val;
        }
    }
    return $arr;
}

// 分类组合一维数组
function categoryForLevel($cate,$html= '----', $pid=0, $level = 0){
    $arr = array();
    foreach($cate as $val){
        if($val['pid'] == $pid){
            $val['level'] = $level + 1;
            $val['html'] = str_repeat($html, $level);
            $arr[] = $val;
            $arr = array_merge($arr,categoryForLevel($cate, $html, $val['cid'], $level+1));
        }
    }
    return $arr;
}

//传递一个父级分类id返回所有子分类id
function getChildsId($cate, $pid){
    $arr = array();
    foreach($cate as $val){
        if($val['pid'] == $pid){
            $arr[] = $val['cid'];
            $arr = array_merge($arr, getChildsId($cate, $val['cid']));
        }
    }
    return $arr;
}

// 写入配置文件
function toConf($name, $value='', $path=CONF_PATH) {
    static $_cache = array();
    $filename = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            return unlink($filename);
        } else {
            // 缓存数据
            $dir = dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir)) mkdir($dir);
            $_cache[$name] = $value;
            return file_put_contents($filename, strip_whitespace("<?php\nreturn " . var_export($_cache, true) . ";\n?>"));
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // 获取缓存数据
    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }
    return $value;
}




function number($str){
    if($str[0] == '0'){
        return $str[1];
    }else{
        return $str;
    }
}
function getAdminName($id){
    $admin=M('admin')->where(array('admin_id'=>$id))->find();
    return $admin['username'];
}
//格式化查看变量

// function myupload()
// {
//     // 如果没有上传文件
//     if(! $_FILES['img']['size'])
//     {
//         return array();
//     }
//     $upload           = new \Think\Upload();
//     $upload->maxSize  = 3145728;
//     $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
//     $upload->rootPath = '/mydata/wwwroot/';
//     $upload->savePath = 'Public/uploads/Order/';
//     $upload->saveName =date('YmdHis').mt_rand(1000,9999);
//     $info   =   $upload->upload();
//     if(!$info)
//     {
//         // 上传错误提示错误信息
//         return $upload->getError();
//     }else
//     {
//         return date('Y-m-d').'/'.$info['img']['savename'];
//     }
// }
function getTree($list,$pid=0,$level=0)
{
    static $arr=array();
    if(is_null($list)){
        $arr=array();
        return $arr;
    }
    foreach($list as $v){
        if($v['category_pid']==$pid){
            $v['level']=$level;
            $arr[]=$v;
            getTree($list,$v['category_id'],$level+1);
        }
    }
    return $arr;
}

function allGetTree($list,$pid=0,$level=0)
{
    static $array=array();
    if(is_null($list)){
        $array=array();
        return $array;
    }
    foreach($list as $v){
        if($v['pid']==$pid){
            $v['level']=$level;
            $array[]=$v;
            allGetTree($list,$v['id'],$level+1);
        }
    }
    return $array;
}
function allGetTree1($list,$pid=0,$level=0)
{
    static $array1=array();
    if(is_null($list)){
        $array1=array();
        return $array1;
    }
    foreach($list as $v){
        if($v['pid']==$pid){
            $v['level']=$level;
            $array1[]=$v;
            allGetTree1($list,$v['id'],$level+1);
        }
    }
    return $array1;
}
