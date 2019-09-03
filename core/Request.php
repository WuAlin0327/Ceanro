<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-07
 * Time: 23:11
 */

namespace core;
class Request
{

    private static $instance = null;
    public $method;
    public $domain;
    public $ip;
    //构造器私有化:禁止从类外部实例化
    private function __construct(){}
    //克隆方法私有化:禁止从外部克隆对象
    private function __clone(){}

    //因为用静态属性返回类实例,而只能在静态方法使用静态属性
    //所以必须创建一个静态方法来生成当前类的唯一实例
    public static function instance()
    {
        //检测当前类属性$instance是否已经保存了当前类的实例
        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();

            // 请求方法
            self::$instance->method= $_SERVER['REQUEST_METHOD'];

            // 完整域名
            $protocol = empty($_SERVER['HTTP_X_CLIENT_PROTO']) ? 'http://' : $_SERVER['HTTP_X_CLIENT_PROTO'] . '://';
            self::$instance->domain = $protocol.$_SERVER['SERVER_NAME'];

            // 请求ip
            self::$instance->ip =  $_SERVER['REMOTE_ADDR'];
        }

        //如果已经有了当前类实例,就直接返回,不要重复创建类实例
        return self::$instance;
    }

    public static function params($key){
        $val = isset($_REQUEST[$key])?$_REQUEST[$key]:null;
        return $val;
    }

    /**
     * 处理GET参数
     * @param $key
     * @return null
     */
    public function _get($key){
        return static::params($key);
    }

    /**
     * 处理$_POST参数
     * @param $key
     * @return null
     */
    public function _post($key){

        return isset($_POST[$key])?$_POST[$key]:null;
    }

    /**
     * 处理$_PUT参数
     * @param $key
     * @return |null
     */
    public function _put($key){
        $_PUT = get_put();
        return isset($_PUT[$key])?$_PUT[$key]:null;
    }

}