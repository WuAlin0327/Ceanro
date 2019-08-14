<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-07
 * Time: 23:30
 */

namespace core;
class Core
{

    public $table;
    public $id = 'id';


    /**
     * 返回Json数据
     * @param $data 需要格式化的数据
     * @param null $callback 回调参数
     * @param int $status 响应状态
     * @return false|string 返回json字符串
     */
    public function json($data,$callback=null,$status=200){
        $str = json_encode($data);
        if (!empty($callback)){
            $str = $callback.'('.$str.')';
        }
        $config = get_config('http_response');
        header($config[$status]);
        return $str;
    }

    /**
     * 根据ID取一条数据
     * @param $id 主键
     * @param $type 'array' || 'all' || 'assoc' || 'row' || 'object' || 'field'
     * @return array|null
     */
    public function find($id,$type='assoc'){
        $db_prefix = get_config('db_prefix');
        $mysql = new MySql();
        $data = $mysql->exclude('select * from '.$db_prefix.$this->table.' where '.$this->id.'='.$id,$type);
        return $data;
    }

}