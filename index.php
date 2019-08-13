<?php

define('ROOT_PATH',dirname(__FILE__));
require ROOT_PATH.'/router.php';
require 'core/Router.php';
require 'core/Response.php';
require 'core/Core.php';
$s = 1;
Router::main();


exit;