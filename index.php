<?php
header('Content-Type:application/json; charset=utf-8');
// 导入相应的库
define('ROOT_PATH',dirname(__FILE__));
require_once ROOT_PATH.'/router.php';
require_once ROOT_PATH.'/core/Router.php';
//require 'core/Response.php';
require_once ROOT_PATH.'/core/Core.php';
require_once ROOT_PATH.'/core/common.php';
require_once ROOT_PATH.'/core/MySql.php';
Router::main();
exit;