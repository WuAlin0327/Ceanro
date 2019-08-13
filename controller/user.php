<?php


class user extends \core\Core\Core
{

    public $id = 'id';
    public $table = 'user';

    static $user_list = [
        ['name'=>'alex','age'=>'20','addr'=>'江西新余'],
        ['name'=>'dalao','age'=>'22','addr'=>'江西南昌']
    ];

    public function get(){
        echo '11';
        return '<h1>轻量级web后端框架</h1><br><h3>轻量级遵循RESTful API规范的框架</h3>';
    }

    public function post(){
        echo '这个是post请求';
    }

    public function delete(){
        echo '这个是delete请求';
    }

    public function put(){
        echo '这个是put请求';
    }

    public function func(){
        echo '这是一个func函数';
    }

    public function replace($id){
        $callback = isset($_GET['callback'])?$_GET['callback']:'';
        $data = $this->find($id,'object');
        return parent::json($data,$callback,'201');
    }
}