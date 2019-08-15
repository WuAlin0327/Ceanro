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
    public function __construct($table)
    {
        $this->table = $table;
        $config = get_config('sql');
        $dsn = "{$config['dbms']}:host={$config['host']};dbname={$config['dbname']}";
        $this->conn = new \PDO($dsn,$config['user'],$config['password']);
        $this->conn->exec('set names utf8');

    }

    /**
     * @param $sql SQL语句
     * @param null $type 返回数据类型
     * @return array
     */
    public function exclude($sql,$format=[],$type='list[dict]'){

        $stm = $this->conn->prepare($sql);
        $stm->execute($format);
        $data = $stm->fetchAll($this->data_format[$type]);
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

        $fields = $this->exclude($sql,[$this->table]);
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
            $into[] = $k;
            $value[] ='\''.$v.'\'';
        }
        $sql = '('.implode(',',$into).') VALUES ('.implode(',',$value).')';
        return $sql;

    }
}