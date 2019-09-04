<?php


namespace core;


class DataBase
{
    public $data_format = [
        'list[object]'=>5,
        'list[list]'=>3,
        'list[dict]'=>2,
        'row'=>'',
        'all'=>4,
    ];
    public $conn;
    public $table;

    static $where;
    static $orders;
    static $table_name;
    static $fields;

    private static $instance;

    public static function instance($table=null)
    {
        if (self::$instance == null) {
            self::$instance = new self();
            self::$instance->table = $table;
            $config = get_config('sql');
            $dsn = "{$config['dbms']}:host={$config['host']};dbname={$config['dbname']}";
            self::$instance->conn = new \PDO($dsn,$config['user'],$config['password']);
            self::$instance->conn->exec('set names utf8');
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * @param string $sql SQL语句
     * @param null $type 返回数据类型
     * @return array
     */
    public function exclude($sql,$format=[],$type='list[dict]'){
        $stm = self::$instance->conn->prepare($sql);
        $stm->execute($format);
        $data = $stm->fetchAll(self::$instance->data_format[$type]);
        return $data;
    }


    public function get_fields(){
        $sql = '
        select 
            column_name as db_fields ,
            is_nullable as db_null,
            data_type,                                                                                              
            character_maximum_length as max_length,
            extra,
            column_comment as comment  
        from information_schema.columns 
        where table_name = ?
        ';

        $fields = self::$instance->exclude($sql,[self::$instance->table]);
        $ret = [];
        foreach($fields as $k=>$v){
            $ret[$v['db_fields']] = $v;
            unset($ret[$v['db_fields']]['db_fields']);
            $ret[$v['db_fields']]['db_null'] = $ret[$v['db_fields']]['db_null'] == 'NO'?false:true;
        }
        return $ret;
    }

    /**
     *
     * $arr 清洗后需要插入到数据库中的数据
     */
    public function insert_format($arr){
        $into = [];
        $value = [];
        foreach($arr as $k=>$v){
            if (empty($v)){
                continue;
            }
            $into[] = $k;
            $value[] ='\''.$v.'\'';
        }
        $sql = '('.implode(',',$into).') VALUES ('.implode(',',$value).')';
        return $sql;

    }


    /**
     * @param $arr
     * @return mixed
     *  update user set username=?,password=? where id = x
     */
    public function update_format($arr){
        $update = [];
        foreach($arr as $k=>$v){
            $update[] = $k.'=\''.$v.'\'';
        }
        return implode(',',$update);
    }

    /**
     * 准备表名
     * @param string $name 默认可以不用传前缀，也可以传表全名
     * @return mixed
     */
    public function table($name){
        $db_prefix = get_config('db_prefix');
        $table = null;
        if (strpos($name,$db_prefix) !== false)
            $table = $name;
        else
            $table = $db_prefix.$name;

        self::$table_name = $table;
        return self::$instance;
    }

    /**
     * 准备where语句
     * @param $where
     * @return mixed
     */
    public function where($where){
        if (is_string($where)){
            self::$where = $where;
        }else{
            $where_sql = [];
            foreach($where as $k=>$v){
                // 如果$k中有问号 例如:['age > ?' => 20]，则将?替换成v
                if (strpos($k,'?') !== false){
                    $where_sql[] = str_replace('?',is_string($v)?'\''.$v.'\'':$v,$k);
                    continue;
                }

                // 如果$v是数组拼接成 $k in 数组中
                if (is_array($v)){
                    $in = [];
                    foreach($v as $kk=>$vv){
                        if (is_string($vv))
                            $in[] = '\''.$vv.'\'';
                        else
                            $in[] = $vv;
                    }
                    $where_sql[] = $k.' in ('.implode(',',$in).')';
                }elseif(is_string($v)){
                    $where_sql[] = $k.' = \''.$v.'\'';
                }
            }
            self::$where = implode(' and ',$where_sql);
        }

        return self::$instance;
    }

    public function fields($fields){
        if (is_string($fields)){
            self::$fields = $fields;
        }elseif(is_array($fields)){
            self::$fields = implode(',',$fields);
        }

        return self::$instance;
    }

    public function selectAll(){
        $where = !empty(self::$where)?' where '.self::$where:'';
        $sql = 'select '.self::$fields.' from '.self::$table_name.$where;
        $resuful = self::$instance->exclude($sql);
        echo json_encode($resuful);
        echo $sql;
    }

}