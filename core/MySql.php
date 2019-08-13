<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-13
 * Time: 20:43
 */

namespace core\Core;


class MySql
{
    public $link;
    public function __construct()
    {
        $config = get_config('mysql');
        $link = mysqli_connect($config['host'],$config['user'],'',$config['database'],$config['port']);
        if (!$link){
            die('连接数据库出错:'.mysqli_error($link));
        }
//        echo 'Mysql连接成功';
        $this->link = $link;
        mysqli_query($this->link,'set names utf8');

    }

    public function close($link){
        mysqli_close($link);
    }

    /**
     * @param $sql
     * @param string $type 'array' || 'all' || 'assoc' || 'row' || 'object' || 'field'
     * @return mixed
     */
    public function exclude($sql,$type='all'){
        $result = mysqli_query($this->link,$sql);
        $func = 'mysqli_fetch_'.$type;
        $data = call_user_func($func,$result);
        return $data;
    }
}