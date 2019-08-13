<?php


// 导入相应的库
define('ROOT_PATH',dirname(__FILE__));
require ROOT_PATH.'/router.php';
require ROOT_PATH.'/core/Router.php';
//require 'core/Response.php';
require ROOT_PATH.'/core/Core.php';
require ROOT_PATH.'/core/common.php';
require ROOT_PATH.'/core/MySql.php';
Router::main();

exit;