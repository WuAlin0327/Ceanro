<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-07
 * Time: 23:30
 */

namespace core\Core;
class Core
{
    public function config(){
        $config = require ROOT_PATH.'/settings.php';
        return $config;
    }
}