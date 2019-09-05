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
//        $data = $this->find($id);


        /**
         * 链式查询的两种方式
         * 链中方式结果一样,不过第一种方式更加简便
         */
        // sql查询链式操作，方式1
        $data = table('user')->fields('*')->where(['username'=>'wualin'])->where(' or id = ?',[30])->select();
        // sql查询链式操作，方式2
        $data2 = \core\DataBase::instance()->table('user')->fields('*')->where(['username'=>'wualin'])->where(' or id = ?',[30])->selectAll();


        return json(['data1'=>$data,'data2'=>$data2],$callback,'201');
    }
}