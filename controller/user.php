<?php

class user extends \core\Core
{

    public $id = 'id';
    public $table = __CLASS__; //

    public function get($id=null){
        // get业务逻辑
    }
    public function post(){
        // post业务逻辑
    }

    public function put($id)
    {
        // put业务逻辑
    }

    public function delete($id)
    {
        // delete业务逻辑
    }

    public function replace($id){
        $callback = isset($_GET['callback'])?$_GET['callback']:'';
        $data = $this->find($id);
        return $this->json($data,$callback,'201');
    }
}