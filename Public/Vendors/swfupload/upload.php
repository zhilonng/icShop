<?php
/**
 * swfupload文件上传处理
 *
 * @author 林坤源
 * @link http://www.lamson.cc
 */
error_reporting ( E_ALL  ^  E_NOTICE );
header('Content-Type:text/html;charset=utf-8');	//设置网页编码

// Code for Session Cookie workaround
if (isset($_POST['PHPSESSID'])) {
	session_id($_POST['PHPSESSID']);
} else if (isset($_GET['PHPSESSID'])) {
	session_id($_GET['PHPSESSID']);
}

session_start();

$POST_MAX_SIZE = ini_get('post_max_size');
$unit = strtoupper(substr($POST_MAX_SIZE, -1));
$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
	header('HTTP/1.1 500 Internal Server Error');
	echo 'POST exceeded maximum allowed size.';
	exit(0);
}

// （1） 设置Application和ThinkPHP的路径
define('APP_PATH', __DIR__ . '/../../../Application/');
define('THINK_PATH', APP_PATH . '../ThinkPHP/');
define('FROM_VENDOR', true);
/* Add by Lwj REASON:PARSE_ERR undefined the function is_mobile_request*/

// （2）加载TP的一些功能文件
require THINK_PATH . 'Common/functions.php';
@$CFG = include(APP_PATH . 'Common/Conf/config.php');

// （3）定义保存路径
// Settings
$save_path = APP_PATH . '../Public/Uploads/Goods/' . date('ym') . '/';

if( ! is_dir($save_path))
{
	@ mkdir($save_path, 0777, true);	
}
$upload_name = 'Filedata';
$max_file_size_in_bytes = 2147483647;				// 2GB in bytes
$extension_whitelist = array('jpg', 'jpeg', 'gif', 'png');	//允许的文件
//$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				//允许的文件名字符
//$valid_chars_regex = '.A-Z0-9';

// Other variables	
//$MAX_FILENAME_LENGTH = 100;
$file_name = '';
$file_extension = '';
$uploadErrors = array(
	0=>'文件上传成功',
	1=>'上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置',
	2=>'上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置',
	3=>'上传的文件仅为部分文件',
	4=>'没有文件上传',
	6=>'缺少临时文件夹'
);

if (!isset($_FILES[$upload_name])) {
	HandleError('No upload found in \$_FILES for ' . $upload_name);
	exit(0);
} else if (isset($_FILES[$upload_name]['error']) && $_FILES[$upload_name]['error'] != 0) {
	HandleError($uploadErrors[$_FILES[$upload_name]['error']]);
	exit(0);
} else if (!isset($_FILES[$upload_name]['tmp_name']) || !@is_uploaded_file($_FILES[$upload_name]['tmp_name'])) {
	HandleError('Upload failed is_uploaded_file test.');
	exit(0);
} else if (!isset($_FILES[$upload_name]['name'])) {
	HandleError('File has no name.');
	exit(0);
}

$file_size = @filesize($_FILES[$upload_name]['tmp_name']);
if (!$file_size || $file_size > $max_file_size_in_bytes) {
	HandleError('File exceeds the maximum allowed size');
	exit(0);
}

if ($file_size <= 0) {
	HandleError('File size outside allowed lower bound');
	exit(0);
}


/*
$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', '', basename($_FILES[$upload_name]['name']));
if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
	HandleError('文件名不符合要求');
	exit(0);
}
*/

/*if (file_exists($save_path . $file_name)) {
	HandleError('同名文件已存在');
	exit(0);
}*/

// Validate file extension
$path_info = pathinfo($_FILES[$upload_name]['name']);
$file_extension = $path_info['extension'];
$is_valid_extension = false;
foreach ($extension_whitelist as $extension) {
	if (strcasecmp($file_extension, $extension) == 0) {
		$is_valid_extension = true;

		break;
	}
}
if (!$is_valid_extension) {
	HandleError('文件扩展名不符合要求');
	exit(0);
}
 
$file_name = $_SERVER['REQUEST_TIME'] . mt_rand(100, 999) . '.' . $file_extension;

if (!@move_uploaded_file($_FILES[$upload_name]['tmp_name'], $save_path.$file_name)) {
	HandleError('文件无法保存.');
	exit(0);
}

/**
 * 生成小中大三级缩略图
 */

// （4）加载缩略图所在的类
require THINK_PATH . 'Library/Think/Image.class.php';
require THINK_PATH . 'Library/Think/Image/Driver/Gd.class.php';

//生成缩略图
$image = new \Think\Image(); 
//将原图限制成最大宽度
$image->open($save_path.$file_name);
// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
$image->thumb($CFG['thumb']['orgw'], $CFG['thumb']['orgh'],\Think\Image::IMAGE_THUMB_FILLED)->save($save_path.$file_name);

// 文件名（不包含扩展名）
$fname = pathinfo($file_name, PATHINFO_FILENAME);

// 生成大中小三张缩略图
foreach($CFG['thumb']['spec'] as $v)
{
	$image->open($save_path.$file_name);
	$image->thumb($CFG['thumb']["{$v}w"], $CFG['thumb']["{$v}h"],\Think\Image::IMAGE_THUMB_FILLED)->save($save_path."{$fname}_{$v}". '.jpg');

}

$arr = pathinfo(realpath($save_path.$file_name));
$dir = explode(DIRECTORY_SEPARATOR, $arr['dirname']);
exit( array_pop($dir) . '/' . $arr['basename']);


function HandleError($message) {
header('HTTP/1.1 500 Internal Server Error');
echo $message;
}