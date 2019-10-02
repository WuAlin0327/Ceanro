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
    static $group_by;
    static $set;
    static $limit;
    static $parameter=[];

    private static $instance;

    /**
     * @param null $table
     * @return self DataBase
     */
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
     * @return array|int
     */
    public function exclude($sql,$format=[],$type='list[dict]'){
        $stm = self::$instance->conn->prepare($sql);
        $params = !empty($format)?$format:self::$parameter;
        $stm->execute($params);
        if (get_config('sql_debug'))
            $stm->debugDumpParams();
        self::instance()->reset();
        if (strpos($sql,'update') !== false || strpos($sql,'insert') !== false || strpos($sql,'delete') !== false){
            $stm->closeCursor();
            return $stm->rowCount();
        }
        $data = $stm->fetchAll(self::$instance->data_format[$type]);
        $stm->closeCursor();
        return $data;
    }


    public function get_fields($table=null){
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

        $fields = self::$instance->exclude($sql,[!empty($table)?$table:self::$instance->table]);
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


    ///////////////////// 下面的方法是对数据库进行链式操作的方法 ///////////////////////////
    /**
     * 准备表名
     * @param string $name 默认可以不用传前缀，也可以传表全名
     * @return self mixed
     */
    public function table($name){
        if (empty($name)){
            self::$table_name = self::instance()->table;
            return self::$instance;
        }
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
     * @param string|array $fields
     * @return self mixed
     */
    public function fields($fields){
        if (is_string($fields)){
            self::$fields = $fields;
        }elseif(is_array($fields)){
            self::$fields = implode(',',$fields);
        }
        return self::$instance;
    }

    /**
     * 准备where语句,一个链式查询中可以有多个where语句，如果传入的参数是数组的话那么默认多个数组之间的关系是and
     * 如果逻辑判断是 or 或者not 请传入字符串
     * @param string|array $where
     * @return self mixed
     */
    public function where($where,$params=array()){
        if (is_string($where)){
            self::$where .= $where;
            foreach($params as $k=>$v)
                self::$parameter[] = $v;

        }else{
            $where_sql = [];
            foreach($where as $field=>$value){
                // 如果$k中有问号 例如:['age > ?' => 20]
                if (strpos($field,'?') !== false){
//                    $where_sql[] = str_replace('?',is_string($v)?'\''.$v.'\'':$v,$k);
                    $where_sql[] = $field;
                    self::$parameter[] = $value;
                    continue;
                }

                // 如果$v是数组拼接成 $k in 数组中
                if (is_array($value)){
                    foreach($value as $kk=>$vv){
                        self::$parameter[] = $vv;
                    }
                    $prepare = rtrim( str_pad('?', 2 * count($value), ',?') , ',');
                    $where_sql[] = ('`'.$field.'`').' in ('.$prepare.')';
                    //self::$parameter[] = $v;
                }elseif(is_string($value) || is_int($value)){
                    // 防止sql注入
                    self::$parameter[] = $value;
                    $where_sql[] = ('`'.$field.'`').' = ?';
                }
            }
            self::$where .= implode(' and ',$where_sql);
        }

        return self::$instance;
    }

    /**
     * 准备order by排序字段
     * @param string|array $order
     * @return self mixed
     */
    public function order($order){
        self::$orders = ' order by ';
        if (is_string($order)){
            self::$orders .= $order;
        }else{
            self::$orders .= implode(',',$order);
        }

        return self::$instance;
    }


    /**
     * @param string $limit 传入limit的时候请传入该格式:1,2
     * @return self mixed
     */
    public function limit($limit){
        self::$limit = ' limit '.$limit;
        return self::$instance;
    }

    /**
     * 执行完sql语句之后需要将这些数据清空以待下一次查询时使用
     */
    public function reset(){
        self::$where = null;
        self::$orders = null;
        self::$table_name = null;
        self::$fields = null;
        self::$group_by = null;
        self::$parameter=[];
        self::$set = null;
        self::$limit = null;
    }

    /**
     * 取出查询到的所有数据
     * @param string $type
     * @return array
     */
    public function selectAll($type='list[dict]'){
        $where = !empty(self::$where)?' where '.self::$where:'';
        $fields = !empty(self::$fields)?self::$fields:'*';
        $order = !empty(self::$orders)?self::$orders:'';
        $limit  = !empty(self::$limit)?self::$limit:'';
        $sql = 'select '.$fields.' from '.self::$table_name.$where.$order.$limit;

        $resuful = self::instance()->exclude($sql,self::$parameter,$type);
        return $resuful;
    }

    /**
     * 取查询到的第一条数据
     * @return array mixed|null
     */
    public function select(){
        $resuful = self::instance()->selectAll();
        return !empty($resuful[0])?$resuful[0]:null;
    }


    /**
     * insert into 表名 (字段1,字段2,字段3,字段4) value(值1,值2,值3,值4);
     * @param array $params
     * @return self mixed
     */
    public function value($params){
        $set = [];
        $flag = true; // 如果是字符串..
        foreach($params as $k=>$v){
            if(is_array($v)){
                $flag = false;
                foreach($v as $kk=>$vv){
                    $set['`'.$k.'`'][$kk] = $v;
                    self::$parameter[] = $vv;
                }
            }else{
                self::$parameter[] = $v;
            }
        }
        if ($flag){
            // 插入单行
             self::$set = '('.rtrim( str_pad('?', 2 * count($params), ',?') , ',').')';
        }else{
            // 插入多行
            foreach($set as $k=>$v){
                self::$set .= '('.rtrim( str_pad('?', 2 * count($v), ',?') , ',').'),';
            }
            self::$set = substr(self::$set ,0,strlen(self::$set)-1);;
        }

        return self::$instance;
    }

    /**
     * @return array|int
     */
    public function insert(){
        $fields = !empty(self::$fields)?'('.self::$fields.')':'';
        $sql = 'insert into '.self::$table_name.$fields.' value '.self::$set;
        $rowCount = self::instance()->exclude($sql,self::$parameter);
        return $rowCount;
    }


    /**
     * @param array $params
     * @return self mixed
     */
    public function update_set($params){
        $arrd = [];
        foreach($params as $k => $v){
            $arrd[] = $k.' = ?';
            self::$parameter[] = $v;
        }
        self::$set = implode(',',$arrd);

        return self::$instance;
    }


    /**
     * 更新数据
     * @return int
     */
    public function update(){
        $where = !empty(self::$where)?' where '.self::$where:'';
        $sql = 'update '.self::$table_name.' set '.self::$set.$where;

        $rowCount = self::instance()->exclude($sql);
        return $rowCount;
    }


    /**
     * 删除数据
     * @return array|int
     */
    public function delete(){
        $where = !empty(self::$where)?' where '.self::$where:'';
        $sql = 'delete from '.self::$table_name.$where;
        $rowCount = self::instance()->exclude($sql,self::$parameter);
        return $rowCount;
    }

}