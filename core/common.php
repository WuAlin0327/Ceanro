<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-13
 * Time: 20:26
 */

function get_config($name=null){
    $config = require ROOT_PATH . '/config/settings.php';
    if (!empty($name)){

        $config = $config[$name];
    }
    return $config;
}