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
    public $mysql;
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
        $this->mysql = new MySql($this->db_prefix.$this->table);
        $this->fields = $this->mysql->get_fields();

    }


    /**
     * 根据ID取一条数据
     * @param $id 主键
     * @param $type 'array' || 'all' || 'assoc' || 'row' || 'object' || 'field'
     * @return array|null
     */
    public function find($id,$type='assoc'){
        $data = $this->mysql->exclude('select * from '.$this->db_prefix.$this->table.' where '.$this->id.'='.$id,$type);
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
        $sql = $fields = 'select * from '.$this->db_prefix.$this->table.$where;
        $db_fields = $this->mysql->exclude($fields,'fields');
        $data = $this->mysql->exclude($sql,'all');
        if ($struct == 'list[dict]'){
            foreach($data as $k=>$v){
                foreach($v as $kk=>$vv){
                    $data[$k][$db_fields[$kk]->name] = $vv;
                    unset($data[$k][$kk]);
                }
            }
        }
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
            if (isset($_GET[$v['field']])){
                $where [] =  ' '.$v['field'].' like \'%'.$_GET[$v['field']].'%\'';
            }
        }
        $where = implode(' and ',$where);
        return $where;
    }


    /**
     * 处理GET参数
     */
    public function params(){

    }

    /**
     * 处理$_POST参数
     */
    public function _post($key){
        $val = isset($_POST[$key])?$_POST[$key]:null;
        return $val;
    }

}