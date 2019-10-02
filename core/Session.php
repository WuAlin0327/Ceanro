<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-09-10
 * Time: 22:39
 */

namespace core;

use core\DataBase;
class Session
{
    private static $set_count = 0;
    private static $cookie_value;
    private static $instance = null;
    private function __construct(){}
    private function __clone(){}

    /**
     * @return Session|null
     */
    public static function instance()
    {
        if (self::$instance == null) {
            if (!session_id()) session_start();
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set($key,$value){
        $_SESSION[$key] = $value;
    }

    /**
     * @param null $key
     * @return mixed
     */
    public function get($key=null){
        return !$key?$_SESSION:$_SESSION[$key];
    }

    /**
     *
     */
    public function clear(){
        session_destroy();
    }
    /**
     *
     */
    public function remove($key){
        unset($_SESSION[$key]);
    }
}