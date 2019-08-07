<?php


require 'core/Router.php';
require './router.php';
require 'core/Response.php';
require 'core/Core.php';
define('ROOT_PATH',dirname(__FILE__));

$config = require 'settings.php';

$paths = register();
Router::main($paths);


exit;