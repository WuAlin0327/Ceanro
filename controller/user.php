<?php

class user extends \core\Core
{

    public $id = 'id';
    public $table = __CLASS__; //




    public function delete(){
        $this->remove();
        echo '这个是delete请求';
    }

    public function put(){
        $this->update();
        echo '这个是put请求';
    }

    public function func(){
        echo '这是一个func函数';
    }

    public function replace($id){
        $callback = isset($_GET['callback'])?$_GET['callback']:'';
        $data = $this->find($id);
        return $this->json($data,$callback,'201');
    }
}