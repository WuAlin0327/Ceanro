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
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set($key,$value){
        $db = DataBase::instance();
//        $db->table('db_session')->insert_set();
        $old_key = self::instance()->get_key();
        $session = self::instance()->get();
        $session[$key] = $value;
        $value = base64_encode(json_encode($session));

        $cookie_value = md5(time());
        if (empty($old_key)){
            $db->table('db_session')->fields(['`key`','`value`'])->insert_set([
                '`key`'=>$cookie_value,
                '`value`'=>$value
            ])->insert();
        }else{
            $db->table('db_session')->update_set([
                '`value`'=>$value
            ])->where(['`key`'=>$old_key])->update();
        }

        self::$cookie_value = $cookie_value;
        setcookie(md5('CeanroSession'),$cookie_value);
    }

    public function get_key(){
        $cookie = !empty($_COOKIE[md5('CeanroSession')])?$_COOKIE[md5('CeanroSession')]:self::$cookie_value;
        return $cookie;
    }


    /**
     * @return array
     */
    public function get(){
        $key = self::instance()->get_key();

        if (empty($key)){
            return [];
        }
        $session = table('db_session')->fields('`value`')->where(['`key`'=>$key])->select();
        $session = json_decode(base64_decode($session['value']),true);
        return $session;
    }

}