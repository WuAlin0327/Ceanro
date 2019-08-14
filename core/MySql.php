<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-13
 * Time: 20:43
 */

namespace core;
class MySql
{
    public $link;
    public $table;
    public function __construct($table)
    {
        $config = get_config('mysql');
        $link = mysqli_connect($config['host'],$config['user'],'',$config['database'],$config['port']);
        if (!$link){
            die('连接数据库出错:'.mysqli_error($link));
        }
//        echo 'Mysql连接成功';
        $this->link = $link;
        mysqli_query($link,'set names utf8');
        $this->table = $table;
    }

    /**
     * 关闭mysql数据库链接
     * @param $link
     */
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
        mysqli_free_result($result);
        return $data;
    }

    /**
     * 返回数据库表结构
     * field : 字段名
     * type : 字段类型
     * null : 是否为空
     * key :
     * default : 默认
     * extra : 备注
     * @return array|null
     * @return
     */
    public function get_fields(){
//        $result = mysqli_query($this->link,'select * from '.$this->table.' limit 0,1');
//        $fields  = mysqli_fetch_fields($result);

        $result = mysqli_query($this->link,'SHOW COLUMNS FROM '.$this->table);
        $fields = mysqli_fetch_all($result);
        foreach($fields as $k=>$v){
            $fields[$k] = [
                'field'=>$v[0],
                'type'=>$v[1],
                'null'=>$v[2]=='NO'?false:true,
                'key'=>$v[3],
                'default'=>$v[4],
                'extra'=>$v[5]
            ];
        }
        return $fields;
    }

}