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
    public $page_size = 20;
    public $one_to_one = [

    ];
    public $foreign_key = [

    ];
    public function __construct()
    {
        $this->db_prefix = get_config('db_prefix');
        $this->table = $this->db_prefix.$this->table;
        $this->db = DataBase::instance($this->table);
        $this->fields = $this->db->get_fields();

    }


    /**
     * 根据ID取一条数据
     * @param $id 主键
     * @param $type list[object] || list[list] || list[dict] || all || row
     * @return array|null
     */
    public function find($id,$type='list[object]'){
        $data = $this->db->exclude('select * from '.$this->table.' where '.$this->id.' = ?',[$id]);
        if (!$data)return null;
        return $data[0];
    }

    /**
     * 查询函数
     * @param string $struct 格式化 list[dict]：列表套字典 list[list]二维数组
     * @return mixed
     */
    public function select($id=null,$struct='list[dict]'){


        if (!empty($id)){
            $data = $this->find($id);
            return $data;
        }
        $page = _get('page');
        $page = !empty($page)?$page:1;
        $offset = ($page-1) * $this->page_size;

        $where = $this->where(); // 将$_GET参数拼接成sql语句进行查询(模糊查询)
        $where = !empty($where)?' where '.$where:'';
//        $sql = 'select * from '.$this->table.$where." limit {$offset},{$this->page_size}";
        $sql = "select * from {$this->table}{$where} limit {$offset},{$this->page_size}";
        $data = $this->db->exclude($sql);

        // 处理foreign_key外键关系
        if (!empty($this->foreign_key)){
            foreach($this->foreign_key as $k=>$v){
                if (empty($this->fields[$k]))continue;
                import('controller.'.$v);
                $obj = new $v();
                foreach($data as $kk=>$vv){
                    if (!$vv[$k])continue;
                    $data[$kk][$k] = $obj->find($vv[$k]);
                }
            }
        }

        return $data;
    }

    /**
     * 根据id更新数据
     * 更新数据
     */
    public function update($id){
        $arrd = $this->filter(null,$type='change');
        if ($arrd['error']){
            return $arrd;
        }
        $format = $this->db->update_format($arrd['brand']);
        $sql = "update {$this->table} set {$format} where {$this->id}={$id}";
        try{
            $row = $this->db->conn->exec($sql);
            return [
                'Code'=>$row,
                'data'=>$arrd['brand'],
                'msg'=>$row?'修改成功':'修改失败'
            ];
        }catch (\PDOException $e){
            die('插入失败'.$e->getMessage());
        }

        return $arrd;
    }

    /**
     * 插入数据
     */
    public function insert(){
        $arrd = $this->filter(null,'append');

        // 标识位，如果有错误则不能插入数据
        if ($arrd['error']){
            return $arrd;
        }
        $format = $this->db->insert_format($arrd['brand']);

        $sql = "insert into {$this->table} {$format}";

        try{

            $row = $this->db->conn->exec($sql);
            return [
                'Code'=>$row,
                'data'=>$arrd['brand'],
                'msg'=>'插入成功',
            ];
        }catch (\PDOException $e){
            die('插入失败'.$e->getMessage());
        }

//        echo json_encode($this->fields);

    }

    /**
     * @param null $data 如果data为空则直接取对应方法中的参数
     * @return array 过滤后的数据
     */
    public function filter($data=null,$type='append'){
        $arr = [];
        $flag = false;

        foreach($this->fields as $k=>$v){
            if ($v['extra'] == 'auto_increment')continue;
            $value = !empty($data[$k])?$data[$k]:call_user_func('_'.strtolower($_SERVER['REQUEST_METHOD']),$k);
            // 判断是否为空
            if (!$v['db_null'] && empty($value)){
                if ($type == 'append'){
                    $flag = true;
                    $arr[$k] = !empty($v['comment'])?$v['comment'].'不能为空':$k.'不能为空';
                }
                continue;
            };

            // 判断长度
            if (($v['data_type'] == 'varchar' || $v['data_type'] == 'char') && !empty($v['max_length']) && strlen($value) > $v['max_length']){
                if ($type == 'append'){
                    $flag = true;
                    $arr[$k] = !empty($v['comment'])?$v['comment']:$k;
                    $arr[$k].='最大长度是:'.$v['max_length'];
                }
                continue;
            }
            if ($type == 'change'){
                if (!$value)continue;
            }

            $arr[$k] = !empty($value)?$value:null;
        }

        return [
            'error'=>$flag,
            'brand'=>$arr,
        ];
    }

    /**
     * 删除数据
     */
    public function remove($id){
        try{
            // $sql = 'delete from '.$this->table.' where '.$this->id .'='.$id;
            $sql = "delete from {$this->table} where {$this->id} = {$id}";
            $row = $this->db->conn->exec($sql);
            return [
                'Code'=>$row,
                'msg'=>'删除成功',
            ];
        }catch (\PDOException $e){
            return [
                'Code'=>0,
                'msg'=>'删除数据失败'
            ];
        }
    }

    /**
     * 将Get参数拼接成sql的查询语句
     */

    public function where(){
        $where = [];
        foreach($this->fields as $k=>$v){
            $val = _get($k);
            if (isset($val)){
                $where [] =  ' '.$k.' like \'%'.$val.'%\'';
            }
        }
        $where = implode(' and ',$where);
        return $where;
    }




    /**
     * get请求
     * @param null $id
     * @return false|string
     */
    public function get($id=null){
        $data = $this->select($id);
        $response = [
            'Code'=>1,
            'data'=>$data
        ];
        return json($response);
    }

    /**
     * @return false|string
     */
    public function post(){
        $res = $this->insert();
        return json($res);
    }

    /**
     * 删除数据
     * @param $id
     * @return false|string
     */
    public function delete($id){
        if (!$id){
            return json([
                'Code'=>0,
                'msg'=>'GET请求请传入需要修改的主键,如:/user/1',
            ]);
        }

        $row = $this->find($id);
        if (!$row){
            return json([
                'Code'=>0,
                'msg'=>'数据不存在',
            ]);
        }

        $result = $this->remove($id);
        return json($result);
    }


    public function put($id){
        // 判断是否传入id
        if (!$id){
            return json([
                'Code'=>0,
                'msg'=>'PUT请求请传入需要修改的主键,如:/user/1',
            ]);
        }
        // 判断这行数据是否存在
        $row = $this->find($id);
        if (!$row){
            return json([
                'Code'=>0,
                'msg'=>'数据不存在',
            ]);
        }
        $data = $this->update($id);
        return json($data);
    }

}