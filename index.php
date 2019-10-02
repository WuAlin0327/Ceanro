<?php

header('Content-Type: json;charset=utf-8');
header('Access-Control-Allow-Origin:http://localhost:8080'); // *代表允许任何网址请求
header('Access-Control-Allow-Methods:POST,GET,DELETE,PUT,OPTIONS'); // 允许请求的类型
header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段


// 导入相应的库
define('ROOT_PATH',dirname(__FILE__));
define('DEBUG',true);
ini_set('display_errors',DEBUG);


// 加载核心模块
require_once ROOT_PATH.'/core/common.php';
require_once ROOT_PATH.'/core/Router.php';
require_once ROOT_PATH.'/router.php';
require_once ROOT_PATH.'/core/Request.php';
require_once ROOT_PATH.'/core/Response.php';
require_once ROOT_PATH.'/core/Config.php';
require_once ROOT_PATH.'/core/Core.php';
require_once ROOT_PATH.'/core/MySql.php';
require_once ROOT_PATH . '/core/DataBase.php';
require_once ROOT_PATH . '/core/Session.php';

Router::main();
exit;