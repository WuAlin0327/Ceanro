<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-09-03
 * Time: 21:34
 */

namespace core;

class Config
{
    private static $instance = null;
    public $config;

    private function __construct(){}

    private function __clone(){}


    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
            self::$instance->config =  require ROOT_PATH . '/config/settings.php';
        }
        return self::$instance;
    }

    /**
     * 根据key取配置
     * @param $key
     * @return mixed
     */
    public function get($key){
        return self::$instance->config[$key];
    }

    /**
     * 设置配置
     * @param $key
     * @param $value
     */
    public function set($key,$value){
        self::$instance->config[$key] = $value;
    }
}