<?php
header('Content-Type:application/json; charset=utf-8');

// 导入相应的库
define('ROOT_PATH',dirname(__FILE__));
define('DEBUG',false);
ini_set('display_errors',DEBUG);
require_once ROOT_PATH.'/core/common.php';
require_once ROOT_PATH.'/core/Router.php';
require_once ROOT_PATH.'/router.php';
//require 'core/Response.php';
require_once ROOT_PATH.'/core/Core.php';
require_once ROOT_PATH.'/core/MySql.php';
require_once ROOT_PATH . '/core/DataBase.php';
Router::main();
exit;