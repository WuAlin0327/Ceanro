<?php


class user extends \core\Core
{

    public $id = 'id';
    public $table = __CLASS__; //
    public $foreign_key = [
        'book_id'=>'book'
    ];


    public function replace($id){
        $callback = isset($_GET['callback'])?$_GET['callback']:'';
////        $data = $this->find($id);
//
//
//        /**
//         * 链式查询的两种方式
//         * 链中方式结果一样,不过第一种方式更加简便
//         */
//        // sql查询链式操作，方式1
        $data = table('user')
            ->fields('*') // 要取的字段
            ->where('addr is not null') // where 过滤
            ->order('id desc') // 排序
            ->limit('1,2') // limit 第一个参数是从什么时候开始
            ->selectAll();
//        // sql查询链式操作，方式2
//        $data2 = \core\DataBase::instance()->table('user')->fields('*')->where(['username'=>'wualin'])->where(' or id = ?',[30])->selectAll();

//        $data = $this->find($id);
//        if (!$data){
//            $data = '未查询到数据';
//        }

        return json($data,$callback,'201');
    }

    // 测试插入数据链式操作
    public function add(){

        // 插入多行
        $params = [
            [
                'username'=>'alex',
                'password'=>'123321x',
                'addr'=>'江西南昌',
                'phone'=>'321'
            ],
            [
                'username'=>'wualin1',
                'password'=>'123321xp k-',
                'addr'=>'江西南昌',
                'phone'=>'321'
            ]
        ];
        $res = table('user')->fields(['username','password','addr','phone'])->insert_set($params)->insert();
        return json($res);
    }

    public function change(){
        $params = [
            'username'=>'wualin1',
            'password'=>'312',
            'addr'=>'江西九江',
            'phone'=>'321',
            'book_id'=>'3'
        ];
        $res = table('user')->update_set($params)->where(['id'=>34])->update();
        return json(['changeCount'=>$res]);
    }

    public function rm(){
        $rowCount = table('user')->where(['id'=>52])->delete();
        return json($rowCount?'删除成功':'删除失败');
    }
}