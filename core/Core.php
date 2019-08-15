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

    public $table ;
    public $id = 'id';
    public $db_prefix;
    public $db;
    public $fields;


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

    public function __construct()
    {
        $this->db_prefix = get_config('db_prefix');
        $this->db = new DataBase($this->db_prefix.$this->table);
        $this->fields = $this->db->get_fields();

    }


    /**
     * 根据ID取一条数据
     * @param $id 主键
     * @param $type 'array' || 'all' || 'assoc' || 'row' || 'object' || 'field'
     * @return array|null
     */
    public function find($id,$type='assoc'){
        $data = $this->db->exclude('select * from '.$this->db_prefix.$this->table.' where '.$this->id.'='.$id,$type);
        return $data;
    }

    /**
     * 查询函数
     * @param string $struct 格式化 list[dict]：列表套字典 list[list]二维数组
     * @return mixed
     */
    public function select($id=null,$struct='list[dict]'){


        if (!empty($id)){
            $data = $this->find($id,'assoc');
            return $data;
        }
        $where = $this->where(); // 将$_GET参数拼接成sql语句进行查询(模糊查询)
        $where = !empty($where)?' where '.$where:'';
        $sql = 'select * from '.$this->db_prefix.$this->table.$where;
        $data = $this->db->exclude($sql);
        return $data;
    }

    /**
     * 更新数据
     */
    public function update(){

    }

    /**
     * 插入数据
     */
    public function insert(){
        $arr = [];
        foreach($this->fields as $k=>$v){
            if ($v['extra'] == 'auto_increment')continue;

            $value = $this->_post($v['field']);

            if (!$v['null'] && empty($value)){
                $arr[$v['field']] = $v['field'].' is null!';
                continue;
            };
            $arr[$v['field']] = $value;
        }
//        echo json_encode($this->fields);
        echo json_encode($arr);
    }

    /**
     * 删除数据
     */
    public function remove(){

    }

    /**
     * 将Get参数拼接成sql的查询语句
     */

    public function where(){
        $where = [];
        foreach($this->fields as $k=>$v){
            $val = $this->_get($k);
            if (isset($val)){
                $where [] =  ' '.$k.' like \'%'.$val.'%\'';
            }
        }
        $where = implode(' and ',$where);
        return $where;
    }

    public static function params($key){
        $val = isset($_REQUEST[$key])?$_REQUEST[$key]:null;
        return $val;
    }

    /**
     * 处理GET参数
     */
    public function _get($key){
        return static::params($key);
    }

    /**
     * 处理$_POST参数
     */
    public function _post($key){

        return static::params($key);
    }

    /**
     * get请求
     * @param null $id
     * @return false|string
     */
    public function get($id=null){
        $data = $this->select($id);
        return $this->json($data);
    }

}