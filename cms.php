<?php
/**
 * @name cms.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-6
 * Encoding UTF-8
 */
defined('DS') or define('DS',DIRECTORY_SEPARATOR);
defined('CMS_DIR') or define('CMS_DIR',dirname(__FILE__).DS);
defined('CORE_DIR') or define('CORE_DIR',CMS_DIR.'core'.DS);
//path to yii framework
defined('YII_DIR') or define('YII_DIR',CMS_DIR.'../yii/framework'.DS);

//StripSlashes all GET, POST, COOKIE
if (get_magic_quotes_gpc()){
	function stripslashes_gpc(&$value){
		$value = stripslashes($value);
	}
	array_walk_recursive($_GET, 'stripslashes_gpc');
	array_walk_recursive($_POST, 'stripslashes_gpc');
	array_walk_recursive($_COOKIE, 'stripslashes_gpc');
}

require CORE_DIR.'components'.DS.'environment'.DS.'Environment.php';