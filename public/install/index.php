<?php
/*
 * 安装文件
 */
@set_time_limit(1000);
error_reporting(E_STRICT);
define('NBNBKCMS_INSTALL', 1);
header('Content-Type:text/html;charset=UTF-8');
$version = '5.4.0';
$phpversion = phpversion();

if($phpversion < $version) exit('您的php版本过低，不能安装本软件，请升级到5.4.0或更高版本再安装，谢谢！');

include 'inc/install.lang.php';
if (file_exists('install.lock')) {
	exit('您已经安装过本软件,如果需要重新安装，请删除 ./public/install/install.lock 文件！');
}
$step = isset($_GET['step'])? intval($_GET['step']) : 1;

if (empty($step)) {
	$step = 1;
}
switch ($step) {
	case 1://协议
		$license = file_get_contents("./license.txt");
		require 'tpl/step_1.php';
		break;
	case 2:
		/* 环境检测 */
		$software = explode('/',$_SERVER["SERVER_SOFTWARE"]);
		$os_software = '<span class="ok">'.PHP_OS.'--'.$software[0].'/'.str_replace('PHP', '', $software[1]).'</span>';
		/* mysql */
		$mysql_lowest = '5.6.0';
		if (function_exists('mysqli_connect')) {
			$environment_mysql = '<span class="ok">'.$lang['install_on'].'</span>';
		} else {
			$environment_mysql = '<span class="no red">&nbsp;</span>';
		}
		/* session_start */
		if (function_exists('session_start')) {
			$environment_session = '<span class="ok">'.$lang['install_on'].'</span>';
		} else {
			$environment_session = '<span class="no red">'. $lang['unsupport'] .'</span>';
		}
		/* uploads */
		$environment_upload = ini_get('file_uploads') ? '<span class="ok">'.ini_get('upload_max_filesize').'</span>' : '<span class="no red">&nbsp;</span>';
		
		/* scandir */
		if(function_exists('scandir')){
            $environment_scandir = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_scandir = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }
		/* iconv */
		if(function_exists('iconv')){
            $environment_iconv = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_iconv = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }
        /* GD */
        if(extension_loaded('gd')) {
            $environment_gd = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_gd = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }
        /* mbstring */
        if(extension_loaded('mbstring')) {
            $environment_mb = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_mb = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }
		/* 文件权限检测 */
		$file = array(
			'/',
			'/install',
			'/uploads',
			'../runtime',
			'../application',
		);
		require 'tpl/step_2.php';
		break;
	case 3:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($_POST['DB_HOST'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_mysql_host_empty'],'input'=>'DB_HOST')));
			}
			if (empty($_POST['DB_PORT'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_mysql_port_empty'],'input'=>'DB_PORT')));
			}
			if (empty($_POST['DB_USER'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_mysql_username_empty'],'input'=>'DB_USER')));
			}
			if (empty($_POST['DB_NAME'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_mysql_name_empty'],'input'=>'DB_NAME')));
			}
			if (empty($_POST['DB_PREFIX'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_mysql_prefix_empty'],'input'=>'DB_PREFIX')));
			}
			if (empty($_POST['SITE_URL'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['site_url_empty'],'input'=>'WEB_URL')));
			}
			if (empty($_POST['username'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_founder_name_empty'],'input'=>'username')));
			}
			if (empty($_POST['password'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['founder_invalid_password'],'input'=>'password')));
			}
			if (strlen($_POST['password']) < 6) {
				exit(json_encode(array('status'=>'error','info'=>$lang['founder_invalid_password'],'input'=>'password')));
			}
			/*if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				exit(json_encode(array('status'=>'error','info'=>$lang['email_failed'],'input'=>'email')));
			}*/
			$db =new mysqli($_POST['DB_HOST'],$_POST['DB_USER'],$_POST['DB_PWD']);
			if ($db->connect_errno) {
				exit(json_encode(array('status'=>'error','info'=>$lang['database_connection_failed'].','.$lang['error_message']. $db->connect_error)));
			}
			if (!$db->select_db($_POST['DB_NAME'])) {
				$result = $db->query("CREATE DATABASE `".$_POST['DB_NAME']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
				if (!$result) {
					exit(json_encode(array('status'=>'error','info'=>$lang['database_create_failed'].','.$lang['error_message'].$db->error,'input'=>'DB_NAME')));
				}
			}
			$db->close();
			//右边的/一定要去除
			$_POST['SITE_URL'] = rtrim($_POST['SITE_URL'],'/');
			$_POST['add_test'] = isset($_POST['add_test']) ? intval($_POST['add_test']) : 0;
			$content = var_export($_POST,true);
			if (file_put_contents('temp.php',"<?php\r\nreturn " .$content."\r\n?>")) {
				exit(json_encode(array('status'=>'success')));
			} else {
				exit(json_encode(array('status'=>'error','info'=>$lang['write_tmp_failed'])));
			}
			//判断配置目录是否可写
			if (!is_writable("../application")) {
				exit(json_encode(array('status'=>'error','info'=>$lang['conf_not_write'])));
			}
		} else {
			 if(!empty($_SERVER['REQUEST_URI']))
		    	$scriptName = $_SERVER['REQUEST_URI'];
		    else
		    	$scriptName = $_SERVER['PHP_SELF'];

		    $basepath = preg_replace("#\/install(.*)$#i", '', $scriptName);

		    if(!empty($_SERVER['HTTP_HOST']))
		        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
		    else
		        $baseurl = "http://".$_SERVER['SERVER_NAME'];
		    $weburl = rtrim("http://".$_SERVER['SERVER_NAME'].$basepath,'/');
		
			require 'tpl/step_3.php';
			}
			break;
	case 4:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$setting = include './temp.php';
			//$datafile = $setting['add_test'] == 1 ? './inc/nbnbkcms_data.sql' : './inc/nbnbkcms.sql';
			$datafile = './inc/nbnbkcms.sql';
			
			$content = file_get_contents($datafile);//带演示
			if (empty($setting)) {				
				exit(json_encode(array('status'=>'error','info'=>$lang['load_failed_reinstall'])));
			}
            
			$cookie_code = get_randomstr(9);
			//去掉注释
			$content=preg_replace('/\/\*[\w\W]*?\*\//', '', $content);
       		$content=preg_replace('/-- ----------------------------[\w\W]*?-- ----------------------------/', '', $content);
			$content = str_replace(
					array('#none#_','#site_url#','#site_name#','#site_theme#','#web_cookie_code#',"\r"),
					array($setting['DB_PREFIX'],$setting['SITE_URL'],$setting['SITE_NAME'],$setting['SITE_THEME'],$cookie_code,"\n"), 
					$content);


			$content = explode(";\n", $content);
			$installNum = count($content);
			$connect = new mysqli($setting['DB_HOST'],$setting['DB_USER'],$setting['DB_PWD'], $setting['DB_NAME']);
			if ($connect->connect_errno) {
				exit(json_encode(array('status'=>'error','info'=>$lang['database_connection_failed'].','.$lang['error_message'].$connect->connect_error)));
			} 

			$connect->query("SET NAMES UTF8");
			$forNum = 0;
			$info = '';
			//exit(json_encode(array('status'=>$status,'info'=>var_export($content,true),'num'=>$forNum)));

			//explode() 函数把字符串分割为数组。
 
			foreach ($content as $tempsql) {
				$forNum++;
				$tempsql = trim($tempsql);
				if (empty($tempsql)) continue;

				$tempArray = explode("\n", $tempsql);
				$sql = '';
				foreach ($tempArray as $query) {
					$sql .= $query;
				}
				if (empty($sql)) continue;

				preg_match('/create\s+table.*\`(.*)\`.*/Ui',$sql, $match);
				$flagOfTable = false;
				if (isset($match[1]) && !empty($match[1])) {
					$tableName = $lang['data_table'].$match[1].'！';
					$flagOfTable = true;
				} else {
					preg_match('/insert\s+into\s+\`(.*)\`.*/iU',$sql,$match);
					if (isset($match[1]) && !empty($match[1])) {
						$tableName = $lang['write_data_table'].$match[1];
					} else {
						$tableName = '';
					}
				}
				$result = $connect->query($sql.';');
				if (!$result) {
					$status = 'error';
					$info .= $lang['install'].$tableName.$lang['faile'].','.$lang['error_message'].$connect->error.'<br/>';
					//错误直接返回
					exit(json_encode(array('status'=>$status,'info'=>$info ,'num'=>$forNum)));
				} else {
					$status = 'success';
					if ($flagOfTable) {
						$info .= $lang['installation_successful'].$tableName.'<br/>';
					}
					$flagOfTable = false;
					
				}
			}
			
            //释放变量
            unset($content);
			
			//添加管理员
			/* $time = time();
			$ip = getip();

			$passwordinfo = get_password($setting['password']);
			$password = $passwordinfo['password'];
			$encrypt = $passwordinfo['encrypt'];

			$result = $connect->query("INSERT INTO `{$setting['DB_PREFIX']}admin` (`name`,`pwd`,`usertype`,`logintime`,`loginip`,`islock`) VALUES ('{$setting['username']}','$password','$encrypt',9,'$time','$ip',0);");
			$insertId = $connect->insert_id;
			if (!$result || !$insertId) {
				exit(json_encode(array('status'=>'error','info'=>$lang['create_administrator_failed'].','.$lang['error_message'].$connect->error.','.$lang['please_refresh_installation'])));
			} */
			
			$connect->close();
		
			$status = 'success_all';
			$info .=$lang['installation_successful'];

			exit(json_encode(array('status'=>$status,'info'=>$info,'num'=>$forNum)));
		} 
		require 'tpl/step_4.php';
		break;
	case 5:
		$setting = require './temp.php';
		/* 修改配置文件 */
		//定义数组
		$db = [
				'type' => 'mysql',
				'hostname' => $setting['DB_HOST'],
				'username' => $setting['DB_USER'],
				'password' => $setting['DB_PWD'],
				'database' => $setting['DB_NAME'],
				'prefix' => $setting['DB_PREFIX'],
				'dsn'            => '',
				'charset'        => 'utf8',
				'fields_strict'  => false,
			];
        
		$dbStr="<?php return " . var_export($db,true) . ";?>";			
		file_put_contents('../../config/database.php', $dbStr);//写文件
		//copy('./inc/conf/config.php', '../../config/app.php');
        
		//删除临时文件
		@unlink('temp.php');
		//删除缓存
		del_dir_file('../runtime',false);
		/* 设置安装完成文件 */
		file_put_contents('install.lock', time());
		require 'tpl/step_5.php';
		break;
		default:
		require 'tpl/step_1.php';
}

function getip(){
	if(isset ($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif(isset ($_SERVER['HTTP_CLIENT_IP'])){
		$onlineip = $_SERVER['HTTP_CLIENT_IP'];
	}else{
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	$onlineip = preg_match('/[\d\.]{7,15}/', addslashes($onlineip), $onlineipmatches);
	return $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
}

function format_textarea($string) {
	$chars = 'utf-8';
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string,ENT_COMPAT,$chars)));
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function get_password($password, $encrypt='') {
    $pwd = array();
    $pwd['encrypt'] =  $encrypt ? $encrypt : get_randomstr();
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}

/**
 * 生成随机字符串
 */
function get_randomstr($length = 6) {
	$chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
	$hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

//循环删除目录和文件函数
function del_dir_file($dirName, $bFlag = true ) {
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					del_dir_file( "$dirName/$item" );
				} else {
					@unlink( "$dirName/$item" );
				}
			}
		}
		closedir( $handle );
		if($bFlag) rmdir($dirName);
	}
}
?>